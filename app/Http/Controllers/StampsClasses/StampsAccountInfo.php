<?php

namespace App\Http\Controllers\StampsClasses;

use App\Http\Controllers\StampsClasses\Stamps;
use SoapFault;
use App\Http\Controllers\StampsTrait\StampsCommon;


class StampsAccountInfo extends Stamps
{
    use StampsCommon;


    public function __construct()
    {
        $this->setConfigurationValues();
    }

    public function getAuthenticateUser($file = 'stampsAuthenticateUser.xml')
    {
        $this->file = $file;

        $replace = array(
            '[integrationID]' => $this->integrationID,
            '[username]' => $this->username,
            '[password]' => $this->password
        );

        $xml = $this->getXML($replace);

        $header = "http://stamps.com/xml/namespace/2021/01/swsim/SwsimV111/AuthenticateUser";

        dump($xml);

        try {

            if (env("APP_ENV") != 'Production') {
                $result = $this->connect('https://swsim.testing.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            } else {
                $result = $this->connect('https://swsim.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            }

            $inforesult = $this->parse($result);


            return $inforesult;


        } catch (SoapFault $fault) {
                echo $fault;
        }
    }


    public function getAccountInfo($file = 'stampsGetAccountInfo.xml')
    {
        $this->file = $file;

        $replace = array(
            '[integrationID]' => $this->integrationID,
            '[username]' => $this->username,
            '[password]' => $this->password
        );

        $xml = $this->getXML($replace);

        $header = "http://stamps.com/xml/namespace/2021/01/swsim/SwsimV111/GetAccountInfo";

       // dump($xml);

        try {

            if (env("APP_ENV") != 'Production') {
                $result = $this->connect('https://swsim.testing.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            } else {
                $result = $this->connect('https://swsim.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            }

            $inforesult = $this->parse($result);

            $newArray = @@$inforesult['soap:Envelope']['soap:Body']['GetAccountInfoResponse'];
            
            return $newArray;


        } catch (SoapFault $fault) {
                echo $fault;
        }
    }

    public function purchasePostage($data, $file='stampsPurchasePostage.xml')
    {
        $this->file = $file;

        $replace = array(
            '[authenticator]' => $data['authenticator'],
            '[purchaseamount]' => $data['purchaseamount'],
            '[controltotal]' => $data['controltotal']
        );

        $xml = $this->getXML($replace);
        $header =  "http://stamps.com/xml/namespace/2021/01/swsim/SwsimV111/PurchasePostage";
        dump($xml);

        try {

            if (env("APP_ENV") != 'Production') {
                $result = $this->connect('https://swsim.testing.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            } else {
                $result = $this->connect('https://swsim.stamps.com/swsim/swsimv111.asmx', $xml, $header);
            }

            $inforesult = $this->parse($result);

            $newArray = @@$inforesult['soap:Envelope']['soap:Body'];
            
            dd($newArray);

            return $newArray;


        } catch (SoapFault $fault) {
                echo $fault;
        }        
        
    }

}
