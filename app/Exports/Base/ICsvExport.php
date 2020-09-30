<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports\Base;

interface ICsvExport
{
    function csvHeaders();

    function csvExport();
}
