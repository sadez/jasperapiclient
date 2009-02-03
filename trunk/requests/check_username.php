<?php

class CheckUsername extends JasperApi
{
    private $is_new = false;
    
    public function __construct()
    {}
    
    /**
     *
     */
    public function run($soap_client)
    {
        
        $op_xml = "<request operationName=\"list\"><resourceDescriptor name=\"\" wsType=\"folder\" uriString=\"\" isNew=\"false\">".
		"<label></label></resourceDescriptor></request>";
		
		$params = array("request" => $op_xml );
		$response = $info->call("list",$params,array('namespace' => $GLOBALS["namespace"]));
		
		return $response;
		
		
		
		
        $xml_request = $this->getXmlTemplate('request_list.xml');
        $xml_request = str_replace('!!is_new!!', $this->is_new, $xml_request);
        $sml_request = str_replace('!!uri_string!!', '/', $xml_request);

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