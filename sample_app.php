<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once('jasper_api_client.php');
require_once('format/report.php');
require_once('format/pdf.php');
require_once('format/html.php');
require_once('builders/html_list.php');


$jasper_url = "https://reports.teladoc.com/services/repository";
$jasper_username = "portal";
//$jasper_username = "jthullbery";
$jasper_password = "test1234";

// Create the JasperApiClient object - Pass it the URL, Username and Password
$client = new JasperApiClient($jasper_url, $jasper_username, $jasper_password);

// Request the list of reports
$listXml = $client->requestList();

// Load XML load from list request into an HtmlList object to create the selector
$htmlList = new HtmlList($listXml);

// Build the Sample App
$html = '<h1>Welcome to the Jasper API Client sample application.</h1>
         <br>Select a report from the drop down and select either HTML or PDF as the report format and click Submit.';

$html .= '
        <form name="sample_app" action="sample_app.php" method="post">
        <table>
            <tr>
                <td>' . $htmlList->getHtmlList() . '</td>
                <td><input type="radio" name="format" value="pdf" checked/>PDF<br /><input type="radio" name="format" value="html" />HTML</td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
        </form>';

// If returning from a submit...
if (isset($_POST['submit']))
{
    // Grab the report desired
    $report_unit = $_POST['report_list'];
    
    // Create a format object for the desired format
    $report_format = ($_POST['format'] == 'pdf') ? new Pdf() : new Html();

    
    $params = array( 'COMPANY_ID' => '5');
    
    // Request the report
    $report = $client->requestReport($report_unit, $report_format, $params);
    
    // If they requested PDF, update the headers and echo the report
    if (strtoupper(get_class($report_format)) == 'PDF')
    {
        $filename = substr($_POST['report_list'], strrpos($_POST['report_list'], '/'));
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . date('U') . '.pdf"');
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
                $handle = fopen('/Applications/MAMP/htdocs/wsdl/images/' . $filename, 'w');
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
        echo strtoupper(get_class($report_format)) . 'China. Man.';
    }
}
else
{
    echo $html;
}
?>