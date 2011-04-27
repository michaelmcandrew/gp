<?php
define( 'CIVICRM_CONFDIR', '/var/www/drupal.green/sites' );
require_once CIVICRM_CONFDIR.'/all/modules/civicrm/civicrm.config.php';
require_once(CIVICRM_CONFDIR.'/default/civicrm.settings.php');
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();
require_once('civimapit.module');
require_once('api/v2/EntityTag.php');
require_once CIVICRM_CONFDIR.'/all/modules/custom/gpew_setparty/gpew_setparty.module';


$updateTagId=30;

$query = "
SELECT
contact_id,
postal_code,
0 as tagged
FROM `civicrm_address`
LEFT JOIN civicrm_value_area_information ON entity_id = contact_id
WHERE is_primary AND contact_id AND postal_code IS NOT NULL AND entity_id IS NULL
UNION
SELECT
contact_id,
postal_code,
1 as tagged
FROM `civicrm_address`
LEFT JOIN civicrm_entity_tag ON entity_id = contact_id
WHERE is_primary AND contact_id AND postal_code IS NOT NULL AND tag_id = $updateTagId
";
	
require_once('CRM/Core/DAO.php');
$params=array();
$result = CRM_Core_DAO::executeQuery( $query, $params );
while($result->fetch()){
	print_r($result);
	civimapit_updateContactAreaInfo($result->contact_id,$result->postal_code);
	gpew_setparty_set_party($result->contact_id);
	if($result->tagged){
		$params = array(
			'contact_id' => $result->contact_id,
			'tag_id'   => $updateTagId,
		);
		civicrm_entity_tag_remove( $params );
	}
	sleep(3);
	echo '.';
}
echo "Done.\n";
?>