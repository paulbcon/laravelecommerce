<?php

namespace App\Http\Controllers\StampsTrait;



trait StampsCommon
{
    function getProperty($var)
    {
        //Test Environment
        if ($var == 'integrationID') return '0cd8eb0f-5ad6-4e6a-9a38-3c1268ce7302';
        if ($var == 'username') return 'ArleneT-001';
        if ($var == 'password') return 'October2021!';
        if ($var == 'question1') return '1234';
        if ($var == 'question2') return '1234';
        if ($var == 'endpoint') return 'https://swsim.testing.stamps.com/swsim/swsimv111.asmx';
    }
}
