<?php

// include config
$CIVICRM_CONFDIR = '/m/htdocs/gp/sites/';
require_once $CIVICRM_CONFDIR.'all/modules/civicrm/civicrm.config.php';
require_once $CIVICRM_CONFDIR.'default/civicrm.settings.php';
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();

// include api, drupal module, etc.
require_once('gpew_dd_import.module');

$ddms_to_be_imported=CRM_Core_DAO::executeQuery( "
	SELECT *
	FROM gp_data.ddm_all_without_cancelled
	WHERE ConfirmationReference NOT IN (
		SELECT direct_debit_reference_16 FROM ukgr_crm.civicrm_value_external_identifiers_5
	)
");

echo "Going to loop through {$ddms_to_be_imported->N} rows...
";

$test=FALSE;
$report=array();
while($ddms_to_be_imported->fetch()){
//	print_r((array)$ddms_to_be_imported);
	echo '.';
	$report=array_merge($report, ddimport_add_mandate((array)$ddms_to_be_imported, $test));
}
//print_r($report);
include('/m/p/f/functions.php');

atc('/m/Desktop/dd_mandate_report.csv', $report);
