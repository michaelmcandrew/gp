<?php
require_once '/var/www/drupal.green/sites/all/modules/civicrm/civicrm.config.php';
require_once('/var/www/drupal.green/sites/default/civicrm.settings.php');
require_once 'CRM/Core/Config.php';

$config = CRM_Core_Config::singleton();

require_once('gpew_capitation.module');
require_once('CRM/Core/DAO.php');

ini_set('memory_limit', '1000M');

$query = "
SELECT cc.id, contact_id
FROM civicrm_contribution AS cc
JOIN civicrm_contribution_type AS cct ON cc.contribution_type_id=cct.id
JOIN is_numbers ON is_numbers.`Item Number`=cct.name
WHERE is_numbers.import='m'
";

$result = CRM_Core_DAO::executeQuery( $query );

print_r($result);

while($result->fetch()){
	gpew_capitation_capitate($result->id, 'look_up', TRUE);
	echo $x++.': '.$result->id.'('.$result->contact_id.')   ';	
}

echo "Done.\n";
?>
