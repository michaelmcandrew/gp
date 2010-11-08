<?php
require_once '/m/htdocs/gp/sites/all/modules/civicrm/civicrm.config.php';
require_once('/m/htdocs/gp/sites/gp.local/civicrm.settings.php');
require_once 'CRM/Core/Config.php';

$config = CRM_Core_Config::singleton();

require_once('civigp_capitation.module');
require_once('CRM/Core/DAO.php');

ini_set('memory_limit', '1000M');

$query = "
SELECT cc.id AS id, contact_id, lower(source) AS frequency
FROM civicrm_contribution AS cc
JOIN ukgr_crm.civicrm_contribution_type AS cct ON cc.contribution_type_id=cct.id
JOIN gp_data.is_numbers AS is_numbers ON cct.name= is_numbers.`Item Number`
WHERE import = 'm' AND id NOT IN (SELECT entity_id FROM `civicrm_value_capitation_4`)
";

$result = CRM_Core_DAO::executeQuery( $query );

echo "Updating area information.\n";

while($result->fetch()){
	if(civigp_capitation_is_current_member($result->contact_id)){	
		civigp_capitation_capitate($result->id,$result->frequency);
		echo '.';
	}
}
echo "Done.\n";
?>