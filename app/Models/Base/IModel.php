<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

interface IModel extends IFromModel, IResource, IActivityLog
{
    public static function table();

    public static function fullTable($as = null);

    public static function fullColumn($column, $as = null);

    public static function fullColumns($columns, $as = []);

    public function getFullTable($as = null);

    public function getFullColumn($column, $as = null);

    public function getFullColumns($columns, $as = []);

    public function lockTableQuery($options = []);

    public function lockTable($options = []);

    public function unlockTableQuery();

    public function unlockTable();
}
