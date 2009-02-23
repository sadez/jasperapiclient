<?php

class RequestRunReport extends JasperApi
{
    private $page;
    
    public function __construct($report, $format, $params, $page)
    {
        $this->report = $report;
        $this->format = $format;
        $this->params = $params;
        $this->page = $page;
    }
    /**
     *
     */
    public function run($soap_client)
    {
        $params_xml = $this->buildRunRequestReportParameters();
        $xml_request = $this->getXmlTemplate('request_run_report.xml');
        
        $arguments = '<argument name="RUN_OUTPUT_FORMAT">' . strtoupper(get_class($this->format)) . '</argument>';
        $arguments .= '<argument name="RUN_OUTPUT_PAGE">' . $this->page . '</argument>';

        $xml_request = str_replace('!!arguments!!', $arguments, $xml_request);
        $xml_request = str_replace('!!report!!', $this->report, $xml_request);
        $xml_request = str_replace('!!params!!', $params_xml, $xml_request);
        
        try
        {
            $result = $soap_client->__soapCall('runReport', array(new SoapParam($xml_request,"requestXmlString")));
            $this->format->setClient($soap_client);
            $this->format->parseXml();
        }
        catch(SoapFault $exception)
        {
            $this->format->setClient($soap_client);
            if ($exception->faultstring == "looks like we got no XML document" && strpos($this->format->getLastResponseHeaders(), "Content-Type: multipart/related;") !== false)
            {
                $this->format->parseXml();
            }
            else
            {
                throw $exception;
            }
        }

        if (!isset($result))
        {
            return $this->format->getReport();
        }
        else
        {
            throw new Exception('Jasper did not return ' . strtoupper(get_class($format)) . ' data. Instead got: ' . "\n" . $result);
        }
    }
    
    /**
     *
     */
    private function buildRunRequestReportParameters()
    {
        $params_xml = '';
        foreach ($this->params as $name => $value)
        {
            $params_xml .= "<parameter name=\"$name\"><![CDATA[$value]]></parameter>\n";
        }
        return $params_xml;
    }
}

?>