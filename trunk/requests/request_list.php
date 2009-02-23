<?php

class RequestList extends JasperApi
{
    private $is_new = false;
    private $current_uri;
    
    public function __construct($current_uri = '/')
    {
        $this->current_uri = $current_uri;
    }
    
    /**
     *
     */
    public function run($soap_client)
    {
        $xml_request = $this->getXmlTemplate('request_list.xml');
        $xml_request = str_replace('!!is_new!!', $this->is_new, $xml_request);
        $xml_request = str_replace('!!uri_string!!', $this->current_uri, $xml_request);

        try
        {
            $result = $soap_client->__soapCall('list', array( new SoapParam($xml_request,"requestXmlString") ));
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