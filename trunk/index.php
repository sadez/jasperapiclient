<?php
   error_reporting(E_ALL);
   ini_set('display_errors', true);
   
   //Loading up my required files
   {
   $api_ini = parse_ini_file('jasper_api_client.ini', true);
   define('FULL_PATH', '/home/jthullbery/www/public_html/testing/1/');
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
   $username = isset($HTTP_POST_VARS['username']) ? $HTTP_POST_VARS['username'] : '';
   $password = isset($HTTP_POST_VARS['password']) ? $HTTP_POST_VARS['password'] : '';
   
   if ($username != '')
   {
        $soap_client = new SoapClient(null, array(
            'location'      => $api_ini['jasper_server_settings']['jasper_repository_url'],
            'uri'           => 'urn:',
            'login'         => $api_ini['jasper_server_settings']['jasper_username'],
            'password'      => $api_ini['jasper_server_settings']['jasper_password'],
            'trace'         => 1,
            'exception'     => 1,
            'soap_version'  => SOAP_1_1,
            'style'         => SOAP_RPC,
            'use'           => SOAP_LITERAL));
            
   		$result = ws_checkUsername($username, $password);
   		if (get_class($result) == 'SOAP_Fault')
   		{
   			$errorMessage = $result->getFault()->faultstring;
		}
		else
		{
			session_start();
            session_unset();
			session_register("username");
			session_register("password");
			$HTTP_SESSION_VARS["username"]=$username;
			$HTTP_SESSION_VARS["password"]=$password;
			header("location: test_app.php");	
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

    <h1>Welcome to the JasperServer sample (PHP version)</h1>
    
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
