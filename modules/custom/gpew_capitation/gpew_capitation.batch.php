<?php
require_once '/var/www/gp/sites/all/modules/civicrm/civicrm.config.php';
require_once('/var/www/gp/sites/gp.local/civicrm.settings.php');
require_once 'CRM/Core/Config.php';

$config = CRM_Core_Config::singleton();

require_once('gpew_capitation.module');
require_once('CRM/Core/DAO.php');

ini_set('memory_limit', '1000M');

$query = "
SELECT cc.id
FROM civicrm_contribution AS cc
WHERE contribution_type_id=2
";

$result = CRM_Core_DAO::executeQuery( $query );

print_r($result);

while($result->fetch()){
	gpew_capitation_capitate($result->id, 'look_up', FALSE);
	echo $x++.':'.$result->id.'   ';
}

echo "Done.\n";
?>
