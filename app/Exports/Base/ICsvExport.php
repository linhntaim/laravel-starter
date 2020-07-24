<?php

namespace App\Exports\Base;

interface ICsvExport
{
    function csvHeaders();

    function csvExport();
}
