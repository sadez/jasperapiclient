<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

$SCHEDULING_WS_URI = "https://reports.teladoc.com/services/ReportScheduler?wsdl";


class IntervalUnit {
  const MINUTE = 'MINUTE';
  const HOUR = 'HOUR';
  const DAY = 'DAY';
  const WEEK = 'WEEK';
}

class CalendarDaysType {
  const ALL = 'ALL';
  const WEEK = 'WEEK';
  const MONTH = 'MONTH';
}

class ResultSendType {
  const SEND = 'SEND';
  const SEND_ATTACHMENT = 'SEND_ATTACHMENT';
}

class RuntimeJobState {
  const UNKNOWN = 'UNKNOWN';
  const NORMAL = 'NORMAL';
  const EXECUTING = 'EXECUTING';
  const PAUSED = 'PAUSED';
  const COMPLETE = 'COMPLETE';
  const ERROR = 'ERROR';
}


/**
 * ReportSchedulerService class
 * 
 *  
 * 
 */
class ReportSchedulerService extends SoapClient
{
    
    private static $classmap = array(
                                      'JobTrigger' => 'JobTrigger',
                                      'JobCalendarTrigger' => 'JobCalendarTrigger',
                                      'JobMailNotification' => 'JobMailNotification',
                                      'JobParameter' => 'JobParameter',
                                      'JobRepositoryDestination' => 'JobRepositoryDestination',
                                      'JobSimpleTrigger' => 'JobSimpleTrigger',
                                      'Job' => 'Job',
                                      'JobSummary' => 'JobSummary',
                                      'IntervalUnit' => 'IntervalUnit',
                                      'CalendarDaysType' => 'CalendarDaysType',
                                      'ResultSendType' => 'ResultSendType',
                                      'RuntimeJobState' => 'RuntimeJobState',
                                     );
    
    public function ReportSchedulerService($wsdl, $username, $password, $options = array())
    {
      foreach(self::$classmap as $key => $value) {
        if(!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      
      $options['login'] = $username;
      $options['password'] = $password;
      
      parent::__construct($wsdl, $options);
    }
    
    /**
     *  
     *
     * @param long $id
     * @return void
     */
    public function deleteJob($id)
    {
      return $this->__soapCall('deleteJob', array($id),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
    /**
     *  
     *
     * @param ArrayOf_xsd_long $ids
     * @return void
     */
    public function deleteJobs($ids)
    {
      return $this->__soapCall('deleteJobs', array($ids),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
    /**
     *  
     *
     * @param long $id
     * @return Job
     */
    public function getJob($id)
    {
      return $this->__soapCall('getJob', array($id),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
    /**
     *  
     *
     * @param Job $job
     * @return Job
     */
    public function scheduleJob(Job $job)
    {
      return $this->__soapCall('scheduleJob', array($job),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
    /**
     *  
     *
     * @param Job $job
     * @return Job
     */
    public function updateJob(Job $job)
    {
      return $this->__soapCall('updateJob', array($job),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
    /**
     *  
     *
     * @param  
     * @return ArrayOfJobSummary
     */
    public function getAllJobs()
    {
      return $this->__soapCall('getAllJobs', array(),       array(
              'uri' => 'http://www.jasperforge.org/jasperserver/ws',
              'soapaction' => ''
             )
        );
    }
    
}

?>