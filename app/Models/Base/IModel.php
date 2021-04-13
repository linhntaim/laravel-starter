<?php


namespace App\Models\Base;


interface IModel
{
    public function lockTableQuery($options = []);

    public function lockTable($options = []);

    public function unlockTableQuery();

    public function unlockTable();
}