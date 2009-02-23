<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

$api_ini = parse_ini_file('jasper_api_client.ini', true);

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

session_start();
if ($HTTP_SESSION_VARS["username"] == '')
{
	header("Location: index.php");
    	exit();
}

// 1 Get the ReoportUnit ResourceDescriptor...
$currentUri = "/";

if ($_GET['uri'] != '')
{
	$currentUri = $_GET['uri'];
}


$soap_options = array(
    'location'      => $api_ini['jasper_server_settings']['jasper_repository_url'],
    'uri'           => 'urn:',
    'login'         => $_SESSION['username'],
    'password'      => $_SESSION['password'],
    'trace'         => 1,
    'exception'     => 1,
    'soap_version'  => SOAP_1_1,
    'style'         => SOAP_RPC,
    'use'           => SOAP_LITERAL);

$soap_client = new SoapClient(null, $soap_options);

$obj = new RequestGet($currentUri);
$result = $obj->run($soap_client);
if (get_class($result) == 'SoapFault')
{
	$errorMessage = $result->getFault()->faultstring;
	echo $errorMessage;
	exit();
}
else
{
	$repoObj = new RepositoryService();
	$folders = $repoObj->getResourceDescriptors($result);
}

if (count($folders) != 1 || $folders[0]['type'] != 'reportUnit')
{
	 echo "<H1>Invalid RU ($currentUri)</H1>";
	 echo "<pre>$result</pre>";
	 
	 exit(); 
}

$reportUnit = $folders[0];

// 2. Prepare the parameters array looking in the $HTTP_GET_VARS for params
// starting with PARAM_ ...
//

$report_params = array();

$moveToPage = 'execute_report.php?uri=' . $currentUri;

foreach ($_GET AS $param_name)
{
	if (strncmp("PARAM_", $param_name,6) == 0)
	{
		$report_params[substr($param_name,6)] = $_GET[$param_name];
		
	}
	
	if ($param_name != "page" && $param_name != "uri" && isset($_GET[$param_name]))
	{
		$moveToPage .= "&".urlencode($param_name)."=". urlencode($_GET[$param_name]);	
 	}
}
 
$moveToPage .="&page=";
 
// 3. Execute the report
$page = 1;
if ( isset($_GET['format']) && $_GET['format'] == RUN_OUTPUT_FORMAT_HTML && isset($_GET['page']) && $_GET['page'] != '')
{
    $page = $_GET['page'];
}

// START HERE
$report_format = (isset($_GET['format'])) ? new $_GET['format']() : false;

if (!$report_format)
{
    throw new exception('Format not defined on line ' . __LINE__ . '.');
}

$reportObj = new RequestRunReport($currentUri, $report_format, $report_params, $page);
$result = $reportObj->run($soap_client);

// 4. 
if (get_class($result) == 'SoapFault')
{
    $errorMessage = $result->getFault()->faultstring;
    echo $errorMessage;
    exit();
}


// INSERT ALL THE BUILDERS


// If they requested PDF, update the headers and echo the report
if (strtoupper(get_class($report_format)) == RUN_OUTPUT_FORMAT_PDF)
{
    $filename = $reportObj->getReportFileName();
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    echo $report;
}

// If they requested HTML then go through each data element
// and write the image to the file system
else if (strtoupper(get_class($report_format)) == RUN_OUTPUT_FORMAT_HTML)
{
    $html = $result['html'];
    $html = str_replace('images/px', 'images/white.gif', $html);
    foreach ($result AS $k => $v)
    {
        if (strpos($k, 'img_') !== FALSE)
        {
            $filename = $reportObj->getReportFileName() . $v['extension'];
            $handle = fopen(WRITABLE . $filename, 'w');
            if (fwrite($handle, $v['image']) === FALSE)
            {
                throw new exception('Could not write to file on line ' . __LINE__);
            }
            $html = str_replace($k, $filename, $html);
            fclose($handle);
        }
    }
    
    echo $html;
}
else if (strtoupper(get_class($report_format)) == RUN_OUTPUT_FORMAT_XLS)
{
    echo 'Complete this!';
	header ( 'Content-type: application/xls' );
	header ( 'Content-Disposition: attachment; filename="report.xls"');
	echo( $attachments["cid:report"]);
	
}
else
{
    echo 'Nothing defined for format: ' . strtoupper(get_class($report_format)) . '.';
}
?>