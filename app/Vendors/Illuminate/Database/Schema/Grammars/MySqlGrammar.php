<?php


namespace App\Vendors\Illuminate\Database\Schema\Grammars;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as BaseMySqlGrammar;
use Illuminate\Support\Fluent;

class MySqlGrammar extends BaseMySqlGrammar
{
    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        return $this->compileCreateRowFormat(
            parent::compileCreate($blueprint, $command, $connection),
            $connection,
            $blueprint
        );
    }

    protected function compileCreateRowFormat($sql, Connection $connection, Blueprint $blueprint)
    {
        if (isset($blueprint->rowFormat)) {
            $sql .= ' row_format = ' . $blueprint->rowFormat;
        } elseif (!is_null($rowFormat = $connection->getConfig('row_format'))) {
            $sql .= ' row_format = ' . $rowFormat;
        }

        return $sql;
    }
}
