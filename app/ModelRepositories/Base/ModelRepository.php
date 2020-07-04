<?php

namespace App\ModelRepositories\Base;

use App\Configuration;
use App\Exceptions\DatabaseException;
use App\Exceptions\Exception;
use App\Utils\AbortTrait;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PDOException;

abstract class ModelRepository
{
    use ClassTrait, AbortTrait;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var Model|mixed
     */
    protected $model;

    private $with;
    private $withTrashed = true;
    private $lock;
    private $strict;
    private $force = false;
    private $pinned = false;

    /**
     * @var array|null
     */
    protected $batch;

    public function __construct($id = null)
    {
        $this->modelClass = $this->modelClass();
        $this->model($id);
    }

    public abstract function modelClass();

    /**
     * @return Builder
     */
    public function rawQuery()
    {
        return call_user_func($this->modelClass . '::query');
    }

    public function withTrashed()
    {
        $this->withTrashed = true;
        return $this;
    }

    public function with($with)
    {
        $this->with = $with;
        return $this;
    }

    public function lock($lock)
    {
        $this->lock = $lock;
        return $this;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        $query = $this->rawQuery();
        if (!is_null($this->with)) {
            $query->with($this->with);
            $this->with = null;
        }
        if ($this->withTrashed) {
            $query->withTrashed();
            $this->withTrashed = false;
        }
        if (!is_null($this->lock)) {
            $query->lock($this->lock);
            $this->lock = null;
        }
        return $query;
    }

    public function notStrict()
    {
        $this->strict = false;
        return $this;
    }

    public function pinModel()
    {
        $this->pinned = true;
        return $this;
    }

    /**
     * @param Builder $query
     * @return Model
     * @throws
     */
    public function first($query)
    {
        $model = $this->catch(function () use ($query) {
            return $this->strict ? $query->firstOrFail() : $query->first();
        });
        $this->strict = false;
        if ($this->pinned) {
            $this->pinned = false;
            $this->model = $model;
        }
        return $model;
    }

    /**
     * @return Model|mixed
     */
    public function newModel()
    {
        $modelClass = $this->modelClass;
        $this->model = new $modelClass();
        return $this->model;
    }

    /**
     * @param Model|mixed|null $id
     * @return Model|mixed|null
     */
    public function model($id = null)
    {
        if (!empty($id)) {
            $this->model = $id instanceof Model ? $id : $this->getById($id);
        }
        return $this->model;
    }

    public function doesntHaveModel()
    {
        return empty($this->model);
    }

    public function forgetModel()
    {
        $this->model = null;
        return $this;
    }

    public function getIdKey()
    {
        return $this->newModel()->getKeyName();
    }

    public function getId()
    {
        return empty($this->model) ? null : $this->model->getKey();
    }

    /**
     * @param callable $callback
     * @param callable $catchCallback
     * @return mixed
     * @throws Exception
     */
    protected function catch(callable $callback, callable $catchCallback = null)
    {
        try {
            return $callback();
        } catch (PDOException $exception) {
            if ($catchCallback) {
                return $catchCallback(DatabaseException::from($exception));
            } else {
                throw DatabaseException::from($exception);
            }
        }
    }

    public function getById($id, callable $callback = null)
    {
        if (empty($callback)) {
            return $this->first($this->queryById($id));
        }
        return $this->catch(function () use ($id, $callback) {
            return $callback($this->queryById($id));
        });
    }

    public function queryById($id)
    {
        return $this->query()->where($this->getIdKey(), $id);
    }

    /**
     * @param array $ids
     * @param callable|null $callback
     * @return Collection
     * @throws Exception
     */
    public function getByIds(array $ids, callable $callback = null)
    {
        return $this->catch(function () use ($ids, $callback) {
            return empty($callback) ? $this->queryByIds($ids)->get() : $callback($this->queryByIds($ids));
        });

    }

    public function queryByIds(array $ids)
    {
        return $this->query()->whereIn($this->getIdKey(), $ids);
    }

    /**
     * @param string $sortBy
     * @param string $sortOrder
     * @return Collection
     * @throws Exception
     */
    public function getAll($sortBy = null, $sortOrder = 'asc')
    {
        return $this->catch(function () use ($sortBy, $sortOrder) {
            return $this->search([], Configuration::FETCH_PAGING_NO, 0, $sortBy, $sortOrder);
        });
    }

    /**
     * @param Builder $query
     * @param array $search
     * @return Builder
     */
    protected function searchOn($query, array $search)
    {
        return $query;
    }

