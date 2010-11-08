<?php
require_once '/Users/michaelmcandrew/htdocs/gp/sites/all/modules/civicrm/civicrm.config.php';
require_once('/Users/michaelmcandrew/htdocs/gp/sites/gp.local/civicrm.settings.php');
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();

require_once('civigp_capitation.module');
require_once('CRM/Core/DAO.php');

$query = "
SELECT cc.id AS id, lower(source) AS frequency
FROM civicrm_contribution AS cc
JOIN ukgr_crm.civicrm_contribution_type AS cct ON cc.contribution_type_id=cct.id
JOIN gp_data.is_numbers AS is_numbers ON cct.name= is_numbers.`Item Number`
WHERE import = 'm'
";

$result = CRM_Core_DAO::executeQuery( $query );

echo "Updating area information.\n";

while($result->fetch()){
	civigp_capitation_capitate($result->id,$result->frequency);
	echo '.';
}
echo "Done.\n";
?>