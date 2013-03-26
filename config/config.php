<?php

/**
 * Configuration file for the site
 *
 * Defines all the necessary settings for the site to operate as it does.
 *
 */

define('SITE_URL', 'http://tophat.dev');
define('SITE_HUMAN_URL', 'TopHat.dev');

define('SITE_PATH','/mnt/hgfs/UbuntuDocumentRoot/topHat/');

/**
 * Set the mode for the site
 *   Options:
 *		DEVELOPMENT
 *			To test things in more like debug mode on a dev server
 * 		TESTING
 *			To test things as they would be on production
 * 		PRODUCTION
 *			Ready for live settings
 */
define('MODE', 'DEVELOPMENT');

define('PROFILE_PERCENT', 100);

/**
 *	Cache Settings
 *
 */
define("CACHE_OBJ_EXPIRE", 10); // 10 seconds
define("CACHE_OBJ_EXPIRE_LONG", 30); // 30 seconds
define("CACHE_OBJ_EXPIRE_LONGER", (60*60)*12); // 12 hours


/**
 * Mode specific configurations
 *	All options that change per site mode.
 */
if(MODE == 'DEVELOPMENT'){
	error_reporting(E_ALL & ~E_DEPRECATED);
	// Jame's Virtual Server:
	define('DB_HOST','localhost');
  define('DB_USER','tophat');
  define('DB_PASS','super');
	define('DB_DATA','tophat');

	define('CONTACT_EMAIL', 'contact-us@tophat.dev');

}
elseif(MODE == 'TESTING'){
	error_reporting(E_ALL & ~E_DEPRECATED);
	// Jame's Virtual Server:
	define('DB_HOST','localhost');
  define('DB_USER','tophat');
  define('DB_PASS','super');
	define('DB_DATA','tophat');

	define('CONTACT_EMAIL', 'contact-us@tophat.dev');
}
elseif(MODE == 'PRODUCTION'){
	error_reporting(E_ALL & ~E_DEPRECATED);
	// Jame's Virtual Server:
	define('DB_HOST','localhost');
  define('DB_USER','tophat');
  define('DB_PASS','super');
	define('DB_DATA','tophat');

	define('CONTACT_EMAIL', 'contact-us@tophat.dev');

}else{
	/// If we were not in one of the given modes, we must die.
	die('Please set the mode to either DEVELOPMENT, TESTING, or PRODUCTION in the configuration file.');
}
