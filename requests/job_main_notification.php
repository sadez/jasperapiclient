<?php

class JobMailNotification
{
    public $id; // long
    public $version; // int
    public $toAddresses; // ArrayOf_xsd_string
    public $subject; // string
    public $messageText; // string
    public $resultSendType; // ResultSendType
    public $skipEmptyReports; // boolean
}

?>