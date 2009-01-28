<?php
/**
 * A client for the Jasper Report Server Web Service written in PHP
 */

class JasperApiClient
{
    private $soap_client;

    public function __construct($url, $username, $password)
    {
        $this->soap_client = new SoapClient(null, array(
            'location'      => $url,
            'uri'           => 'urn:',
            'login'         => $username,
            'password'      => $password,
            'trace'         => 1,
            'exception'     => 1,
            'soap_version'  => SOAP_1_1,
            'style'         => SOAP_RPC,
            'use'           => SOAP_LITERAL));
    }
    
    /**
     *
     *
     */
    public function scheduleReport($report, $format, $params)
    {
        $report = new ScheduleReport($report, $format, $params);
        $report->run($this->soap_client);
    }
    
    /**
     * Makes a SOAP call to a Jasper server and returns a string containing XML that is all the list items
     *
     * @return XML
     */
    public function requestList()
    {
        $list = new RequestList();
        return $list->run($this->soap_client);
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
        $report = new RequestReport($report, $format, $params);
        return $report->run($this->soap_client);
    }
}
?>