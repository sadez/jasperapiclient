<?php

class ScheduleReport extends JasperApi
{   
    public function run()
    {
        // run the report
        $xml_request = $this->getXmlTemplate('schedule_report.xml');
        
        $params = $this->buildScheduleReportParameters();
        $formats = $this->buildScheduleReportFormats();
        $filename = $this->getReportFileName();
        $repository_destination = $this->buildScheduleReportRepositoryDestination();
        
        $xml_request = str_replace('!!report!!', $this->report, $xml_request);
        $xml_request = str_replace('!!label!!', date('U'), $xml_request);
        $xml_request = str_replace('!!description!!', '', $xml_request);
        $xml_request = str_replace('!!simple_trigger!!', '', $xml_request);
        $xml_request = str_replace('!!calendar_trigger!!', '', $xml_request);
        $xml_request = str_replace('!!output_filename!!', $filename, $xml_request);
        $xml_request = str_replace('!!output_format!!', $formats, $xml_request);
        $xml_request = str_replace('!!repository_destination!!', $repository_destination, $xml_request);
        $xml_request = str_replace('!!mail_notification!!', '', $xml_request);
    }
    
    /**
     *
     */
    private function buildScheduleReportRepositoryDestination()
    {
        $xml_request = $this->getXmlTemplate('schedule_report_repository_destination.xml');
        
        $xml_request = str_replace('!!folder_uri!!', '', $xml_request);
        $xml_request = str_replace('!!sequential_filenames!!', '', $xml_request);
        $xml_request = str_replace('!!override_files!!', '', $xml_request);
        
        return $xml_request;
    }
    
    
    /**
     * Takes an array of parameters and builds the proper XML for the scheduleReport call
     *
     * @param $params array An array holding all the parameters
     *
     * @return xml
     */
    private function buildScheduleReportParameters()
    {
        $param_count = count($this->params);
        $xml_request = '';
        if ($param_count > 0)
        {
            $xml_request = $this->getXmlTemplate('schedule_report_parameter.xml');
            $temp = '<parameters soapenc:arrayType="ns1:JobParameter[' . $param_count . ']" xsi:type="soapenc:Array" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">';
            
            foreach ($params AS $param)
            {
                $temp_xml_request = $xml_request;
                $temp_xml_request = str_replace('!!name_type!!', $this->param['name']['type'], $temp_xml_request);
                $temp_xml_request = str_replace('!!name_name!!', $this->param['name']['name'], $temp_xml_request);
                $temp_xml_request = str_replace('!!value_type!!', $this->param['value']['type'], $temp_xml_request);
                $temp_xml_request = str_replace('!!value_value!!', $this->param['value']['value'], $temp_xml_request);
                $temp .= $temp_xml_request;
            }
            
            $temp .= '</parameters>';
        }
        return $temp;
    }
    
    /**
     *
     */
    private function buildScheduleReportFormats()
    {
        $format_count = count($this->formats);
        $xml_formats = '';
        if ($formats_count > 0)
        {
            $xml_request = $this->getXmlTemplate('schedule_report_output_format.xml');
            $temp = '<outputFormats soapenc:arrayType="xsd:string[' . $format_count . ']" xsi:type="soapenc:Array" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">';
            
            foreach ($this->formats AS $format)
            {
                $temp_xml_request = $xml_request;
                $temp_xml_request = str_replace('!!output_format!!', $format);
                $temp .= $temp_xml_request;
            }
            
            $temp .= '</outputFormats>';
        }
        
        return $temp;
    }
}

?>