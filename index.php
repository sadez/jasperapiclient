<?php
   error_reporting(E_ALL);
   ini_set('display_errors', true);
   require_once('client.php');

   $errorMessage = "";
   
   $username = $HTTP_POST_VARS['username'];
   $password = $HTTP_POST_VARS['password'];
   
   if ($username != '')
   {
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
