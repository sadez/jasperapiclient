<?php

class Pdf extends report
{   
    public function parseXml()
    {
        preg_match('/boundary="(.*?)"/', $this->getLastResponseHeaders(), $matches);
        $boundary = $matches[1];
        $parts = explode($boundary, $this->getLastResponse());

        $pdf = null;
        foreach($parts as $part)
        {
            if (strpos($part, "Content-Type: application/pdf") !== false)
            {
                $pdf = substr($part, strpos($part, '%PDF-'));
                break;
            }
        }
        $this->data = $pdf;
    }
}

?>