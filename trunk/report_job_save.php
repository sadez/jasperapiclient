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
	$username = $_SESSION["username"];
	$password = $_SESSION["password"];
	if (!isset($username))
	{
		header("Location: index.php");
		exit();
	}
			
	$reportSchedulerService = new ReportSchedulerService($api_ini['jasper_server_settings']['jasper_schedule_url'], $username, $password);
	
	$job = new Job();
	$reportURI = $_POST["reportURI"];
	$job->reportUnitURI = $reportURI;
	$job->label = $_POST["label"];
	$job->baseOutputFilename = $_POST["outputName"];
	$job->outputFormats = $_POST["output"];
   
	$repoDest = new JobRepositoryDestination();
	$repoDest->folderURI = "/ContentFiles"; //hardcoded!
	$repoDest->sequentialFilenames = isset($_POST["sequential"]);
	$job->repositoryDestination = $repoDest;
   
	$trigger = new JobSimpleTrigger();
	$trigger->occurrenceCount = -1; //recur indefinitely
	$trigger->recurrenceInterval = $_POST["interval"];
	$trigger->recurrenceIntervalUnit = $_POST["intervalUnit"];
	$job->simpleTrigger = $trigger;

	$mailTo = $_POST["mailTo"];
	if ($mailTo != "")
	{
		$mail = new JobMailNotification();
		$mail->toAddresses = array($mailTo);
		$mail->subject = "Reports";
		$mail->messageText = "Some reports";
		$mail->resultSendType = ResultSendType::SEND;
		$job->mailNotification = $mail;
	}
	$reportSchedulerService->__setLocation($api_ini['jasper_server_settings']['jasper_schedule_url']);
	$savedJob = $reportSchedulerService->scheduleJob($job);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JasperServer Web Services Sample</title>
    </head>
    <body>

    <center><h1>JasperServer Web Services Sample</h1></center>
    <hr/>
    <h3>Saved job <?php echo $savedJob->id ?>.</h3>
     <hr/>
     <a href="report_schedule.php?reportURI=<?php echo $reportURI ?>">Back</a>
    <br/>
     <a href="index.php">Exit</a>
    </body>
</html>
