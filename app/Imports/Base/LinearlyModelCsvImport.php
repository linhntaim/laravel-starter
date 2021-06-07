<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports\Base;

/**
 * Class ModelCsvImport
 * @package App\Imports\Base
 */
abstract class LinearlyModelCsvImport extends LinearlyCsvImport
{
    use ModelCsvImportTrait;
}
