<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\StampsClasses\StampsAccountInfo;

class StampController extends Controller
{
    public function getAuthenticateUser()
    {
        $result = new StampsAccountInfo();
        $account = $result->getAuthenticateUser();

        dd($account);

    }

    public function getAccountInfo()
    {
        $result = new StampsAccountInfo();
        $account = $result->getAccountInfo();

        dd($account);

    }

    public function purchasePostage()
    {
        $result = new StampsAccountInfo();
        $account = $result->getAccountInfo();

        $data = array(
            'authenticator' => $account['Authenticator'],
            'purchaseamount' => 100.00,
            'controltotal' => $account['AccountInfo']['PostageBalance']['ControlTotal'],
        );

        $purchase = $result->purchasePostage($data, $file = 'stampsPurchasePostage.xml');

    }
}
