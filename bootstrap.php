<?php
/**
 * Bootstrap file.
 *
 *	Loads before all other files on anything that is being
 * 	handled by the router. (So not on images and things in
 * 	the htdocs folder 
 *
 *	Loads necessary configs, routes, and helpers needed globally
 */

require_once('/mnt/hgfs/UbuntuDocumentRoot/topHat/config/config.php');

require_once('config/routes.php');

require_once('system/model.php');
require_once('system/model_list.php');
require_once('system/model_through.php');

require_once('system/controller.php');


require_once('helpers/utility.php');
require_once('system/utilities/router.php');
require_once('system/utilities/inflector.php');
require_once('system/utilities/profiler.php');


define("PROFILE_SESSION", rand(1,100)<=PROFILE_PERCENT);	

$__profiler = new Profiler(TRUE, PROFILE_SESSION);

$__memcache = new Memcached();
$__memcache->addServer('localhost', 11211);
print_r($__memcache->getVersion());


// require_once('Mail/Queue.php');
// require_once('Mail/Queue/Container/mdb2.php');
// include_once('Net/SMTP.php');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

session_start();
