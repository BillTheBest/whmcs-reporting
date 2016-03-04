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

$userUrl = $baseUrl . 'clientssummary.php?userid=';
$ticketUrl = $baseUrl . 'supporttickets.php?action=view&id=';
$yesterday = date("Y-m-d", time() - 60 * 60 * 24);

// Check for tickets closed yesterday
$status = 'Closed';
$lastreply = $yesterday . '%';
$query= "
  SELECT t.title, t.userid, t.id, t.date, t.lastreply, t.admin, c.companyname, c.firstname, c.lastname
  FROM whmcs.tbltickets as t
  JOIN whmcs.tblclients as c
  ON c.id=t.userid
  WHERE t.status = 'Closed'
  AND t.lastreply LIKE '$lastreply'
  ORDER BY t.userid, t.date
";

try {

  $result = $link->query( $query );

  # Query failed. Bail out, gracefully.
  if ( ! $result ) {
    $e = $link->error;
    throw new Exception( $e );
  }

} catch ( Exception $e ) {
  echo 'Exception caught: ' . $e->getMessage() . "\n";
  exit(1);
}

$body .= "<h1>Tickets Closed Yesterday</h1>\n";
$body .= "<table>\n";
$body .= "  <thead>\n";
$body .= "    <tr>\n";
$body .= "      <th>Ticket Opened</th>\n";
$body .= "      <th>Client</th>\n";
$body .= "      <th>Subject</th>\n";
$body .= "      <th>Employee</th>\n";
$body .= "      <th>Ticket Closed</th>\n";
$body .= "    </tr>\n";
$body .= "  </thead>\n";
$body .= "  <tbody>\n";

foreach( $result as $r ) {
  $query = "SELECT admin from tblticketreplies WHERE tid=" . $r['id'] . " ORDER BY id DESC LIMIT 1";
  try {
    $admin = $link->query( $query );
    if ( ! $admin ) {
      $e = $link->error;
      throw new Exception( $e );
    }
  } catch ( Exception $e ) {
    echo 'Exception caught: ' . $e->getMessage() . "\n";
    exit(1);
  }
  foreach ($admin as $a) {
    $adminName = $a['admin'];
  }
  $body .= "    <tr>\n";
  $body .= "      <td>" . $r['date'] . "</td>\n";
  $body .= "      <td><a href=\"" . $userUrl . $r['userid']  . "\" target=\"_blank\">" . $r['companyname'] . "</a></td>\n";
  $body .= "      <td><a href=\"" . $ticketUrl . $r['id'] . "\" target=\"_blank\">" . $r['title'] . "</a></td>\n";
  $body .= "      <td>" . $adminName . "</td>\n";
  $body .= "      <td>" . $r['lastreply'] . "</td>\n";
  $body .= "    </tr>\n";
}

$body .= "  </tbody>\n";
$body .= "</table>\n";
$body .= "<br><br>";
