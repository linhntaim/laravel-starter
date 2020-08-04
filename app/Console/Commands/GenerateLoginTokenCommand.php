<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Models\SysToken;

class GenerateLoginTokenCommand extends Command
{
    protected $signature = 'generate:login_token {user}';

    protected $noInformation = true;

    protected function go()
    {
        $sysToken = SysToken::query()->create([
            'type' => SysToken::TYPE_LOGIN,
        ]);
        $this->warn(json_encode([
            'token' => $sysToken->token,
            'id' => $this->argument('user'),
        ]));
    }
}
