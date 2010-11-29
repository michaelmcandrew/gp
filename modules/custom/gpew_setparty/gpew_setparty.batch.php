<?php

// include config
$CIVICRM_CONFDIR = '/m/htdocs/gp/sites/';
require_once $CIVICRM_CONFDIR.'all/modules/civicrm/civicrm.config.php';
require_once $CIVICRM_CONFDIR.'default/civicrm.settings.php';
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();

// include api, drupal module, etc.
require_once('gpew_setparty.module');
require_once('../civimapit/civimapit.module');
require_once 'api/v2/Contact.php';




$ContactsToBeUpdated=CRM_Core_DAO::executeQuery( "SELECT cc.id as id, postal_code
FROM civicrm_contact AS cc
LEFT JOIN civicrm_value_gpew_party_information AS cvgpi ON cc.id = cvgpi.entity_id
JOIN civicrm_address AS ca ON ca.contact_id=cc.id
WHERE (postal_code IS NOT NULL AND postal_code!='') AND cvgpi.id IS NULL LIMIT 10;");

while($ContactsToBeUpdated->fetch()){
	print_r($ContactsToBeUpdated->id."\n");
	civimapit_updateContactAreaInfo($ContactsToBeUpdated->id,$ContactsToBeUpdated->postal_code);
	gpew_setparty_set_party($ContactsToBeUpdated->id);
	sleep(3);
}