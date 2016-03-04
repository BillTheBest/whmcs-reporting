<?php
#####
## WHMCS Custom Cron and Reporting
##
## Written by https://downsouth.hosting
######
## Needs us to install this script?
## Need custom reports created?
##
## email: hello@downsouth.hosting
######

// MySQL only at this time.  Feel free to fork and make a pull request!

// Global Variables
$vars = array(
    'db_host' => 'localhost',
    'db_user' => 'username',
    'db_pass' => 'password',
    'db_name' => 'whmcs',
  );

// Build our database connection
try {

  $link = new mysqli( $vars['db_host'], $vars['db_user'], $vars['db_pass'], $vars['db_name'] );

  // Connection failed.  Bail out, gracefully.
  if ( $link->connect_errno ) {
    $e = $link->connect_error();
    throw new Exception( $e );
  }

} catch ( Exception $e ) {
    echo 'Exception caught: ' . $e->getMessage() . "\n";
    exit(1);
}
