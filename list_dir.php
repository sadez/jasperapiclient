<?php 

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

error_reporting(E_ALL);
ini_set('display_errors', true);
session_start();
if ($_SESSION['username'] == '')
{
	header("Location: index.php");
    	exit();
}

$currentUri = "/";
$parentUri = "/";

if ($_GET['uri'] != '')
{
	$currentUri = $_GET['uri'];
}


$pos = strrpos($currentUri, "/");
if($pos === false || $pos == 0)
{
     $parentUri="/";
}
else
{
	 $parentUri = substr($currentUri, 0, $pos );
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

// Check the credientials
$request = new RequestList($currentUri);
$result = $request->run($soap_client);

if (get_class($result) == 'SoapFault')
{
	$errorMessage = $result->getFault()->faultstring;
}
else
{
    $obj = new RepositoryService();
	$folders = $obj->getResourceDescriptors($result);
}


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>

    <h1>List report</h1>
    
    Current Directory: <?php echo $currentUri; ?><br>
    <br>
    <a href="?uri=<?php echo $parentUri; ?>">[..]</a><br>

	<table border="1" cellpadding="3">   
    <?php
       for ($i=0; $i < count($folders); ++$i)
       {
       	    $resource = $folders[$i];

    	    if ( $resource['type'] == 'folder')
    	    {	           
    		?>
    		<tr>
    			<td>
    				<a href="?uri=<?php echo $resource['uri']; ?>">[<?php echo $resource['label']; ?>]</a>
    			</td>
    			<td>&nbsp;</td>
    		</tr>
    		<?php
            } 
            else if ( $resource['type'] == 'reportUnit')
    	    {
    		?>
    		<tr>
    			<td>
    				<a href="run_report.php?uri=<?php echo $resource['uri']; ?>"><?php echo $resource['label']; ?></a>
    			</td>
    			<td>
    				<a href="report_schedule.php?reportURI=<?php echo $resource['uri'] ?>">Schedule</a>
    			</td>
    		</tr>
    		<?php
            }
       }
    ?>
    </table>
     <br>
     <br>
     <hr>
     <a href="index.php">Exit</a>
    </body>
</html>
