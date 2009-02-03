<?php

class JobSummary
{
    public $id; // long
    public $version; // int
    public $reportUnitURI; // string
    public $username; // string
    public $label; // string
    public $state; // RuntimeJobState
    public $previousFireTime; // dateTime
    public $nextFireTime; // dateTime
}

?>