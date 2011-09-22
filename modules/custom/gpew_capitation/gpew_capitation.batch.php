<?php
require_once '/var/www/drupal.green/sites/all/modules/civicrm/civicrm.config.php';
require_once('/var/www/drupal.green/sites/default/civicrm.settings.php');
require_once 'CRM/Core/Config.php';

$config = CRM_Core_Config::singleton();

require_once('gpew_capitation.module');
require_once('CRM/Core/DAO.php');

ini_set('memory_limit', '1000M');

$query = "
	SELECT
		cc.id, cc.contact_id
	FROM `civicrm_membership` AS cm
	JOIN `civicrm_membership_payment` AS cmp
		ON cm.id=cmp.membership_id
	JOIN `civicrm_contribution` AS cc
		ON cc.id=cmp.contribution_id
	WHERE  cm.membership_type_id=10;
";



$result = CRM_Core_DAO::executeQuery( $query );

print_r($result);

while($result->fetch()){
	gpew_capitation_capitate($result->id, 'look_up', FALSE);
	echo $x++.': '.$result->id.'('.$result->contact_id.')   ';
}

echo "Done.\n";
?>

