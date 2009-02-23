<?php

class RequestGet extends JasperApi
{
    private $is_new = false;
    private $uri;
    private $arguments;
    
    public function __construct($uri = '/', $args = array())
    {
        $this->setUri($uri);
        $this->setArguments($args);
    }

    public function run($soap_client)
    {
        $arguments = '';
        if (is_array($this->arguments))
        {
            foreach ($this->arguments AS $key => $value)
            {
                $arguments .= '<argument name="' . $key . '">'. $value . '</argument>';
            }
        }
        
        $xml_request = $this->getXmlTemplate('request_get.xml');
        $xml_request = str_replace('!!is_new!!', $this->is_new, $xml_request);
        $xml_request = str_replace('!!uri_string!!', $this->uri, $xml_request);
        $xml_request = str_replace('!!name!!', $this->uri, $xml_request);
        $xml_request = str_replace('!!arguments!!', $arguments, $xml_request);

        try
        {
            $result = $soap_client->__soapCall('get', array( new SoapParam($xml_request,"requestXmlString") ));
        }
        catch(SoapFault $exception)
        {
            throw new Exception("Jasper did not return list data. Instead got: \n$result");
        }

        return $result;
    }
    
    public function setUri($uri)
    {
        $this->uri = $uri;
    }
    
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }
}

?>