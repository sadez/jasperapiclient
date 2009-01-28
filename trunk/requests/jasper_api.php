<?php

class JasperApi
{
    protected $report;
    protected $format;
    protected $params;
    
    private $soap_client;
    
    /**
     *
     */
    public function __construct($report, $format, $params)
    {
        $this->report = $report;
        $this->format = $format;
        $this->params = $params;
    }
    
    /**
     *
     */
    public function getReportFileName()
    {
        $filename = substr($this->report, strrpos($this->report, '/') + 1) . date('U');
        return $filename;
    }
    
    /**
     *
     *
     */
    protected function getXmlTemplate($name)
    {
        $filename = FULL_PATH . '/templates/' . $name;
        $handle = fopen($filename, 'r');
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return $contents;
    }
    
    
}

?>