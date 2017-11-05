<?php

class WikiUser {

    protected $wikimate = null;
    protected $exists = false;
    protected $invalid = false;
    protected $error = null;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var boolean
     */
    protected $emailable;

    /**
     * @var int
     */
    protected $editcount;

    /**
     * @var string
     */
    protected $registration;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var array
     */
    protected $rights;

    /*
     *
     * Magic methods
     *
     */

    /**
     * Constructs a WikiUser object from the user name given
     * and associate with the passed Wikimate object.
     *
     * @param  string    $name  Name of the user
     * @param  Wikimate  $wikimate  Wikimate object
     */
    public function __construct($name, $wikimate) {
        $this->wikimate = $wikimate;
        $this->name = $name;
        $this->getInfo();
    }

    /**
     * Forget all object properties.
     *
     * @return  <type>  Destructor
     */
    public function __destruct() {
        $this->wikimate = null;
        $this->exists = false;
        $this->invalid = false;
        $this->error = null;
        $this->name = null;
        $this->id = null;
        $this->gender = null;
        $this->emailable = null;
        $this->registration = null;
        $this->rights = null;
        $this->groups = null;

        return null;
    }

    /**
     * Returns the user existence status.
     *
     * @return  boolean  True if file exists
     */
    public function exists() {
        return $this->exists;
    }

    /**
     * Alias of self::__destruct().
     */
    public function destroy() {
        $this->__destruct();
    }

    /*
     *
     * User meta methods
     *
     */

    /**
     * Returns the latest error if there is one.
     *
     * @return  mixed  The error array, or null if no error
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Gets the information of the user. 
     *
     * @return  boolean              The info of the user (array), or false if error
     */
    public function getInfo() {
        $data = array(
            'list' => 'users',
            'usprop' => 'gender|emailable|registration|editcount|rights|groups|blockinfo',
            'ususers' => $this->name
        );

        $r = $this->wikimate->query($data); // Run the query
        // Check for errors
        if (isset($r['error'])) {
            $this->error = $r['error']; // Set the error if there was one
            return false;
        } else {
            $this->error = null; // Reset the error status
        }

        // Get the user (there should only be one)
        $user = array_pop($r['query']['users']);
        unset($r, $data);

        // Abort if missing user
        if (isset($user['missing'])) {
            $this->exists = false;
            $this->invalid = isset($user['cancreateerror']);
            if ($this->invalid) {
                $this->error = $user['cancreateerror'][0]['message'];
            }
            return false;
        }

        // Check that user is present 
        if (!isset($user['missing']) && isset($user['userid'])) {
            // For "old" users registration is null
            if ($user['registration'] == null) {
                $user['registration'] = $this->getFirstEditDate($this->name);
            }

            $this->exists = true;
            $this->invalid = false;
            $this->id = $user['userid'];
            $this->gender = $user['gender'];
            $this->emailable = isset($user['emailable']);
            $this->registration = $user['registration'];
            $this->rights = $user['rights'];
            $this->groups = $user['groups'];
        }
        unset($user);

        return true;
    }

    /**
     * Get the date of the first edit for the user
     * @param type $username
     * @return mixed false if error else date 
     */
    public function getFirstEditDate($username = null) {
        $data = array(
            'list' => 'usercontribs',
            'uclimit' => 1,
            'ucdir' => 'newer',
            'ucuser' => $username == null ? $this->name : $username,
            'ucnamespace' => '',
            'usprop' => 'title|timestamp|comment'
        );

        $r = $this->wikimate->query($data); // Run the query
        // Check for errors
        if (isset($r['error'])) {
            $this->error = $r['error']; // Set the error if there was one
            return false;
        } else {
            $this->error = null; // Reset the error status
        }
        return $r['query']['usercontribs'][0]['timestamp'];
    }

    /*
     *
     * Getter methods
     *
     */

    /**
     * Returns the name of this user.
     *
     * @return  string  The name of this user
     */
    public function getName() {
        return $this->name;
    }

    function getId() {
        return $this->id;
    }

    function getGender() {
        return $this->gender;
    }

    function isEmailable() {
        return $this->emailable;
    }

    function getEditcount() {
        return $this->editcount;
    }

    function getRegistration() {
        return $this->registration;
    }

    function getGroups() {
        return $this->groups;
    }

    function getRights() {
        return $this->rights;
    }

}
