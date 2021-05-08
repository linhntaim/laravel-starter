<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class NowListener
{
    use ClassTrait, IndependentClientTrait, TransactionTrait;

    protected $transactionUsed = false;

    protected static function __transCurrentModule()
    {
        return 'listener';
    }

    public function start()
    {
        Log::info(sprintf('%s listening...', static::class));
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s listened!', static::class));
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this;
    }

    public function handle($event)
    {
        if ($this->transactionUsed) {
            $this->transactionStart();
        }
        $this->independentClientApply();
        try {
//        $this->start();
            $this->go($event);
//            $this->end();
            if ($this->transactionUsed) {
                $this->transactionComplete();
            }
        }
        catch (Throwable $e) {
            if (!($this instanceof Listener)) {
                if ($this->transactionUsed) {
                    $this->transactionStop();
                }
                $this->failed($event, $e);
            }
            throw $e;
        }
    }

    protected abstract function go($event);

    public function failed($event, Throwable $e)
    {
        $this->fails();
    }
}
