<?php

class LocationService {

	protected $api;
    const API='https://overpass-api.de/api/interpreter/';

	/** @var  Requests_Session */
	protected $session;
	protected $useragent;

	protected $error     = null;
	protected $debugMode = false;
    
    protected $country;
    
    // country : adm level monumentsdb => admin level OSM
    protected $levels = array ('fr' => array('2' => '6'));


    public function __construct($country,$api=self::API) {
		$this->country = $country;
        $this->api = $api;
		$this->initRequests();
	}  
    
    
	/**
	 * Set up a Requests_Session with appropriate user agent.
	 *
	 * @return  void
	 */
	protected function initRequests() {
		$this->useragent = 'wlm-photo-stats (https://github.com/jva6438/wlm-photo-stats)';

		$this->session = new Requests_Session($this->api);
		$this->session->useragent = $this->useragent;
	}   
    
    /**
     * Get the administrative level code at the given coordinates
     * 
     * @param float $lat
     * @param float $lon
     * @param string $level OSM admin level 
     * @return mixed 
     */
    public function getAdministrativeLevel($lat,$lon,$level) {
    	$headers = array(
			'Content-Type' => "application/x-www-form-urlencoded"
		); 
        
        $query='[out:json];
            is_in(%F,%F);
            area._[boundary=administrative][admin_level="%s"];
            out body;';
        
        $form=array('data' => sprintf($query,$lat,$lon,$level));
        $apiResult = $this->session->post($this->api, $headers, $form);
        $data=json_decode($apiResult->body,true);   
        if (is_null($data)) return false;
        $code=$data['elements'][0]['tags']['ISO3166-2'];
        return strtolower($code);
    }    
    
    // Get the monumentsdb level at the given coordinates
    public function getAdminLevel($lat,$lon,$level) {
        if(!array_key_exists($this->country, $this->levels)) return false;
        if(!array_key_exists($level, $this->levels[$this->country])) return false;
        
        return $this->getAdministrativeLevel($lat, $lon, $this->levels[$this->country][$level]);        
    }
    
    
    public function getAdm2($lat,$lon) {      
        return $this->getAdminLevel($lat, $lon, '2');
    }
   
}

