<?php

class Job
{
    public $id; // long
    public $version; // int
    public $reportUnitURI; // string
    public $username; // string
    public $label; // string
    public $description; // string
    public $simpleTrigger; // JobSimpleTrigger
    public $calendarTrigger; // JobCalendarTrigger
    public $parameters; // ArrayOfJobParameter
    public $baseOutputFilename; // string
    public $outputFormats; // ArrayOf_xsd_string
    public $outputLocale; // string
    public $repositoryDestination; // JobRepositoryDestination
    public $mailNotification; // JobMailNotification
}

?>