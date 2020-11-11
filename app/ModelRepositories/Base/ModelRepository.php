<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Configuration;
use App\Exceptions\AppException;
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
    private $withTrashed = false;
    private $lock;
    private $strict = true;
    private $more = false;
    private $mores = [];
    private $sorts = [];
    private $sortsDefault = [];
    private $sortsAllowed = [];
    private $sortsAllowedDefault = [];
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

    public function lockForUpdate()
    {
        return $this->lock(true);
    }

    public function sharedLock()
    {
        return $this->lock(false);
    }

    public function beenMore()
    {
        return $this->more;
    }

    public function more($moreBy = null, $moreOrder = 'asc', $morePivot = null)
    {
        if ($moreBy) {
            $this->mores[$moreBy] = [
                'order' => $moreOrder,
                'pivot' => $morePivot,
            ];
        }
        return $this;
    }

    public function sorts($sorts = [])
    {
        $this->sorts = $sorts;
        return $this;
    }

    public function sortsAllowed($sortsAllowed = [])
    {
        $this->sortsAllowed = $sortsAllowed;
        return $this;
    }

    public function sort($sortBy = null, $sortOrder = 'asc')
    {
        if ($sortBy) {
            $this->sorts[$sortBy] = $sortOrder;
        }
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
        $this->more = false;
        if (!empty($this->mores)) {
            $query->where(function ($query) {
                foreach ($this->mores as $moreBy => $more) {
                    if ($more['pivot']) {
                        $query->where($moreBy, $more['order'] == 'asc' ? '>' : '<', $more['pivot']);
                    }
                }
            });
            foreach ($this->mores as $moreBy => $more) {
                $query->orderBy($moreBy, $more['order']);
            }
            $this->mores = [];
        }
        if (!empty($this->sorts)) {
            $sortsAllowed = array_merge($this->sortsAllowed, $this->sortsAllowedDefault);
            $noNeedToCheckSortsAllowed = empty($sortsAllowed);
            foreach (array_merge($this->sorts, $this->sortsDefault) as $sortBy => $sortOrder) {
                if ($noNeedToCheckSortsAllowed || in_array($sortBy, $sortsAllowed)) {
                    $query->orderBy($sortBy, $sortOrder ? $sortOrder : 'asc');
                }
            }
            $this->sorts()->sortsAllowed();
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
     * @return Model|mixed
     * @throws
     */
    public function first($query)
    {
        $model = $this->catch(function () use ($query) {
            return $this->strict ? $query->firstOrFail() : $query->first();
        });
        $this->strict = true;
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
     * @throws
     */
    public function model($id = null)
    {
        if (!is_null($id)) {
            if ($id instanceof Model) {
                if (get_class($id) != $this->modelClass) {
                    throw new AppException('Model does not match the class');
                }
                $this->model = $id;
            } else {
                $this->model = $this->getById($id);
            }
        }
        return $this->model;
    }

    public function withModel($id = null)
    {
        $this->model($id);
        return $this;
    }

    public function doesntHaveModel()
    {
        return empty($this->model);
    }

    public function hasModel()
    {
        return !$this->doesntHaveModel();
    }

    public function forgetModel()
    {
        $this->model = null;
        return $this;
    }

    public function load($relations)
    {
        if ($this->hasModel()) {
            $this->model->load($relations);
        }
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
     * @param Model|int|mixed $id
     * @return mixed
     */
    public function retrieveId($id)
    {
        return $id instanceof Model ? $id->getKey() : $id;
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
     * @return Collection
     * @throws Exception
     */
    public function getAll()
    {
        return $this->catch(function () {
            return $this->search([], Configuration::FETCH_PAGING_NO, 0);
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
     * @return Builder
     */
    protected function searchQuery()
    {
        return $this->query();
    }

    /**
     * @param array $search
     * @param int $paging
     * @param int $itemsPerPage
     * @return Collection|LengthAwarePaginator|Builder
     * @throws Exception
     */
    public function search(array $search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE)
    {
        $query = $this->searchQuery();

        if (!empty($search)) {
            $query = $this->searchOn($query, $search);
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
            case Configuration::FETCH_PAGING_MORE:
                return $this->catch(function () use ($query, $itemsPerPage) {
                    $models = $query->limit($itemsPerPage + 1)->get();
                    if ($models->count() > $itemsPerPage) {
                        $this->more = true;
                        $models->pop();
                    }
                    return $models;
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
