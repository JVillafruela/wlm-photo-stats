<?php
class Heritage {
    var $country;
    
    function __construct($country) {
        $this->country=$country;
    }
    
    public function getHeritageId($text) {
        return $this->country == 'fr' ? $this->getHeritageIdfr($text) : FALSE;
    }

    protected function getHeritageIdfr($text) {
        if (preg_match('/\{\{Mérimée\|(.+)\}\}/i', $text, $matches, PREG_OFFSET_CAPTURE) !== 1) return false;
        $tplparams=$matches[1][0];
        if (preg_match('/PA\d[0-9AB]\d\d\d\d\d\d/', $text, $matches, PREG_OFFSET_CAPTURE) !== 1) return false;
        //print_r($matches);
        return $matches[0][0];   
    }
    
}