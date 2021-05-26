<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use App\Utils\Database\MySqlTableLock;
use Illuminate\Database\Eloquent\Factories\HasFactory;

trait ModelTrait
{
    use FromModelTrait, ResourceTrait, ActivityLogTrait, HasFactory, MemorizeTrait;

    public static function table()
    {
        return (new static)->getTable();
    }

    protected static function tableWithPrefix($table, $prefix = '', $as = null)
    {
        return $as ?
            sprintf('%s%s as %s', $prefix, $table, $as)
            : sprintf('%s%s', $prefix, $table);
    }

    protected static function columnWithTable($column, $table, $prefix = '', $as = null)
    {
        return $as ?
            sprintf('%s.%s as %s', static::tableWithPrefix($table, $prefix), $column, $as)
            : sprintf('%s.%s', static::tableWithPrefix($table, $prefix), $column);
    }

    public static function fullTableOf($table, $as = null)
    {
        return static::tableWithPrefix($table, (new static)->getConnection()->getTablePrefix(), $as);
    }

    public static function fullTable($as = null)
    {
        return (new static)->getFullTable($as);
    }

    public static function fullColumnOfTable($column, $table, $as = null)
    {
        return static::columnWithTable($column, $table, (new static)->getConnection()->getTablePrefix(), $as);
    }

    public static function fullColumn($column, $as = null)
    {
        return (new static)->getFullColumn($column, $as);
    }

    public static function fullColumnsOfTable($columns, $table, $as = null)
    {
        return array_map(function ($column, $index) use ($table, $as) {
            return static::columnWithTable(
                $column,
                $table,
                (new static)->getConnection()->getTablePrefix(),
                $as[$column] ?? ($as[$index] ?? null)
            );
        }, $columns);
    }

    public static function fullColumns($columns, $as = [])
    {
        return (new static)->getFullColumns($columns, $as);
    }

    public function getFullTable($as = null)
    {
        return static::tableWithPrefix($this->getTable(), $this->getConnection()->getTablePrefix(), $as);
    }

    public function getFullColumn($column, $as = null)
    {
        return static::columnWithTable($column, $this->getTable(), $this->getConnection()->getTablePrefix(), $as);
    }

    public function getFullColumns($columns, $as = [])
    {
        return array_map(function ($column, $index) use ($as) {
            return $this->getFullColumn($column, $as[$column] ?? ($as[$index] ?? null));
        }, $columns);
    }

    protected function getTableLock($options = [])
    {
        $connection = $this->getConnectionName() ?? config('database.default');
        $options['table'] = $this->getFullTable();
        switch ($connection) {
            case 'mysql':
                return new MySqlTableLock($options);
            default:
                return null;
        }
    }

    public function lockTableQuery($options = [])
    {
        if ($tableLock = $this->getTableLock($options)) {
            return $tableLock->toLockQuery();
        }
        return null;
    }

    public function lockTable($options = [])
    {
        if ($query = $this->lockTableQuery($options)) {
            return $this->getConnection()->unprepared($query);
        }
        return false;
    }

    public function unlockTableQuery()
    {
        if ($tableLock = $this->getTableLock()) {
            return $tableLock->toUnlockQuery();
        }
        return null;
    }

    public function unlockTable()
    {
        if ($query = $this->unlockTableQuery()) {
            return $this->getConnection()->unprepared($query);
        }
        return false;
    }

    public function mergeVisible(array $visible)
    {
        $this->visible = array_merge($this->visible, $visible);
        return $this;
    }

    public function mergeAppends(array $appends, $visible = true)
    {
        $this->appends = array_merge($this->appends, $appends);
        return $visible ? $this->mergeVisible($appends) : $this;
    }

    public function toArray()
    {
        return $this->attributesToArray();
    }
}
