<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Import/Parser.php';
require_once 'api/v2/utils.php';

define ('CIVICRM_GPEW_DD_MANDATE_TABLE', 'civicrm_value_external_identifiers_5');
define ('CIVICRM_GPEW_DD_MANDATE_ID', 'direct_debit_reference_16');
define ('CIVICRM_GPEW_DD_MANDATE_FREQUENCY', 'payment_frequency_37');


/**
 * class to parse contact csv files
 */
class CustomImport_Parser_DD
{
		//DB object containing all candidates
	    protected $candidate;

		//array showing results for each candidate as they are processed.
	    public $results = array();

	    public $db_table;

	    public $test = FALSE;
	
		function getReport(){
			return $this->report;
		}

		function getCanditates(){
			$params = array();
			$this->candidate = CRM_Core_DAO::executeQuery("SELECT * FROM {$this->db_table}",$params);
		}
		
		function RapiDataToDate($d){

			if(strlen($d==0)){
				return;
			}

			$day = substr($d,0,2);
			$month = substr($d,3,2);
			$year = substr($d,6,4);

			// $day = '01';
			// $month = '01';
			// $year = '2001';

			// echo "day: $day\n";
			// echo "month: $month\n";
			// echo "year: $year\n";

			$DateTime = new DateTime("$month/$day/$year");

			return $DateTime;
		}
		
		function isAMember($contact_id){
			require_once 'api/v2/Membership.php';
			$params = array( 'contact_id' => $contact_id);
		  	$result = civicrm_membership_contact_get($params);
			if(!is_array(current($result))){
				return FALSE;
			}
			return TRUE;

		}
		
		function getCurrent($var){
			return $this->current[$var];
		}
		
		function wantsToBeAMember(){
			return (substr_count($this->getCurrent('source'), 'member') == 0) AND $this->getCurrent('custom_data_1')=='No';
		}
		
		function searchForTGP() {
			$contactParams=array('custom_16'=>$this->getCurrent('tgp'));
			require_once "api/v2/Contact.php";
			$searchResult=civicrm_contact_search($contactParams);
			
			if(count($searchResult)==1) {
				$this->currentContactArray=current($searchResult);
				$this->getContactLink($searchResult);
				$this->addReportLine('info', "{$this->getCurrent('tgp')} (already) matched to one CiviCRM contact {$this->getContactLink(current($searchResult))}");
				$TGPQueryParams[1] = array( $this->currentContactArray['civicrm_value_external_identifiers_5_id'], 'Integer');
//				print_r($TGPQueryParams);
				$TGPQuery = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_value_external_identifiers_5 WHERE id=%1", $TGPQueryParams);
				$TGPQuery->fetch();
				$this->current['is_membership_tgp']=TRUE;
				return 'one';
			} elseif(count($searchResult)>1) {
				foreach($searchResult as $contact){
					$contactText[]=$this->getContactLink($contact);
				}
				$contactsText=implode(', ', $contactText);
				$this->addReportLine('warning', "More than one contacts have TGP number {$this->getCurrent('tgp')} ({$contactsText}).");
				return 'more than one';
			} elseif(count($searchResult)==0) {
				$this->addReportLine('info', "No contact has TGP number {$this->getCurrent('tgp')}.");
				return 'none';
			}

		}
		
		function isMembershipTGP(){
			
		}
		
		function getContactLink($contact='current') {
			if($contact=='current'){
				$contact=$this->currentContactArray;			
			}
			if(!is_array($contact)){
				return "'test contact'";
			} else {
				return "<a href='/civicrm/contact/view?cid={$contact['contact_id']}'>{$contact['display_name']} ({$contact['contact_id']})</a>";
			}
		}
		
		function addReportLine($type, $message){
			$this->report[]=array('type'=>$type,'message'=>$message);
		}
		
		
		
}


