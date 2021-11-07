<?php

namespace App\Http\Controllers\StampsClasses;

use App\Http\Controllers\StampsTrait\StampsCommon;
use SoapClient;


abstract class Stamps
{
    use StampsCommon;

    protected $integrationID, $username, $password, $question1, $question2, $endpoint, $file;
    protected $request, $client, $wsdl, $json;


    public function setClient()
    {
       $this->client = new SoapClient($this->wsdl, array(
            'trace' => 1,
            'cache_wsdl' => 0,
            'stream_context' => stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            ))
        ));
    }

    public function setWsdl($path)
    {
        $this->wsdl = $path;
    }

    public function setConfigurationValues()
    {
        if (env("APP_ENV") == 'Production')
        {
            //production keys here
        } else {
            $this->integrationID = $this->getProperty('integrationID');
            $this->username = $this->getProperty('username');
            $this->password = $this->getProperty('password');
            $this->question1 = $this->getProperty('question1');
            $this->question2 = $this->getProperty('question2');
            $this->endpoint = $this->getProperty('endpoint');
        }
    }



    /**
     * @param  array
     * @return xml parsed
     */

    public function getXML($keys)
    {

        $xml = file_get_contents(storage_path('app/public/' . $this->file));
        //replace keys in xml with values

        foreach ($keys as $key => $value) {
            $xml = str_replace($key, $value, $xml);
        }
        // return $xml;
        return preg_replace("@\r\n@", "", $xml);
    }

    public function connect($url, $xml, $header=null)
    {

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 3600);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 3600);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($soap_do, CURLOPT_SSL_CIPHER_LIST, 'HIGH');
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
        curl_setopt(
            $soap_do,
            CURLOPT_HTTPHEADER,
            array(
                "POST /swsim/swsimv111.asmx HTTP/1.1",
                "Content-Type: text/xml; charset=utf-8",
                "Connection: Keep-Alive",
                "Content-Length: " . strlen($xml),
                "Cache-Control: private, max-age=0",
                "SOAPAction: " . $header,
            )
        );
        $result = curl_exec($soap_do);


        $err = curl_error($soap_do);
        $errno = curl_errno($soap_do);
        // var_dump($err);
        // var_dump($errno);
        return $result;
    }

    public function parse($xml)
    {
        //dd($xml);
        $this->dom = new \DOMDocument();
        $this->dom->loadXML($xml);
        $this->ret = array();

        if (!isset($this->dom) || $this->dom == false)
            return $this->ret;

        $this->root = $this->dom->documentElement;
        $this->ret[$this->root->nodeName] = $this->parse_node($this->root);

        $this->encoding = '';
        $matches = array();
        if (preg_match('/<\?xml.*encoding="(.*?)"\?>/', $xml, $matches))
            $this->encoding = $matches[1];


        return $this->ret;
    }
    public  function parse_node($node)
    {
        $ret = '';
        $node_name = $node->nodeName;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $n) {
                if ($n->nodeName == '#text' || $n->nodeName == '#cdata-section') {
                    if (!is_array($ret)) {
                        $ret = $n->nodeValue;
                    }
                    $node_value = $n->nodeValue;
                } else {
                    if (isset($ret) && !is_array($ret))
                        $ret = array();
                    $tmp = $this->parse_node($n);
                    $attrs = $n->attributes;

                    if ($attrs != NULL) {
                        $attrs = $this->convert_attrs($attrs);
                        if (!empty($attrs)) {
                            $tmp2 = $tmp;
                            $tmp = array();
                            $tmp['value'] = $tmp2;
                            $tmp['_attrs'] = $attrs;
                        }
                    }
                    if (!isset($ret[$n->nodeName])) {
                        $ret[$n->nodeName] = $tmp;
                    } else {
                        if (is_array($ret[$n->nodeName]) && !isset($ret[$n->nodeName][0])) {
                            $switch = $ret[$n->nodeName];
                            $ret[$n->nodeName] = array();
                            $ret[$n->nodeName][0] = $switch;
                        } else if (!is_array($ret[$n->nodeName]) && isset($ret[$n->nodeName])) {
                            $switch = $ret[$n->nodeName];
                            $ret[$n->nodeName] = array();
                            $ret[$n->nodeName][0] = $switch;
                        }
                        $ret[$n->nodeName][] = $tmp;
                    }
                }
            }
        }
        return $ret;
    }

    public  function convert_attrs($att)
    {

        $ret = array();

        foreach ($att as $i)
            $ret[$i->name] = $i->value;

        return $ret;
    }

}
