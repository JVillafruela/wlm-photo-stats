<?php
require_once __DIR__ . '/vendor/autoload.php';

define('COUNTRY','fr');
define('LANG','fr');
define('YEAR',2017);

define('WLM_CATEGORY','Images from Wiki Loves Monuments 2017 in France');

define('API_COMMONS','https://commons.wikimedia.org/w/api.php');
//
// stats database
define('DBS_HOST','localhost');
define('DBS_NAME','wlmstats');
define('DBS_USER','wlmstats');
define('DBS_PWD','wlmstats');

// monuments database
define('DBM_HOST','localhost');
define('DBM_NAME','monumentsdb');
define('DBM_USER','wlmstats');
define('DBM_PWD','wlmstats');

// Idiorm configuration
ORM::configure('mysql:host='. DBS_HOST .';dbname='. DBS_NAME);
ORM::configure('username', DBS_USER);
ORM::configure('password', DBS_PWD);

ORM::configure('mysql:host='. DBM_HOST .';dbname='. DBM_NAME, null, 'monumentsdb');
ORM::configure('username', DBM_USER, 'monumentsdb');
ORM::configure('password', DBM_PWD, 'monumentsdb');

ORM::configure('id_column_overrides', array(
    'monuments_all' => array('country','lang','id')
    ),'monumentsdb');
