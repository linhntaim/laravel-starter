<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class NowJob
{
    use ClassTrait, Dispatchable, IndependentClientTrait, TransactionTrait;

    protected $transactionUsed = false;

    protected static function __transCurrentModule()
    {
        return 'job';
    }

    public function start()
    {
        Log::info(sprintf('%s jobbing...', static::class));
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s jobbed!', static::class));
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this;
    }

    public function handle()
    {
        if ($this->transactionUsed) {
            $this->transactionStart();
        }
        $this->start();
        $this->independentClientApply();
        try {
            $this->end();
            $this->go();
            if ($this->transactionUsed) {
                $this->transactionComplete();
            }
        } catch (Throwable $e) {
            if (!($this instanceof Job)) {
                if ($this->transactionUsed) {
                    $this->transactionStop();
                }
                $this->failed($e);
            }
            throw $e;
        }
    }

    public abstract function go();

    public function failed(Throwable $e)
    {
        $this->fails();
    }
}
