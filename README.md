# WHMCS - Custom Cron & Reporting

### Background
Sometimes you need to get data out of WHMCS at regular intervals.  This custom cron solution will allow you to run your tasks hourly, daily, weekly, or monthly and deliver an email with the data you seek.  Sometimes you need a bit more power and flexibility from the standard reports in WHMCS.  With this custom framework you can build out custom queries as deep as you like within the WHMCS database.

### Features
  - Scheduled task intervals configured in system cron
  - Mysql support
  - Ability to query any and all data in the WHMCS database

### Requirements
  - PHP, MySQLi, system cron
  - php Mail & Mail_Mime
    - pear install Mail
    - pear install Mail_Mime

### Installation
  - **https://downsouth.hosting can get this installed for you for $20**
    - Email: ```hello@downsouth.hosting```
  - Edit custom.php and set the following variables:
    - ```$baseAdminUrl``` - The url to your WHMCS admin directory **with** trailing slash
    - ```$smtpFrom``` - From address used when sending email
    - ```$smtpTo``` - Comma separated list of emails you want the email delivered to
    - ```$smtpHost``` - Hostname of your SMTP server (Use something like sendgrid.com)
    - ```$smtpUser``` - Username to authenticate to your mail service
    - ```$smtpPass``` - Password to authenticate to your mail service
  - Edit custom/config/database.php
    - update the ```$vars``` array with your database login info
  - Upload to your WHMCS cron directory.
    - Upload ```custom.php``` and the ```custom directory```
  - Configure cron to run custom.php for each interval you want (examples below)
    - ```0 * * * * cd /path/to/whcms/crons/; php -q custom.php hourly # hourly, on the hour```
    - ```0 7 * * * cd /path/to/whcms/crons/; php -q custom.php daily # daily 7am```
    - ```5 7 * * 1 cd /path/to/whcms/crons/; php -q custom.php weekly # weekly 7:05am```
    - ```10 7 1 * * cd /path/to/whcms/crons/; php -q custom.php monthly # monthly 7:10am```

### Custom Reports
  - Need custom reports? contact us for a quote! ```hello@downsouth.hosting```

Included are 3 custom reports to get you started with examples.  Feel free to use them or remove them from ```custom.php``` if you don't need them.

  - ```custom/daily/closed_tickets_yesterday.php``` - List of admins and how many tickets they closed yesterday.
  - ```custom/weekly/closed_tickets_weekly.php``` - Same as about, but a weekly report.
  - ```custom/monthly/closed_tickets_monthly.php``` - Same as previous, but a montly report.
