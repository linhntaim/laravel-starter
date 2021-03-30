<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

/**
 * Trait MultipleKeyTrait
 * @package App\ModelTraits
 * @method string|array getKeyName()
 */
trait MultipleKeyTrait
{
    public function getKey()
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $keys = [];
            foreach ($keyNames as $keyName) {
                $keys[$keyName] = $this->getAttribute($keyName);
            }
            return $keys;
        }
        return $this->getAttribute($this->getKeyName());
    }

    public function getQualifiedKeyName()
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $columns = [];
            foreach ($keyNames as $keyName) {
                $columns[] = $this->qualifyColumn($keyName);
            }
            return $columns;
        }
        return parent::getQualifiedKeyName();
    }

    public function getForeignKey()
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $keys = [];
            $snakedClassBasename = Str::snake(class_basename($this));
            foreach ($keyNames as $keyName) {
                $keys[] = $snakedClassBasename . '_' . $keyName;
            }
            return $keys;
        }
        return parent::getForeignKey();
    }

    protected function setKeysForSelectQuery($query)
    {
        if (is_array($keys = $this->getKeyForSelectQuery())) {
            $query->where(function ($query) use ($keys) {
                foreach ($keys as $keyName => $keyValue) {
                    $query->where($keyName, $keyValue);
                }
            });
            return $query;
        }
        return parent::setKeysForSelectQuery($query);
    }

    protected function getKeyForSelectQuery()
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $keys = [];
            foreach ($keyNames as $keyName) {
                $keys[$keyName] = $this->original[$keyName] ?? $this->getAttribute($keyName);
            }
            return $keys;
        }
        return parent::getKeyForSelectQuery();
    }

    protected function setKeysForSaveQuery($query)
    {
        if (is_array($keys = $this->getKeyForSaveQuery())) {
            $query->where(function ($query) use ($keys) {
                foreach ($keys as $keyName => $keyValue) {
                    $query->where($keyName, $keyValue);
                }
            });
            return $query;
        }
        return parent::setKeysForSaveQuery($query);
    }

    protected function getKeyForSaveQuery()
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $keys = [];
            foreach ($keyNames as $keyName) {
                $keys[$keyName] = $this->original[$keyName] ?? $this->getAttribute($keyName);
            }
            return $keys;
        }
        return parent::getKeyForSaveQuery();
    }

    public static function destroy($ids)
    {
        if ($ids instanceof BaseCollection) {
            $ids = $ids->all();
        }

        $ids = is_array($ids) ? $ids : func_get_args();

        if (count($ids) === 0) {
            return 0;
        }

        // We will actually pull the models from the database table and call delete on
        // each of them individually so that their events get fired properly with a
        // correct set of attributes in case the developers wants to check these.
        $key = ($instance = new static)->getKeyName();

        $count = 0;

        if (is_array($key)) {
            $query = $instance;
            foreach ($ids as $id) {
                $query->orWhere(function ($query) use ($key, $id) {
                    foreach ($key as $k) {
                        $query->where($key, $id[$k]);
                    }
                });
            }
            foreach ($query->get() as $model) {
                if ($model->delete()) {
                    $count++;
                }
            }
        } else {
            foreach ($instance->whereIn($key, $ids)->get() as $model) {
                if ($model->delete()) {
                    $count++;
                }
            }
        }

        return $count;
    }

    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        if (is_array($keyName)) {
            foreach ($keyName as $k) {
                $this->setAttribute($k, $id[$k]);
            }
        } else {
            $this->setAttribute($keyName, $id);
        }
    }

    public function replicate(array $except = null)
    {
        if (is_array($keyNames = $this->getKeyName())) {
            $defaults = array_merge($keyNames, [
                $this->getCreatedAtColumn(),
                $this->getUpdatedAtColumn(),
            ]);

            $attributes = Arr::except(
                $this->getAttributes(), $except ? array_unique(array_merge($except, $defaults)) : $defaults
            );

            return tap(new static, function ($instance) use ($attributes) {
                $instance->setRawAttributes($attributes);

                $instance->setRelations($this->relations);

                $instance->fireModelEvent('replicating', false);
            });
        }
        return parent::replicate($except);
    }
}