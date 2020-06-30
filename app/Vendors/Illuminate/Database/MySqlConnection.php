<?php


namespace App\Vendors\Illuminate\Database;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use App\Vendors\Illuminate\Database\Schema\Blueprint;
use App\Vendors\Illuminate\Database\Schema\Grammars\MySqlGrammar;

class MySqlConnection extends BaseMySqlConnection
{
    public function getSchemaBuilder()
    {
        $schemaBuilder = parent::getSchemaBuilder();
        $schemaBuilder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $schemaBuilder;
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new MySqlGrammar());
    }
}
