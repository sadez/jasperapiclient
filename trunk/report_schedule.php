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
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
	if (!isset($username))
	{
		header("Location: index.php");
		exit();
	}
    
	$reportSchedulerService = new ReportSchedulerService($api_ini['jasper_server_settings']['jasper_schedule_url'], $username, $password);

	$reportURI = (isset($_GET['reportURI'])) ? $_GET['reportURI'] : '';
	$parentURI = substr($reportURI, 0, strrpos($reportURI, "/"));
	
	$reportSchedulerService->__setLocation($api_ini['jasper_server_settings']['jasper_schedule_url']);

	$jobs = $reportSchedulerService->getReportJobs($reportURI);

?>

    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">

    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>JasperServer Web Services Sample</title>
        </head>
        <body>

        <center><h1>JasperServer Web Services Sample</h1></center>
        <hr/>
        <h3>List report jobs</h3>
        Report: <?php echo $reportURI ?><br>
        <br/>
        <table id="mainTable" border="2" cellpadding="2" cellspacing="2" height="100%" valign="top">
        <tr>
        	<td align="center">Id</td>
        	<td align="center">Label</td>
        	<td align="center">State</td>
        	<td align="center">Last ran at</td>
        	<td align="center">Next run time</td>
        <tr>
    <?php
        if (is_array($jobs))
        {
    	    foreach ($jobs as $job)
    	    {
    ?>
        <tr>
        	<td><?php echo $job->id ?></td>
        	<td><?php echo $job->label ?></td>
        	<td><?php echo $job->state ?></td>
        	<td><?php echo $job->previousFireTime ?></td>
        	<td><?php echo $job->nextFireTime ?></td>
        </tr>
    <?php
    	    }
    	}
    	else
    	{   ?>
    	<tr>
    	    <td colspan="5">No scheduled jobs.</td>
    	</tr>
	<?php
	    }
    ?>
         </table>
         <br/>
         <a href="report_job.php?reportURI=<?php echo $reportURI ?>">Schedule new job</a>
         <hr/>
         <a href="list_dir.php?uri=<?php echo $parentURI ?>">Back to repository</a>
         <br/>
         <a href="index.php">Exit</a>
        </body>
    </html>
