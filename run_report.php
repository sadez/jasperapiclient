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
    header('Location: index.php');
    exit();
}

$currentUri = "/";
$previousUri = "/";

if ($_GET['uri'] != '')
{
	$currentUri = $_GET['uri'];
	if (strlen($currentUri) > 1)
	{
	    $previousUri = substr($currentUri, 0, strrpos($currentUri, '/'));
    }
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

$requestGetObj = new RequestGet($currentUri);
$result = $requestGetObj->run($soap_client);
if (get_class($result) == 'SoapFault')
{
	$errorMessage = $result->getFault()->faultstring;
	echo $errorMessage;
	exit();
}
else
{
	$repositoryServiceObj = new RepositoryService();
	$folders = $repositoryServiceObj->getResourceDescriptors($result);
}

if (count($folders) != 1 || $folders[0]['type'] != 'reportUnit')
{
	 echo "<H1>Invalid RU ($currentUri)</H1>";
	 echo "<pre>$result</pre>";
	 exit(); 
}

$reportUnit = $folders[0];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>

    <h1><?php echo  $reportUnit['label']; ?></h1>
    
    <a href="list_dir.php?uri=<?php echo $previousUri; ?>">[..]</a>
    
    <br>
    <br>
    </a><br>
    <form action="execute_report.php">
     <input type="hidden" name="uri" value="<?php echo $currentUri ?>">
   <table border=0>
    <?php 
       $parametersCount = 0;
       $resources = $reportUnit['resources'];
       
       // Find the datasource uri...
       $dsUri = '';
       for ($i=0; $i< count($resources); ++$i)
       {
           $rd = $resources[$i];

           if ($rd['type'] == TYPE_DATASOURCE )
           {
                 $dsUri = $rd['properties'][PROP_FILERESOURCE_REFERENCE_URI]['value'];  //getReferenceUri();
           }
           else if ($rd['type'] == TYPE_DATASOURCE_JDBC ||
                    $rd['type'] == TYPE_DATASOURCE_JNDI ||
                    $rd['type'] == TYPE_DATASOURCE_BEAN)
           {
                 $dsUri = $rd['uri'];
           }
       }
       
       
       // Show all input controls
       for ($i=0; $i< count($resources); ++$i)
       {
            $rd = $resources[$i];
            
            if ($rd['type'] == TYPE_INPUT_CONTROL )
            {
                $parametersCount++;
       ?>
        <tr><td><?php echo $rd['label']; ?></td><td>
       <?php   
       		$controlType = $rd['properties'][PROP_INPUTCONTROL_TYPE]['value'];
       		
                if ($controlType == IC_TYPE_BOOLEAN)
                {
    ?>
        <input type="checkbox" value="true" name="<?php echo "PARAM_".$rd['name']; ?>">
    <?php                
                }
                else if ($controlType == IC_TYPE_SINGLE_VALUE)
                {
    ?>
        <input type="text" name="<?php echo "PARAM_".$rd['name']; ?>">
    <?php                
                }
                else if ($controlType == IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES)
                {
    ?>
        <select name="<?php echo "PARAM_".$rd['name']; ?>">
               <?php
               
               	   $listOfValues = array();
               	   foreach ($rd['resources'] AS $lov)
                   {
                        if ($lov['type'] == TYPE_LOV)
                        {
                            $listOfValues = $lov;
                            break;
                        }
                    }
                    
                    //  LOV->properties { [PROP_LOV]->properties { [0]{name,value}, [1]{name,value}... } }
                    //  name = key
                    //  value = label
                    
                    foreach ($listOfValues['properties'][PROP_LOV]['properties'] AS $lovItem)
                    {
                    	?>
                            <option value="<?php echo htmlspecialchars($lovItem['name']); ?>"><?php echo htmlspecialchars($lovItem['value']); ?></option>
                        <?php 
                    }
               
               ?>
        </select>
        
    <?php                
                }
                else if ($controlType == IC_TYPE_SINGLE_SELECT_QUERY)
                {
    ?>
        <select name="<?php echo "PARAM_".$rd['name']; ?>">
                <?php
                   // Get the list of entries....
                   
                   $requestGetObj->setUri($rd['uri']);
                   $requestGetObj->setArguments(array( IC_GET_QUERY_DATA => $dsUri ));
                   $result = $requestGetObj->run($soap_client);
                   if (get_class($result) == 'SoapFault')
                   {
                       $errorMessage = $result->getFault()->faultstring;
                       echo $errorMessage;
                       exit();
                   }
                   else
                   {
                       $folders = $repositoryServiceObj->getResourceDescriptors($result);
                   }
                   
                   print_r($folders);
                   
                   exit;
                   
                   $rds = $folders;
                   
                   //$result = ws_get($rd['uri'], array( IC_GET_QUERY_DATA => $dsUri ) );
                   
                   //$rds = $repositoryServiceObj->getResourceDescriptors($result);
                   $rd = $rds[0]; 
                   
                   $datarows = $rd['properties'][PROP_QUERY_DATA]['properties'];
                   
                   foreach ($datarows AS $datarow)
                   {
                   	$row_value = $datarow['value'];
                   	$row_label = "";
                   	$k = 0;
                   	foreach ($datarow['properties'] AS $datacolumn)
                   	{
                   		if ($k > 0) $row_label .= "   |   ";
                   		$row_label .= $datacolumn['value'];
                		$k++;
                	}
                   	?>
                   	<option value="<?php echo htmlspecialchars($row_value); ?>"><?php echo htmlspecialchars($row_label); ?></option>
                   	<?php
                   }                    
                ?>
        </select>
        </td><td><?php echo $rd['description']; ?></td></tr>
    <?php           
            }
         }
      }
    ?>
    </table>
     <br>
     Export format: <select name="format">
         <option value="HTML">HTML</option>
         <option value="PDF">PDF</option>
         <option value="XLS">XLS</option>
     </select>
     
     
     
     <input type="submit" value="Run the report">
     </form>
     <br>
     <?php
        if ($parametersCount > 0)
        {
     ?>
     Attention: some input controls may require a number. The date/time format used with the webservices is a Long (current time: <?php echo time(); ?>).
     <?php
        }
     ?>
     <hr>
     <a href="index.php">Exit</a>
    </body>
</html>

