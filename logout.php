<?php

/**
 * Log out a user
 */

// Initialisation
require_once('includes/init.php');

Auth::getInstance()->logout();

// Redirect to home page
Util::redirect(Config::APP_PATH.'/index.php');

?>
