<?php

class JobCalendarTrigger extends JobTrigger
{
    public $minutes; // string
    public $hours; // string
    public $daysType; // CalendarDaysType
    public $weekDays; // ArrayOf_xsd_int
    public $monthDays; // string
    public $months; // ArrayOf_xsd_int
}

?>