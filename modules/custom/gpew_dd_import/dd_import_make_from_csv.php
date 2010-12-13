<?php
include_once('/m/p/f/functions.php');
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$oldDB = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
$dbs['import']=$dbname='gp_data';
// cts('/m/p/gp/data/dd/ddm_all.csv', 'ddm_all', 'gp_data');
// cts('/m/p/gp/data/dd/ddm_cancelled.csv', 'ddm_cancelled', 'gp_data');
cts('/m/p/gp/data/dd/tgp_from_myob_notes.csv', 'tgp_from_myob_notes', 'gp_data');

