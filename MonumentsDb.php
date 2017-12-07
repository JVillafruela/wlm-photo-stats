<?php

class MonumentsDb {
    
	protected $api;

	/** @var  Requests_Session */
	protected $session;
	protected $useragent;

	protected $error     = null;
	protected $debugMode = false; 
    
	public function __construct($api=null)
	{
		$this->api = $api==null ? 'https://tools.wmflabs.org/heritage/api/api.php' : $api;

		$this->initRequests();
	}

	/**
	 * Set up a Requests_Session with appropriate user agent.
	 *
	 * @return  void
	 */
	protected function initRequests()
	{
		$this->useragent = 'wlm-photo-stats (https://github.com/jva6438/wlm-photo-stats)';

		$this->session = new Requests_Session($this->api);
		$this->session->useragent = $this->useragent;
	} 
    
	/**
	 * Sets the debug mode.
	 *
	 * @param   boolean   $b  True to turn debugging on
	 * @return  Wikimate      This object
	 */
	public function setDebugMode($b)
	{
		$this->debugMode = $b;
		return $this;
	}    


	/**
	 * Get or print the Requests configuration.
	 *
	 * @param   boolean  $echo  Whether to echo the options
	 * @return  array           Options if $echo is false
	 * @return  boolean         True if options have been echoed to STDOUT
	 */
	public function debugRequestsConfig($echo = false)
	{
		if ($echo) {
			echo "<pre>Requests options:\n";
			print_r($this->session->options);
			echo "Requests headers:\n";
			print_r($this->session->headers);
			echo "</pre>";
			return true;
		}
		return $this->session->options;
	}


	/**
	 * Performs a query to the API with the given details.
	 *
	 * @param   array  $params  Array of details to be passed in the query
	 * @return  array          Unserialized php output from the wiki API
	 */
	public function query(array $params)
	{
		$params['action'] = 'search';
		$params['format'] = 'json';

		$apiResult = $this->session->get($this->api.'?'.http_build_query($params));
        $result = json_decode($apiResult->body,true);
        if (is_array($result) && array_key_exists('monuments', $result)) {            
           return  count($result['monuments'])==1 ?  $result['monuments'][0] : $result['monuments'];
        }
		return $result;
	}    
    
    
    public function searchById($id,$country) {
        $params['srcountry']=$country;
        $params['srid']=$id;
        return $this->query($params);        
    } 
}

