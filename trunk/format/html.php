<?php

class Html extends report
{
    public function parseXml()
    {
        preg_match('/boundary="(.*?)"/', $this->getLastResponseHeaders(), $matches);
        
        $boundary = $matches[1];
        $parts = explode($boundary, $this->getLastResponse());

        $pdf = null;
        
        foreach($parts as $part)
        {
            // Remove the training dashes (--)
            $part = substr($part, 0, -2);
            if (strpos($part, 'Content-Type: image/png') !== false)
            {
                $end = strpos($part, '>') - (strpos($part, 'Content-Id: ') + 13);
                $image_id = substr($part, strpos($part, 'Content-Id: ') + 13, $end);
                
                $html[$image_id]['image'] = substr($part, strpos($part, '>') + 5);
                $html[$image_id]['extension'] = '.png';
                //break;
            }
            else if (strpos($part, "Content-Type: text/html") !== false)
            {
                $html['html'] = substr($part, strpos($part, '<html>'));
                //break;
            }
            else if (strpos($part, "Content-Type: image/jpeg") !== false)
            {
                $end = strpos($part, '>') - (strpos($part, 'Content-Id: ') + 13);
                $image_id = substr($part, strpos($part, 'Content-Id: ') + 13, $end);
                
                $html[$image_id]['image'] = substr($part, strpos($part, '>') + 5);
                $html[$image_id]['extension'] = '.jpg';
            }
            
        }
        
        $this->data = $html;
    }
}

?>