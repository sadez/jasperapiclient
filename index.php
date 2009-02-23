<?php
   error_reporting(E_ALL);
   ini_set('display_errors', true);
   
   //Loading up my required files
   {
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
   }
   
   $errorMessage = "";
   $username = isset($_POST['username']) ? $_POST['username'] : '';
   $password = isset($_POST['password']) ? $_POST['password'] : '';
   
   if ($username != '')
   {
        $soap_options = array(
            'location'      => $api_ini['jasper_server_settings']['jasper_repository_url'],
            'uri'           => 'urn:',
            'login'         => $_POST['username'],
            'password'      => $_POST['password'],
            'trace'         => 1,
            'exception'     => 1,
            'soap_version'  => SOAP_1_1,
            'style'         => SOAP_RPC,
            'use'           => SOAP_LITERAL);
        
        $soap_client = new SoapClient(null, $soap_options);
        
        // Check the credientials
        $check_user = new CheckUsername();
        $result = $check_user->run($soap_client);

   		if (get_class($result) == 'SoapFault')
   		{
   			$errorMessage = $result->faultstring;
		}
		else
		{
            session_start();
            session_unset();
            session_register('username');
            session_register('password');
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            header("location: list_dir.php?uri=");	
            exit();	
		}
   }
             

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>

    <h1>Welcome to the James API Example</h1>
    
   <h2><font color="red"><?php echo $errorMessage; ?></font></h2>
   
   <form action="index.php" method=POST>

       Insert a JasperServer username and password (i.e. tomcat/tomact)<br><br>
       
       Username <input type="text" name="username"><br>
       Password <input type="password" name="password"><br>
       
       <br>
       <input type="submit" value="Enter">  
       
   </form>
    
    
    
    </body>
</html>
