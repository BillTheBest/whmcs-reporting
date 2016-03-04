<?php
#####
# WHMCS Custom Cron and Reporting
#
# Written by https://downsouth.hosting
#####
# Needs us to install this script?
# Need custom reports created?
#
# email: hello@downsouth.hosting
#####

// Make sure these php modules are installed
//   pear install Mail
//   pear install Mail_Mime
require_once 'Mail.php';
require_once 'Mail/mime.php';

// Edit this file to match your database setup
include      'custom/config/database.php';

// Edit the variables to match your installation
$baseAdminUrl    = 'https://yourdomain.com/whmcs/admin/';

$smtpFrom    = "you@yourdomain.com";
$smtpTo      = "you@yourdomain.com,you2@yourdomain.com";
$smtpHost    = "smtp.smtphost.com";
$smtpPort    = 25;
// Comment out the $smtpUser & $smtpPass if your server does not require auth (most do)
$smtpUser    = "SMTP User";
$smtpPass    = "SMTP Password";
$smtpHeaders = array (
  'From' => $smtpFrom,
  'To' => $smtpTo,
  'Subject' => $smtpSubject,
  'MIME-Version' => 1,
  'Content-type' => 'text/html;charset=iso-8859-1',
  'Content-Transfer-Encoding' => 'quoted-printable',
);

# Check for cron type and exit if not set, set it otherwise                                                            
if ( ! isset($argv[1]) ) {
  echo "Usage: custom.php <cron type>\n";
  exit(1);           
  } else {             
    $cronType = $argv[1];
  }
}

// Leave me alone
$smtpSubject = 'WHMCS - Custom ' . $cronType . ' Cron';
$crlf = "\n";
$body = '';


// Here we can run any specified cron type we like
// We have included a couple sample scripts for you already/
switch( $cronType ) {

  case 'hourly':

    break;

  case 'daily':

    include 'custom/daily/closed_tickets_yesterday.php';
    break;

  case 'weekly':
    include 'custom/weekly/closed_tickets_weekly.php';

    break;

  case 'monthly':
    include 'custom/monthly/closed_tickets_monthly.php';

    break;

  default:
    echo 'Incorrect parameters supplied.  Exiting';
    exit(1);
}

if ( $body === '' ) {
  exit(0);
} else {
  $mime    = new Mail_mime($crlf);
  $mime->setHTMLBody($body);
  $body    = $mime->get();
  $headers = $mime->headers( $smtpHeaders );

  if (is_set($smtpUser))
  {
    $smtp =& Mail::factory('smtp',array('host' => $smtpHost, 'port' => $smtpPort, 'username' => $smtpUser, 'password' => $smtpPass));
  }else{
    $smtp =& Mail::factory('smtp',array('host' => $smtpHost, 'port' => $smtpPort, 'auth' => false));
  }

  try {

    $mail = $smtp->send($smtpTo, $smtpHeaders, $body);

    # Connection failed.  Bail out, gracefully.
    if ( PEAR::isError( $mail ) ) {
      $e = $mail->getMessage();
      throw new Exception( $e );
    }

  } catch ( Exception $e ) {
      echo 'Exception caught: ' . $e->getMessage() . "\n";
      exit(1);
  }
}
?>
