<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Configuration;
use App\Exceptions\AppException;
use App\Exceptions\DatabaseException;
use App\Models\Base\IFromModel;
use App\Models\Base\IModel;
use App\Utils\AbortTrait;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\DateTimer;
use App\Vendors\Illuminate\Support\Str;
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

    protected $modelByUnique = false;

    private $with;

    private $withTrashed = false;

    private $onlyTrashed = false;

    private $selects = [];

    private $lock;

    private $strict = true;

    private $more = false;

    private $mores = [];

    private $sorts = [];

    private $sortsDefault = [];

    private $sortsAllowed = [];

    private $sortsAllowedDefault = [];

    private $limitTake = 0;

    private $limitSkip = 0;

    private $force = false;

    private $pinned = false;

    private $rawQuery = null;

    private $fixedRawQuery = null;

    /**
     * @var array
     */
    protected $batch = [];

    public function __construct($id = null)
    {
        $this->modelClass = $this->modelClass();
        $this->model($id);
    }

    public abstract function modelClass();

    /**
     * @param bool $pinned
     * @return Model|IFromModel|IModel|mixed
     */
    public function newModel($pinned = true)
    {
        $modelClass = $this->modelClass;
        if ($pinned) {
            $this->model = new $modelClass();
            return $this->model;
        }
        return new $modelClass();
    }

    public function getTable()
    {
        return $this->newModel(false)->getTable();
    }

    public function setModelByUnique($modelByUnique = true)
    {
        $this->modelByUnique = $modelByUnique;
        return $this;
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
                $modelClass = get_class($id);
                $matchedClass = $modelClass == $this->modelClass;
                if (!$matchedClass && !is_subclass_of($this->modelClass, $modelClass)) {
                    throw new AppException('Model does not match the class');
                }
                $this->model = $matchedClass ? $id : $this->newModel()->fromModel($id);
            }
            else {
                $this->model = $this->modelByUnique ? $this->getUniquely($id) : $this->getById($id);
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
        return $this->newModel(false)->getKeyName();
    }

    public function getId()
    {
        return empty($this->model) ? null : $this->model->getKey();
    }

    /**
     * @param Model|int|string|mixed $id
     * @return int|string|mixed
     */
    public function retrieveId($id)
    {
        return $id instanceof Model ? $id->getKey() : $id;
    }

    /**
     * @param Collection|Model[]|int[]|string[]|array $ids
     * @return int[]|string[]|array
     */
    public function retrieveIds($ids)
    {
        return $ids instanceof Collection ? $ids->map(function (Model $model) {
            return $model->getKey();
        })->all() : collect($ids)->map(function ($id) {
            return $id instanceof Model ? $id->getKey() : $id;
        })->all();
    }

    /**
     * @return Builder
     */
    public function rawQuery()
    {
        if ($this->rawQuery) {
            $rawQuery = $this->rawQuery;
            $this->rawQuery = null;
            return $rawQuery;
        }
        if ($this->fixedRawQuery) {
            return $this->fixedRawQuery;
        }
        return call_user_func($this->modelClass . '::query');
    }

    /**
     * @param Model|callable|null $model
     * @param callable|null $callback
     * @return Builder
     * @throws
     */
    public function modelQuery($model = null, $callback = null)
    {
        if (is_null($model)) {
            $model = $this->newModel(false);
        }
        elseif (is_callable($model)) {
            $model = $model($this->newModel(false));
        }
        if (is_callable($callback)) {
            $model = $callback($model);
        }
        if (get_class($model) != $this->modelClass) {
            throw new AppException('Model does not match the class');
        }
        return $model->newQuery();
    }

    /**
     * @param Model|callable|null $model
     * @param callable|null $callback
     * @return static
     * @throws
     */
    public function useModelQuery($model = null, $callback = null)
    {
        $this->rawQuery = $this->modelQuery($model, $callback);
        return $this;
    }

    /**
     * @param Model|callable|null $model
     * @param callable|null $callback
     * @return static
     * @throws
     */
    public function useModelQueryAsFixed($model = null, $callback = null)
    {
        $this->fixedRawQuery = $this->modelQuery($model, $callback);
        return $this;
    }

    /**
     * @return static
     * @throws
     */
    public function clearUsingModelQuery()
    {
        $this->rawQuery = null;
        $this->fixedRawQuery = null;
        return $this;
    }

    public function withTrashed()
    {
        $this->withTrashed = true;
        return $this;
    }

    public function onlyTrashed()
    {
        $this->onlyTrashed = true;
        return $this;
    }

    public function with($with)
    {
        $this->with = $with;
        return $this;
    }

    /**
     * @param string|array $selects
     * @return static
     */
    public function select($selects)
    {
        $this->selects = (array)$selects;
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
            $this->sorts[] = ['by' => $sortBy, 'order' => $sortOrder];
        }
        return $this;
    }

    public function limit(int $take, int $skip = 0)
    {
        $this->limitTake = $take;
        $this->limitSkip = $skip;
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
        if (!empty($this->selects)) {
            $query->select($this->selects);
            $this->selects = [];
        }
        if ($this->withTrashed) {
            $query->withTrashed();
            $this->withTrashed = false;
        }
        if ($this->onlyTrashed) {
            $query->onlyTrashed();
            $this->onlyTrashed = false;
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
            foreach (array_merge($this->sorts, $this->sortsDefault) as $sort) {
                if ($noNeedToCheckSortsAllowed || in_array($sort['by'], $sortsAllowed)) {
                    $query->orderBy($sort['by'], $sort['order'] ?? 'asc');
                }
            }
            $this->sorts()->sortsAllowed();
        }
        if ($this->limitTake > 0) {
            if ($this->limitSkip > 0) {
                $query->skip($this->limitSkip);
            }
            $query->take($this->limitTake);
            $this->limit(0);
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
     * @return Model|mixed|null
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
     * @param callable $callback
     * @param callable|null $catchCallback
     * @return Model|Collection|bool|mixed|null|void
     * @throws
     */
    protected function catch(callable $callback, callable $catchCallback = null)
    {
        try {
            return $callback();
        }
        catch (PDOException $exception) {
            if ($catchCallback) {
                return $catchCallback(DatabaseException::from($exception));
            }
            else {
                throw DatabaseException::from($exception);
            }
        }
    }

    protected function getLockTableQuery($lock, $connection = null)
    {
        return null;
    }

    /**
     * @param array $options
     * @return static
     */
    public function lockTable($options = [])
    {
        $this->newModel(false)->lockTable($options);
        return $this;
    }

    public function unlockTable()
    {
        $this->newModel(false)->unlockTable();
        return $this;
    }

    public function queryById($id)
    {
        return $this->query()->where($this->getIdKey(), $id);
    }

    /**
     * @param mixed $id
     * @param callable|null $callback
     * @return Model|mixed|null
     * @throws
     */
    public function getById($id, callable $callback = null)
    {
        if (empty($callback)) {
            return $this->first($this->queryById($id));
        }
        return $this->catch(function () use ($id, $callback) {
            return $callback($this->queryById($id));
        });
    }

    public function queryByIds(array $ids)
    {
        return $this->query()->whereIn($this->getIdKey(), $ids);
    }

    /**
     * @param array $ids
     * @param callable|null $callback
     * @return Collection
     * @throws
     */
    public function getByIds(array $ids, callable $callback = null)
    {
        return $this->catch(function () use ($ids, $callback) {
            return empty($callback) ? $this->queryByIds($ids)->get() : $callback($this->queryByIds($ids));
        });
    }

    /**
     * @return string[]|array
     */
    protected function getUniqueKeys()
    {
        return [
            $this->getIdKey(),
        ];
    }

    /**
     * @param Builder $query
     * @param string|mixed $unique
     * @return Builder
     */
    public function queryUniquely($query, $unique)
    {
        foreach ($this->getUniqueKeys() as $uniqueKey) {
            $query->orWhere($uniqueKey, $unique);
        }
        return $query;
    }

    /**
     * @param string|mixed $unique
     * @return Model|mixed|null
     * @throws
     */
    public function getUniquely($unique)
    {
        return $this->first(
            $this->query()->where(function ($query) use ($unique) {
                return $this->queryUniquely($query, $unique);
            })
        );
    }

    /**
     * @return array
     */
    protected function getGeneratedUniqueKeys()
    {
        return [];
    }

    /**
     * @param string $uniqueKey
     * @return int|callable
     */
    protected function generateUniqueCallback(string $uniqueKey)
    {
        $generatedUniqueKeys = $this->getGeneratedUniqueKeys();
        return $generatedUniqueKeys[$uniqueKey] ?? 32;
    }

    /**
     * @param string $uniqueKey
     * @param int|callable|null $generateCallback
     * @param array|callable|null $ignores
     * @return string
     */
    protected function generateUniqueValue(string $uniqueKey, $generateCallback = null, $ignores = null)
    {
        if (is_null($generateCallback)) {
            if (method_exists($this, $method = 'generate' . Str::studly($uniqueKey))) {
                $generateCallback = function () use ($method) {
                    return $this->{$method}();
                };
            }
            else {
                $generateCallback = $this->generateUniqueCallback($uniqueKey);
            }
        }
        if (is_int($generateCallback)) {
            $length = $generateCallback;
            $generateCallback = function () use ($length) {
                return Str::random($length);
            };
        }

        while (($uniqueValue = $generateCallback()) && $this->notStrict()->getByUnique($uniqueKey, $uniqueValue, $ignores)) {
        }
        return $uniqueValue;
    }

    /**
     * @param string $uniqueKey
     * @param string $uniqueValue
     * @param array|callable|null $ignores
     * @return Model|null
     */
    public function getByUnique(string $uniqueKey, string $uniqueValue, $ignores = null)
    {
        $query = $this->query()->where($uniqueKey, $uniqueValue);
        if (is_array($ignores)) {
            foreach ($ignores as $ignoreKey => $ignoreValue) {
                $query->where($ignoreKey, '<>', $ignoreValue);
            }
        }
        elseif (is_callable($ignores)) {
            $query = $ignores($query);
        }
        return $this->first($query);
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
    public function searchQuery()
    {
        return $this->query();
    }

    /**
     * @param array $search
     * @param int $paging
     * @param int $itemsPerPage
     * @return Collection|LengthAwarePaginator|Builder|int
     * @throws
     */
    public function search(array $search = [], int $paging = Configuration::FETCH_PAGING_YES, int $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE)
    {
        $query = $this->searchOn($this->searchQuery(), $search);

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
            case Configuration::FETCH_COUNT:
                return $this->catch(function () use ($query) {
                    return $query->count();
                });
            default:
                return $query;
        }
    }

    /**
     * @param array $search
     * @return Builder
     */
    public function where(array $search = [])
    {
        return $this->search($search, Configuration::FETCH_QUERY);
    }

    /**
     * @param array $search
     * @return int
     * @throws
     */
    public function count(array $search = [])
    {
        return $this->search($search, Configuration::FETCH_COUNT);
    }

    /**
     * @param array $search
     * @param int $min
     * @return bool
     */
    public function has(array $search = [], int $min = 0)
    {
        return $this->count($search) > $min;
    }

    /**
     * @param array $search
     * @param int $itemsPerPage
     * @return Collection
     * @throws
     */
    public function next(array $search = [], int $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE)
    {
        return $this->search($search, Configuration::FETCH_PAGING_MORE, $itemsPerPage);
    }

    /**
     * @param array $search
     * @return Collection
     * @throws
     */
    public function getAll(array $search = [])
    {
        return $this->search($search, Configuration::FETCH_PAGING_NO, 0);
    }

    /**
     * @param array $attributes
     * @return Model|mixed
     * @throws
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
     * @throws
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

    /**
     * @param array $attributes
     * @param array $values
     * @return Model|mixed
     * @throws
     */
    public function firstOrCreateWithAttributes(array $attributes = [], array $values = [])
    {
        return $this->catch(function () use ($attributes, $values) {
            if (!empty($attributes)) {
                $this->model = $this->query()->firstOrCreate($attributes, $values);
            }
            return $this->model;
        });
    }

    public function force()
    {
        $this->force = true;
        return $this;
    }

    public function deleteAll()
    {
        return $this->queryDelete($this->query());
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
    public function queryDelete($query)
    {
        return $this->catch(function () use ($query) {
            if ($this->force) {
                $this->force = false;
                $query->forceDelete();
            }
            else {
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

    /**
     * @return Model|mixed
     * @throws
     */
    public function restore()
    {
        if (!is_null($this->model) && $this->model->trashed()) {
            return $this->catch(function () {
                $this->model->restore();
                return $this->model;
            });
        }
        return $this->model;
    }

    public function batchInsertStart($batch = 1000, $ignored = false)
    {
        $this->batch['insert'] = [
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
        $this->batch['insert']['run'] = 0;
        $this->batch['insert']['values'] = [];
        return $this;
    }

    public function batchInsert($attributes)
    {
        return $this->batchInsertAdd($attributes)
            ->batchInsertTryToSave();
    }

    public function batchInserted()
    {
        return $this->batch['insert']['inserted'];
    }

    protected function batchInsertAdd($attributes)
    {
        if ($this->newModel(false)->timestamps) {
            $now = DateTimer::syncNow();
            $attributes['created_at'] = $now;
            $attributes['updated_at'] = $now;
        }
        $this->batch['insert']['values'][] = $attributes;
        return $this;
    }

    protected function batchInsertTryToSave()
    {
        if (++$this->batch['insert']['run'] == $this->batch['insert']['batch']) {
            $this->batchInsertSave()
                ->batchInsertReset();

            $this->batch['insert']['inserted'] = true;
        }
        else {
            $this->batch['insert']['inserted'] = false;
        }
        return $this;
    }

    protected function batchInsertSave()
    {
        if (count($this->batch['insert']['values']) > 0) {
            $this->catch(function () {
                if ($this->batch['insert']['ignored']) {
                    $this->rawQuery()->insertOrIgnore($this->batch['insert']['values']);
                }
                else {
                    $this->rawQuery()->insert($this->batch['insert']['values']);
                }
            });
        }
        return $this;
    }

    public function batchInsertEnd()
    {
        return $this->batchInsertSave()
            ->batchInsertClear();
    }

    public function batchInsertAbort()
    {
        return $this->batchInsertClear();
    }

    public function batchInsertClear()
    {
        return $this->batchInsertStart();
    }

    public function batchReadStart($query, $batch = 1000)
    {
        $this->batch['read'] = [
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
     * @throws
     */
    public function batchRead(&$length, &$shouldEnd)
    {
        $collection = $this->catch(function () {
            return optional($this->batch['read']['query'])
                ->skip((++$this->batch['read']['run'] - 1) * $this->batch['read']['batch'])
                ->take($this->batch['read']['batch'])->get();
        });
        $length = $collection->count();
        $shouldEnd = $length < $this->batch['read']['batch'];
        return $collection;
    }

    public function batchReadEnd()
    {
        return $this->batchReadClear();
    }

    public function batchReadClear()
    {
        return $this->batchReadStart(null);
    }
}
