<?php
/**
 * A client for the Jasper Report Server Web Service written in PHP
 */

class JasperApiClient
{
    private $url;
    private $username;
    private $password;
    private $soapVersion;
    private $style;
    private $use;

    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->soapVersion = SOAP_1_1;
        $this->style = SOAP_RPC;
        $this->use = SOAP_LITERAL;
    }
    
    /**
     * Makes a SOAP call to a Jasper server and returns a string containing XML that is all the list items
     *
     * @return XML
     */
    public function requestList()
    {
        $request = '
        <request operationName="list" locale="en">
            <resourceDescriptor name="" wsType="folder" uriString="/reports" isNew="false">
                <label>null</label>
            </resourceDescriptor>
        </request>';

        $client = new SoapClient(null, array(
            'location'      => $this->url,
            'uri'           => 'urn:',
            'login'         => $this->username,
            'password'      => $this->password,
            'trace'         => 1,
            'exception'     => 1,
            'soap_version'  => $this->soapVersion,
            'style'         => $this->style,
            'use'           => $this->use));

        $list = null;
        $temp_array = array();
        try
        {
            $result = $client->__soapCall('list', array( new SoapParam($request,"requestXmlString") ));
        }
        catch(SoapFault $exception)
        {
            throw new Exception("Jasper did not return list data. Instead got: \n$result");
        }
        
        return $result;
    }

    /**
     * Returns a report that in given $format
     *
     * @param $report string The name of the report as it appears on the server
     * @param $format object An object of the given format for said report
     * @param $params array  An array holding any number of paramters that pertain to said report
     *
     * @return $format object
     */
    public function requestReport($report, $format, $params)
    {
        $params_xml = "";
        foreach ($params as $name => $value)
        {
            $params_xml .= "<parameter name=\"$name\"><![CDATA[$value]]></parameter>\n";
        }

        $request = '
        <request operationName="runReport" locale="en">
          <argument name="RUN_OUTPUT_FORMAT">' . strtoupper(get_class($format)) . '</argument>
          <resourceDescriptor name="" wsType=""
          uriString="' . $report . '"
          isNew="false">
          <label>null</label>
          ' . $params_xml . '
          </resourceDescriptor>
        </request>';

        $client = new SoapClient(null, array(
            'location'  => $this->url,
            'uri'       => 'urn:',
            'login'     => $this->username,
            'password'  => $this->password,
            'trace'    => 1,
            'exception'=> 1,
            'soap_version'  => $this->soapVersion,
            'style'    => $this->style,
            'use'      => $this->use));
        
        try
        {
            $result = $client->__soapCall('runReport', array(new SoapParam($request,"requestXmlString")));
            $format->setClient($client);
            $format->parseXml();
        }
        catch(SoapFault $exception)
        {

            $format->setClient($client);
            if ($exception->faultstring == "looks like we got no XML document" && strpos($format->getLastResponseHeaders(), "Content-Type: multipart/related;") !== false)
            {
                $format->parseXml();
            }
            else
            {
                throw $exception;
            }
        }

        if (!isset($result))
        {
            return $format->getReport();
        }
        else
        {
            throw new Exception('Jasper did not return ' . strtoupper(get_class($format)) . ' data. Instead got: ' . "\n" . $result);
        }
    }

}
?>