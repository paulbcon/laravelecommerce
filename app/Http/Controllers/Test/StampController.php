<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\StampsClasses\StampsAccountInfo;

class StampController extends Controller
{
    public function getInfo()
    {
        $result = new StampsAccountInfo();
        $account = $result->getAccountInfo();

        dd($account);


    }
}
