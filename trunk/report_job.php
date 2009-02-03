<?php
	
	$reportURI = $HTTP_GET_VARS["reportURI"];
	
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
    <center>
    <h3>New job</h3>
    <form name="jobForm" method="post" action="report_job_save.php">
    	<table border="2" cellpadding="2" cellspacing="2">
    		<tr>
    			<td align="right">Report *</td>
    			<td align="left"><input name="reportURI" value="<?php echo $reportURI ?>" readonly="readonly" size="50"/></td>
    		</tr>
    		<tr>
    			<td align="right">Label *</td>
    			<td align="left"><input name="label" size="50"/></td>
    		</tr>
    		<tr>
    			<td align="right">Every *</td>
    			<td align="left">
    				<input name="interval" size="5"/>
    				<select name="intervalUnit">
    					<option value="MINUTE">mins</option>
    					<option value="HOUR">hrs</option>
    					<option value="DAY">days</option>
    					<option value="WEEK">weeks</option>
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<td align="right">Output *</td>
    			<td align="left">
    				<input type="checkbox" name="output[]" value="PDF"/>
    				PDF &nbsp;
    				<input type="checkbox" name="output[]" value="HTML"/>
    				HTML &nbsp;
    				<input type="checkbox" name="output[]" value="XLS"/>
    				XLS &nbsp;
    				<input type="checkbox" name="output[]" value="RTF"/>
    				RTF &nbsp;
    				<input type="checkbox" name="output[]" value="CSV"/>
    				CSV &nbsp;
    			</td>
    		</tr>
    		<tr>
    			<td align="right">Output filename * <sup>2</sup></td>
    			<td align="left"><input name="outputName" size="50"/></td>
    		</tr>
    		<tr>
    			<td align="right">Sequential filenames</td>
    			<td align="left">
    				<input type="checkbox" name="sequential" value="true"/>
    			</td>
    		</tr>
    		<tr>
    			<td align="right">Mail to</td>
    			<td align="left">
    				<input name="mailTo" size="50"/>
    			</td>
    		</tr>
    		<tr>
    			<td colspan="2" align="center">
    				<input type="submit" value="Save"/>
    			</td>
    		</tr>
    		<tr>
    			<td align="right">*</td>
    			<td align="left"><em>Mandatory</em></td>
    		</tr>
    		<tr>
    			<td align="right"><sup>1</sup></td>
    			<td align="left"><em>The job will be scheduled to start immediately</em></td>
    		</tr>
    		<tr>
    			<td align="right"><sup>2</sup></td>
    			<td align="left"><em>Saved under /ContentFiles</em></td>
    		</tr>
    	</table>
    </form>
    </center>
     <hr/>
     <a href="test_app.php?reportURI=<?php echo $reportURI ?>">Back</a>
     <br/>
     <a href="index.php">Exit</a>
    </body>
</html>
