<?php

define('FRAMEWORK_VERSION', '1.1.012');
define('CATALONIAFW_VERSION', 'v. '.FRAMEWORK_VERSION);
define('CATALONIAFW_URL', 'http://www.cataloniaframework.com/');


define('CHARSET','UTF-8');
ini_set('default_charset', 'utf-8');
setlocale(LC_ALL, 'en_US.UTF8');
date_default_timezone_set('Europe/Andorra');

set_time_limit(40);

// Change this in order to start working
define('FIRST_TIME', true);
if (FIRST_TIME == true) {
    require '../views/errors/catfw_firsttime.php';
    Core::end();
}

define('DEVELOPMENT', 'DEVELOPMENT');
define('PRODUCTION', 'PRODUCTION');
define('PREPRODUCTION', 'PREPRODUCTION');

// Change this line for Production
define('ENVIRONMENT', DEVELOPMENT);

if (ENVIRONMENT == DEVELOPMENT) {
	require_once 'development.php';
}
if (ENVIRONMENT == PRODUCTION) {
	require_once 'production.php';
}
if (ENVIRONMENT == PREPRODUCTION) {
	require_once 'preproduction.php';
}

// Define a prefix for all the includes
define('WEB_ROOT', $st_server_config['storage']['web_root']);
define('LOGS_ROOT', $st_server_config['storage']['logs']);
define('CATFW_ROOT', $st_server_config['storage']['catfw_root']);
define('CATFW_CORE_ROOT', CATFW_ROOT.'catfwcore/');
define('CLASSES_ROOT', $st_server_config['storage']['classes_root']);
define('CONTROLLERS_ROOT', CATFW_ROOT.'controllers/');
define('MODELS_ROOT', CATFW_ROOT.'models/');
define('VIEWS_ROOT', CATFW_ROOT.'views/');
define('CUSTOM_INIT_ROOT', CATFW_ROOT.'init/');
define('TRANSLATIONS_ROOT', CATFW_ROOT.'translations/');
define('CACHE_ROOT', CATFW_ROOT.'cache/');

// To write errors if access to DB fails.
define('LOG_SQL_FILE', LOGS_ROOT.'sql.log');
define('LOG_ERROR_FILE', LOGS_ROOT.'error.log');
// TTL for Cache in getEvent in seconds
define('CACHE_TTL_GETEVENT', 60*60);

// If MULTILANG is true, then urls will be like http://www.cataloniafw.com/ca/controller/action/param1/value1...
// Otherwise will be http://www.cataloniafw.com/controller/action/param1/value1...
define('MULTILANG', true);

require_once CATFW_CORE_ROOT.'db.class.php';   // For Db constants used in config

if (ENVIRONMENT == DEVELOPMENT) {
	require_once 'development_db.php';
}
if (ENVIRONMENT == PRODUCTION) {
	require_once 'production_db.php';
}
if (ENVIRONMENT == PREPRODUCTION) {
	require_once 'preproduction_db.php';
}

$st_array_navigation = Array();
