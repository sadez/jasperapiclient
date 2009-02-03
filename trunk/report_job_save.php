<?php

	require_once("ReportSchedulerService.php");

	session_start();
	$username = $HTTP_SESSION_VARS["username"];
	$password = $HTTP_SESSION_VARS["password"];
	if (!isset($username))
	{
		header("Location: index.php");
		exit();
	}
			
	$reportSchedulerService = new ReportSchedulerService($SCHEDULING_WS_URI, $username, $password);
	
	$job = new Job();
	$reportURI = $HTTP_POST_VARS["reportURI"];
	$job->reportUnitURI = $reportURI;
	$job->label = $HTTP_POST_VARS["label"];
	$job->baseOutputFilename = $HTTP_POST_VARS["outputName"];
	$job->outputFormats = $HTTP_POST_VARS["output"];
   
	$repoDest = new JobRepositoryDestination();
	$repoDest->folderURI = "/ContentFiles"; //hardcoded!
	$repoDest->sequentialFilenames = isset($HTTP_POST_VARS["sequential"]);
	$job->repositoryDestination = $repoDest;
   
	$trigger = new JobSimpleTrigger();
	$trigger->occurrenceCount = -1; //recur indefinitely
	$trigger->recurrenceInterval = $HTTP_POST_VARS["interval"];
	$trigger->recurrenceIntervalUnit = $HTTP_POST_VARS["intervalUnit"];
	$job->simpleTrigger = $trigger;

	$mailTo = $HTTP_POST_VARS["mailTo"];
	if ($mailTo != "")
	{
		$mail = new JobMailNotification();
		$mail->toAddresses = array($mailTo);
		$mail->subject = "Reports";
		$mail->messageText = "Some reports";
		$mail->resultSendType = ResultSendType::SEND;
		$job->mailNotification = $mail;
	}
	$reportSchedulerService->__setLocation($SCHEDULING_WS_URI);
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
     <a href="reportSchedule.php?reportURI=<?php echo $reportURI ?>">Back</a>
    <br/>
     <a href="index.php">Exit</a>
    </body>
</html>
