<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs;

use App\Imports\Base\Import;
use App\Jobs\Base\Job;

class ImportJob extends Job
{
    /**
     * @var Import
     */
    public $import;

    public function __construct(Import $import)
    {
        parent::__construct();

        $this->import = $import;
    }

    public function go()
    {
        $this->import->import();
    }
}
