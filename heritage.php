<?php
class Heritage {
    var $country;
    
    function __construct($country) {
        $this->country=$country;
    }
    
    public function getHeritageId($text) {
        return $this->country == 'fr' ? $this->getHeritageIdfr($text) : FALSE;
    }
    
    /*  Examples
        {{Mérimée}}    
        {{Mérimée|PA00090753}}    
        {{Mérimée|PA00090676|IA35024928}}
        {{Mérimée|type=inscrit|PA16000036}}    
        {{mérimée|type=classé|PA16000036}}    
        {{Mérimée|type=classé+inscrit|PA00110393}}
     */
    protected function getHeritageIdfr($text) {
        if (preg_match('/\{\{Mérimée\|(.+)\}\}/i', $text, $matches, PREG_OFFSET_CAPTURE) !== 1) return false;
        $tplparams=$matches[1][0];
        if (preg_match('/PA\d[0-9AB]\d\d\d\d\d\d/', $tplparams, $matches, PREG_OFFSET_CAPTURE) !== 1) return false;
        //print_r($matches);
        return $matches[0][0];   
    }
    
}