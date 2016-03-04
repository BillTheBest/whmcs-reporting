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

$lastMonth = date('Y-m', strtotime('last month'));
$lastMonthQueryString = $lastMonth . '-%';
$employeeMap = array();

$body .= "<h1>Tickets Closed Last Month $lastMonth</h1>\n";
$body .= "<table>\n";
$body .= "  <thead>\n";
$body .= "    <tr>\n";
$body .= "      <th>Employee</th>\n";
$body .= "      <th>Ticket Closed</th>\n";
$body .= "    </tr>\n";
$body .= "  </thead>\n";
$body .= "  <tbody>\n";

// build array of employees first and last names concatenated
// this is how tickets are tracked in tblticketreplies with the 'admin' field
// not by admin id...
$employeeQuery = "SELECT firstname, lastname FROM tbladmins";
try {
  $employeeResult = $link->query($employeeQuery);

  if(! $employeeResult) {
    $e = $link->error;
    throw new Exception($e);
  }

} catch (Exception $e) {
  echo 'Exception caught: ' . $e->getMessage() . "\n";
  exit(1);
}
foreach( $employeeResult as $r ) {
  $admin = $r['firstname'] . " " . $r['lastname'];
  $employeeMap[$admin] = 0;
}

$monthQuery= "
  SELECT t.title, t.userid, t.id, t.date, t.lastreply, t.admin, c.companyname, c.firstname, c.lastname
  FROM whmcs.tbltickets as t
  JOIN whmcs.tblclients as c
  ON c.id=t.userid
  WHERE t.status = 'Closed'
  AND t.lastreply LIKE '$lastMonthQueryString'
  ORDER BY t.userid, t.date
";

try {
  $monthResult = $link->query($monthQuery);

  if(! $monthResult) {
    $e = $link->error;
    throw new Exception($e);
  }

} catch (Exception $e) {
  echo 'Exception caught: ' . $e->getMessage() . "\n";
  exit(1);
}

foreach( $monthResult as $r ) {
  $repliesQuery = "SELECT admin from tblticketreplies WHERE tid=" . $r['id'] . " ORDER BY id DESC LIMIT 1";
  try {
    $repliesResult = $link->query($repliesQuery);

    if(! $repliesResult) {
      $e = $link->error;
      throw new Exception($e);
    }

  } catch (Exception $e) {
    echo 'Exception caught: ' . $e->getMessage() . "\n";
    exit(1);
  }

  foreach ($repliesResult as $a) {
    $employeeMap[$a['admin']] = $employeeMap[$a['admin']] + 1;
  }
}

$x = 0;
foreach($employeeMap as $employee => $count) {
  $body .= "    <tr>\n";
  $body .= "      <td><center>" . $employee . "</center></td>\n";
  $body .= "      <td><center>" . $count . "</center></td>\n";
  $body .= "    </tr>\n";
}
$body .= "  </tbody>\n";
$body .= "</table>\n";
$body .= "<br><br>";
