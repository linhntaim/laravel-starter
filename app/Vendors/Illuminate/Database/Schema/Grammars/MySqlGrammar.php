<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Vendors\Illuminate\Database\Schema\Grammars;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as BaseMySqlGrammar;

class MySqlGrammar extends BaseMySqlGrammar
{
    protected function compileCreateEngine($sql, Connection $connection, Blueprint $blueprint)
    {
        return $this->compileCreateRowFormat(
            parent::compileCreateEngine($sql, $connection, $blueprint),
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
