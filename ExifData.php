<?php

/**
 * Description of ExifData
 *
 * @author Jérôme
 */
class ExifData {
    
    var $exif;
    
    function __construct($metadata) {
        foreach($metadata as $m) {
            if (is_array($m) && !is_array($m['value'])) {
                //print "{$m['name']} {$m['value']} \n";
                $this->exif[$m['name']]=$m['value'];
            } else {
                $this->exif[$m['name']]="not implemented";
            }
        }        
    }
    
    public function __get($name)   {
        if (array_key_exists($name, $this->exif)) {
            return $this->exif[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name)  {
        return isset($this->exif[$name]);
    }    
 

}
