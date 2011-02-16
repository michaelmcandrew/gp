<?php
define( 'CIVICRM_CONFDIR', '/var/www/gp/sites' );
require_once '/m/htdocs/gp/sites/all/modules/civicrm/civicrm.config.php';
require_once('/m/htdocs/gp/sites/gp.local/civicrm.settings.php');
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();

require_once('gpew_new_member_notification.module');
require_once('CRM/Core/DAO.php');

ini_set('memory_limit', '1000M');
$params=array();
$query = "select contact_id, civicrm_membership.start_date, civicrm_membership.end_date from civicrm_membership_log join civicrm_membership ON civicrm_membership.id=membership_id where modified_date > '2011-01-12' and civicrm_membership.status_id=1 group by contact_id";
$membs = CRM_Core_DAO::executeQuery( $query, $params );
while($membs->fetch()){
 	gpew_new_member_notification_notify($membs);
 	echo '.';
}
// echo "Done.\n";
?>


SELECT contact_id, civicrm_membership.start_date, civicrm_membership.end_date
FROM civicrm_membership_log
JOIN civicrm_membership ON civicrm_membership.id=membership_id
JOIN civicrm_contact ON civicrm_contact.id=civicrm_membership.contact_id
WHERE modified_date > '2011-01-12'
AND civicrm_membership.status_id=1
AND civicrm_contact.is_deleted!=1
GROUP BY contact_id;
