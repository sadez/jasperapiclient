<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

$api_ini = parse_ini_file('jasper_api_client.ini', true);

define('FULL_PATH', '/home/jthullbery/www/public_html/jasperApi/trunk/');

foreach ($api_ini['parent'] AS $value)
{
    require_once($value);
}

foreach ($api_ini['required_dir'] AS $value)
{
    $files = scandir(FULL_PATH . $value, 1);
    foreach ($files AS $name)
    {
        if (substr($name, -3) == 'php')
        {
            require_once($value . '/' . $name);
        }
    }
}

foreach ($api_ini['required'] AS $value)
{
    require_once($value);
}

// Create a SoapClient()
$soap_client = new SoapClient(null, array(
    'location'      => $api_ini['jasper_server_settings']['jasper_url'],
    'uri'           => 'urn:',
    'login'         => $api_ini['jasper_server_settings']['jasper_username'],
    'password'      => $api_ini['jasper_server_settings']['jasper_password'],
    'trace'         => 1,
    'exception'     => 1,
    'soap_version'  => SOAP_1_1,
    'style'         => SOAP_RPC,
    'use'           => SOAP_LITERAL));

// Request the list of reports
$list = new RequestList();
$listXml = $list->run($soap_client);

// Load XML load from list request into an HtmlList object to create the selector
$htmlList = new HtmlList($listXml);

// Build the Sample App
$html = '<h1>Welcome to the Jasper API Client sample application.</h1>
         <br>Select a report from the drop down and select either HTML or PDF as the report format and click Submit.';

$html .= '
        <form name="demo_app" action="demo_app.php" method="post">
        <table>
            <tr>
                <td>' . $htmlList->getHtmlList() . '</td>
                <td><input type="radio" name="format" value="pdf" checked/>PDF<br /><input type="radio" name="format" value="html" />HTML</td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="Submit"></td>
                <td><input type="submit" name="future_report" value="Schedule Report"></td>
            </tr>
        </table>
        </form>';

if (isset($_POST['future_report']))
{
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    
    // Grab the report to be scheduled
    $report_unit = $_POST['report_list'];
    
    // Create a format object for the desired format
    $report_format = ($_POST['format'] == 'pdf') ? new Pdf() : new Html();
    
    $params = array( array( 'name'     => array( 'name' => '', 'type' => ''),
                     'value'    => array( 'value' => '', 'type' => '')));
    
    // Request the scheduling
    $reportObj = new ScheduleReport($report_unit, $report_format, $params);
    $report = $reportObj->run($soap_client);
    
}
// If returning from a submit...
if (isset($_POST['submit']))
{
    // Grab the report desired
    $report_unit = $_POST['report_list'];
    
    // Create a format object for the desired format
    $report_format = ($_POST['format'] == 'pdf') ? new Pdf() : new Html();

    $params = array( 'COMPANY_ID' => '5');

    // Request the report
    $reportObj = new RequestReport($report_unit, $report_format, $params);
    $report = $reportObj->run($soap_client);

    // If they requested PDF, update the headers and echo the report
    if (strtoupper(get_class($report_format)) == 'PDF')
    {
        $filename = $reportObj->getReportFileName();
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
        echo $report;
    }

    // If they requested HTML then go through each data element
    // and write the image to the file system
    else if(strtoupper(get_class($report_format)) == 'HTML')
    {
        $html = $report['html'];
        $html = str_replace('images/px', 'images/white.gif', $html);
        foreach ($report AS $k => $v)
        {
            if (strpos($k, 'img_') !== FALSE)
            {
                $filename = date('U') . '_' . $k . $v['extension'];
                $handle = fopen('/path/to/writable/' . $filename, 'w');
                if (fwrite($handle, $v['image']) === FALSE)
                {
                    echo "Could not write to file ({$filename})";
                }
                $html = str_replace($k, $filename, $html);
                fclose($handle);
            }
        }
        
        echo $html;
    }
    else
    {
        echo 'Nothing defined for format: ' . strtoupper(get_class($report_format)) . '.';
    }
}
else
{
    echo $html;
}
?>