<?php

class report
{
    protected $data;
    protected $client;
    protected $responseHeaders;
    protected $response;
    
    public function __construct($client = null)
    {
        // Something, sometime.
    }
    
    public function getReport()
    {
        return $this->data;
    }
    
    public function setClient($client)
    {
        $this->client = $client;
        $this->setLastResponseHeaders($this->client->__getLastResponseHeaders());
        $this->setLastResponse($this->client->__getLastResponse());
    }
    
    public function setLastResponseHeaders($responseHeader)
    {
        $this->responseHeaders = $responseHeader;
    }
    
    public function getLastResponseHeaders()
    {
        return $this->responseHeaders;
    }
    
    public function setLastResponse($response)
    {
        $this->response = $response;
    }
    
    public function getLastResponse()
    {
        return $this->response;
    }
    
    protected function parseXml()
    {
    }
}

?>