<?php

class RequestReport extends JasperApi
{
    /**
     *
     */
    public function run($soap_client)
    {
        $params_xml = $this->buildRequestReportParameters();
        $xml_request = $this->getXmlTemplate('request_report.xml');

        $xml_request = str_replace('!!format!!', strtoupper(get_class($this->format)), $xml_request);
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
    private function buildRequestReportParameters()
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