    /**
     * @param array $search
     * @param int $paging
     * @param int $itemsPerPage
     * @param string|null $sortBy
     * @param string|null $sortOrder
     * @return Collection|LengthAwarePaginator|Builder
     * @throws Exception
     */
    public function search(array $search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = 'asc')
    {
        $query = $this->query();

        if (!empty($search)) {
            $query = $this->searchOn($query, $search);
        }

        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        switch ($paging) {
            case Configuration::FETCH_PAGING_NO:
                return $this->catch(function () use ($query) {
                    return $query->get();
                });
            case Configuration::FETCH_PAGING_YES:
                return $this->catch(function () use ($query, $itemsPerPage) {
                    return $query->paginate($itemsPerPage);
                });
            default:
                return $query;
        }
    }

    /**
     * @param array $attributes
     * @return Model|mixed
     * @throws Exception
     */
    public function createWithAttributes(array $attributes = [])
    {
        return $this->catch(function () use ($attributes) {
            $this->model = $this->rawQuery()->create($attributes);
            return $this->model;
        });
    }

    /**
     * @param array $attributes
     * @return Model|mixed
     * @throws Exception
     */
    public function updateWithAttributes(array $attributes = [])
    {
        return $this->catch(function () use ($attributes) {
            if (!empty($attributes)) {
                $this->model->update($attributes);
            }
            return $this->model;
        });
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return Model|mixed
     * @throws
     */
    public function updateOrCreateWithAttributes(array $attributes = [], array $values = [])
    {
        return $this->catch(function () use ($attributes, $values) {
            if (!empty($attributes)) {
                $this->model = $this->query()->updateOrCreate($attributes, $values);
            }
            return $this->model;
        });
    }

    public function force()
    {
        $this->force = true;
        return $this;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteWithIds(array $ids)
    {
        return $this->queryDelete($this->queryByIds($ids));
    }

    /**
     * @param Builder $query
     * @return bool
     * @throws
     */
    protected function queryDelete($query)
    {
        return $this->catch(function () use ($query) {
            if ($this->force) {
                $this->force = false;
                $query->forceDelete();
            } else {
                $query->delete();
            }
            return true;
        });
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->queryDelete($this->model);
    }

    public function restore()
    {
        if ($this->model && $this->model->trashed()) {
            return $this->catch(function () {
                $this->model->restore();
                return $this->model;
            });
        }
        return $this->model;
    }

    public function batchInsertStart($batch = 1000, $ignored = false)
    {
        $this->newModel();
        $this->batch = [
            'type' => 'insert',
            'values' => [],
            'batch' => $batch,
            'ignored' => $ignored,
            'inserted' => false,
            'run' => 0,
        ];
        return $this;
    }

    protected function batchInsertReset()
    {
        $this->batch['run'] = 0;
        $this->batch['values'] = [];
    }

    public function batchInsert($attributes)
    {
        $this->batchInsertAdd($attributes);
        $this->batchInsertTryToSave();
        return $this;
    }

    public function batchInserted()
    {
        return $this->batch['inserted'];
    }

    protected function batchInsertAdd($attributes)
    {
        if ($this->model->timestamps) {
            $now = DateTimer::syncNow();
            $attributes['created_at'] = $now;
            $attributes['updated_at'] = $now;
        }
        $this->batch['values'][] = $attributes;
    }

    protected function batchInsertTryToSave()
    {
        if (++$this->batch['run'] == $this->batch['batch']) {
            $this->batchInsertSave();
            $this->batchInsertReset();

            $this->batch['inserted'] = true;
        } else {
            $this->batch['inserted'] = false;
        }
    }

    protected function batchInsertSave()
    {
        if (count($this->batch['values']) > 0) {
            $this->catch(function () {
                if ($this->batch['ignored']) {
                    $this->rawQuery()->insertOrIgnore($this->batch['values']);
                } else {
                    $this->rawQuery()->insert($this->batch['values']);
                }
            });
        }
    }

    public function batchInsertEnd()
    {
        $this->batchInsertSave();
        $this->model = null;
        $this->batch = null;
    }

    public function batchReadStart($query, $batch = 1000)
    {
        $this->batch = [
            'type' => 'read',
            'query' => $query,
            'batch' => $batch,
            'run' => 0,
        ];
        return $this;
    }

    /**
     * @param int $length
     * @param bool $shouldEnd
     * @return Collection
     * @throws Exception
     */
    public function batchRead(&$length, &$shouldEnd)
    {
        $collection = $this->catch(function () {
            return $this->batch['query']->skip((++$this->batch['run'] - 1) * $this->batch['batch'])->take($this->batch['batch'])->get();
        });
        $length = $collection->count();
        $shouldEnd = $length < $this->batch['batch'];
        return $collection;
    }

    public function batchReadEnd()
    {
        $this->batch = null;
    }
}
