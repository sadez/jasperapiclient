<?php

class ReportList
{
    private $list;
    private $options;
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
        $this->options = array();
    }
    
    public function getHtmlList($name = 'report_list', $id = 'report_list')
    {
        $this->parseXml();
        $this->buildHtmlList($name = 'report_list', $id = 'report_list');
        return $this->list;
    }
    
    private function buildHtmlList($name = 'report_list', $id = 'report_list')
    {
        $html = '<select name="' . $name . '" id="' . $id . '">';
        foreach ($this->options AS $value)
        {
            $html .= '<option name="' . $value . '" value="' . $value . '">' . $value . '</option>';
        }
        $html .= '</select>';
        
        $this->list = $html;
    }
    
    private function parseXml()
    {
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser, 'startTag', 'endTag');
        xml_set_character_data_handler($xml_parser, 'contents');
        
        if (!(xml_parse($xml_parser, $this->data)))
        {
            throw new Exception('XML Parsing Error on line ' . xml_get_current_line_number($xml_parser));
        }
        
        xml_parser_free($xml_parser);
    }
    
    private function startTag($parser, $data, $attrs)
    {
        if ($data == 'RESOURCEDESCRIPTOR' && isset($attrs['WSTYPE']) && $attrs['WSTYPE'] == 'reportUnit')
        {
            $this->options[] = $attrs['URISTRING'];
        }
        //echo '<b>' . $data;
    }
    
    private function contents($parser, $data)
    {
        //echo $data;
    }
    
    private function endTag($parser, $data)
    {
        //echo '</b><br/>' . $data;
    }
    
}

?>