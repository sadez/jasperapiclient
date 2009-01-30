<?php

class RequestList extends JasperApi
{
    private $is_new = false;
    
    public function __construct()
    {}
    
    /**
     *
     */
    public function run($soap_client)
    {
        $xml_request = $this->getXmlTemplate('request_list.xml');
        $xml_request = str_replace('!!is_new!!', $this->is_new, $xml_request);

        try
        {
            $result = $soap_client->__soapCall('list', array( new SoapParam($xml_request,"requestXmlString") ));
            echo $soap_client->__getLastRequest(); exit;
            echo $soap_client->__getLastResponse(); exit;
        }
        catch(SoapFault $exception)
        {
            throw new Exception("Jasper did not return list data. Instead got: \n$result");
        }
        
        return $result;
    }
    
    public function setIsNew($is_new)
    {
        $this->is_new = (bool)$is_new;
    }
}

?>