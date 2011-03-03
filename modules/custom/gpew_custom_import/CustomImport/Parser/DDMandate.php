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
require_once 'api/v2/Location.php';
require_once "api/v2/Contact.php";
require_once "api/v2/Membership.php";
require_once "api/v2/MembershipContact.php";
require_once 'DD.php';

define ('CIVICRM_GPEW_DD_MANDATE_TABLE', 'civicrm_value_external_identifiers_5');
define ('CIVICRM_GPEW_DD_MANDATE_ID', 'direct_debit_reference_16');
define ('CIVICRM_GPEW_DD_MANDATE_FREQUENCY', 'payment_frequency_37');


/**
 * class to parse contact csv files
 */
class CustomImport_Parser_DDMandate extends CustomImport_Parser_DD
{
	//DB object containing all candidates
    protected $candidate;

	//array showing results for each candidate as they are processed.
    public $results = array();

    public $db_table;

    public $test = FALSE;
	
	function import(){
		$this->getCanditates();
		while($this->candidate->fetch()){
			$this->initCurrent();
			$this->parseCandidate();
		}
	}
	
	function initCurrent(){
//		print_r($this->candidate);
		unset($this->multiplePossibleContacts);
		unset($this->currentContactArray);
		$this->current=array();
		$this->current['tgp']=$this->candidate->confirmationreference_34;
		$this->current['account_details_validated']=$this->candidate->accountdetailsvalidated_27;
		$this->current['is_test_data']=$this->candidate->istestdata_47;				
		$this->current['start_date']=$this->RapiDataToDate($this->candidate->directdebitstartdate_30);
		$this->current['frequency']=$this->candidate->directdebitfrequency_29;
		$this->current['first_name']=$this->candidate->firstname_2;				
		$this->current['last_name']=$this->candidate->lastname_4;
		$this->current['individual_prefix']=$this->candidate->Title_1;
		$this->current['source']=$this->candidate->referersource_35;
		$this->current['address_line_1']=$this->candidate->addressline_5;
		$this->current['address_line_2']=$this->candidate->addressline_6;
		$this->current['address_line_3']=$this->candidate->addressline_7;
		$this->current['city']=$this->combineFields(array($this->candidate->town_12,$this->candidate->county_13));
		$this->current['postcode']=$this->candidate->postcode_14;
		$this->current['email']=$this->candidate->email_16;				
		$this->current['phone_home']=$this->candidate->hometelephone_17;				
		$this->current['phone_mobile']=$this->candidate->mobiletelephone_18;				
		$this->current['custom_data_1']=$this->candidate->customdata_36;
		$this->current['birth_date']=$this->RapiDataToDate($this->candidate->dateofbirth_19);
		$this->current['dp_text']=$this->candidate->dataprotectiontext_20;


	}

	function combineFields($fieldsToCombine) {
		foreach($fieldsToCombine as $field) {
			$field=trim($field);
			if($field!='') {
				$outputFields[]=$field;
			}
		}
		if(count($outputFields)){
			return implode($outputFields, ',');             
		}
	}       

	
	function setCurrentContactID($contact_id){
		$this->current['contact_id']=$contact_id;
	}
	
	function isValidCandidate() {
	        $valid = TRUE;
	        if($this->getCurrent('start_date') ==''){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')} has no valid start date.");
	                $valid = FALSE;
	        }
	        if($this->getCurrent('account_details_validated')=='FALSE'){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')} not imported: AccountDetailsValidated is FALSE");
	                $valid = FALSE;
	        }
	        if($this->getCurrent('start_date') > new DateTime('+100 days')){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')} not imported: start date more than 100 days in the future.");
	                $valid = FALSE;
	        }
	        if($contact['IsTestData']=='TRUE'){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')} not imported: is test data.");
	                $valid = FALSE;
	        }               
	        return $valid;
	}
	
	
	function parseCandidate(){

		if(!$this->isValidCandidate()){
			return;
		}			
		
		//if the TGP is already in CiviCRM and matched to one contact, nothing else this script needs to do, so return.
		if($this->searchForTGP() != 'none'){
			return;
		}

		// try and find a contact for this TGP number
		$searchFieldSets=array(
			array('first_name','last_name','postcode'),
			array('first_name','last_name','email'),
		);
		foreach($searchFieldSets as $searchFieldSet){
			$this->SearchForContact($searchFieldSet);
			if(is_array($this->currentContactArray)){
				break;
			}
		}
		// if there are multiple possible contacts that could be added at this stage, don't try and go any further
		if($this->multiplePossibleContacts){
			return;
		}
		
		if(!is_array($this->currentContactArray)){
			// you couldn't find a contact! add the person to CiviCRM
			$this->addContactFromMandate();
		}
		
		$this->addTGPToMandate();

		$params[1]=array( $this->currentContactArray['contact_id'], 'Integer');
		$params[2]=array( $this->getCurrent('tgp'), 'String');
		if($this->wantsToBeAMember()){
			$params[3]=array( '1', 'String');
		} else {
			$params[3]=array( '0', 'String');
		}
		$query = "
			UPDATE ".CIVICRM_GPEW_DD_MANDATE_TABLE."
			SET is_membership_payment_55= %3 WHERE entity_id = %1 AND direct_debit_reference_16 = %2";				
		$result = CRM_Core_DAO::executeQuery( $query, $params );
		
		if($this->wantsToBeAMember() AND !$this->isAMember($this->currentContactArray['contact_id'])){
			$this->addMembership();
		} else {
			
			return;
		}
		if($this->couldBeYoungGreen()){
			$this->addToYoungGreens();
		}
	}	
	
	
	function SearchForContact($fields) {
		foreach($fields as $field){
			if(is_array($field)){
				$params[$field[0]]=$this->getCurrent($field[1]);				
			} else {
				$params[$field]=$this->getCurrent($field);				
			}
		}
		$result=civicrm_contact_search($params);
		$searchedFields=implode(', ',array_keys($params));
		if(count($result)==1) {
			$this->currentContactArray=current($result);
			$this->addReportLine('info', "Match found for {$this->getCurrent('tgp')}: {$this->getContactLink(current($result))} (using fields: {$searchedFields}).");
		} elseif(count($result)>1) {
			foreach($result as $contact){
				$contactText[]=$this->getContactLink($contact);
			}
			$contactsText=implode(', ', $contactText);
			$this->addReportLine('warning', "More than one contacts could be assigned TGP number {$this->getCurrent('tgp')}: {$contactsText} (using fields: {$searchedFields}).  Please investigate and add manually.");
			$this->multiplePossibleContacts = TRUE;
		} elseif(count($searchResult)==0) {
			$this->addReportLine('info', "No contact found for TGP number {$this->getCurrent('tgp')} (using fields: {$searchedFields}).");
			return FALSE;
		}
		
		
	}

	function addContactFromMandate() {
		
		// add contact params
		$contact_params=array(
			'contact_type'=>'Individual',
			'individual_prefix'=>$this->getCurrent('individual_prefix'),
			'first_name'=>$this->getCurrent('first_name'),
			'last_name'=>$this->getCurrent('last_name'),
			'source'=>$this->getCurrent('source')
			);

		$birth_date=$this->getCurrent('birth_date');
		if(is_object($birth_date)){
			$contact_params['birth_date'] = $birth_date->format('Y-m-d');
		}

		if($this->getCurrent('dp_text')== 'TRUE') {
			$contact_params['do_not_mail']=1;
		}
		
		// add location params
		$location_params=array(
			'location_type'=>'Home',
			'street_address'=>$this->getCurrent('address_line_1'),
			'supplemental_address_1'=>$this->getCurrent('address_line_2'),
			'supplemental_address_2'=>$this->getCurrent('address_line_3'),
			'city'=>$this->getCurrent('city'),
			'postal_code'=>$this->getCurrent('postcode')
		);
		
		$location_params['email'][]=array(
			'email'=>$this->getCurrent('email')
		);
		
		if($this->getCurrent('phone_home') != ''){
			$location_params['phone'][] = array(
				'phone' => $this->getCurrent('phone_home'),
				'phone_type_id' => 1, // phone
			);
		}	  
		if($this->getCurrent('phone_mobile')!=''){
			$location_params['phone'][] = array(
					'phone' => $this->getCurrent('phone_mobile'),
					'phone_type_id' => 2, // mobile
			);
		}

		if(!$this->test){
			$contact_result=civicrm_contact_add($contact_params);
			
			if($contact_result['is_error']) {
				$this->addReportLine('warning', "Failed to add the contact with TGP number: {$this->getCurrent('tgp')}.");
				//TODO: If this fails, really we should abort the whole process.  In practice, it is unlikely to fail.
			} else {
				$this->currentContactArray=$contact_result; //TODO check that this is as you would expect, i.e. not nested in a result key.
				$this->addReportLine('note', "Added contact ({$this->getContactLink($contact_result)}) for TGP: {$this->getCurrent('tgp')}.");
			}
			$this->setCurrentContactID($contact_result['contact_id']);
			$location_params['contact_id'] = $contact_result['contact_id'];
			$location_result=civicrm_location_add($location_params);
			if($location_result['is_error']) {
				$this->addReportLine('warning', "Could not add location information for contact with TGP number: {$this->getCurrent('tgp')}.");
			}						
		} else {
			$this->addReportLine('note', "Ready to add contact with TGP {$this->getCurrent('tgp')}.");
		}
	}
	
	function addTGPToMandate() {
		if(!$this->test){
			$params = array();
			$query = "
				REPLACE INTO ".
				CIVICRM_GPEW_DD_MANDATE_TABLE."
				SET entity_id='{$this->currentContactArray['contact_id']}',".
				CIVICRM_GPEW_DD_MANDATE_ID."='{$this->getCurrent('tgp')}', ".
				CIVICRM_GPEW_DD_MANDATE_FREQUENCY."='{$this->getCurrent('frequency')}'";				
			$updateResult = CRM_Core_DAO::executeQuery( $query, $params );
		} else {
			$this->addReportLine('note', "Ready to add TGP {$this->getCurrent('tgp')} to contact {$this->getContactLink()}.");
		}
	}

	function couldBeYoungGreen(){
		return $this->getCurrent('birth_date') > new DateTime('-30 years');
	}

	function addToYoungGreens(){
		$params=array(
			'group_id'=>30,
			'contact_id'=>$result['contact_id']
		);
		if(!$this->test){
			$result=civicrm_group_contact_add($params); //TODO test this code!			
			if($result['is_error']) {
				$this->addReportLine('warning', "Failed to add contact with URN {$contact['ConfirmationReference']} to young greens group");
			} else {
				$this->addReportLine('note', "Contact with URN {$contact['ConfirmationReference']} added to young greens group");
			}
		} else {
			$this->addReportLine('note', "Ready to add TGP {$this->getCurrent('tgp')} to young green groups.");
		}
	}	

	function tagAsFreeGift(){
		$params=array(
			'group_id'=>30,
			'contact_id'=>$result['contact_id']
		);
		if(!$this->test){
			$result=civicrm_group_contact_add($params); //TODO test this code!			
			if($result['is_error']) {
				$this->addReportLine('warning', "Failed to add contact with URN {$contact['ConfirmationReference']} to young greens group");
			} else {
				$this->addReportLine('note', "Contact with URN {$contact['ConfirmationReference']} added to young greens group");
			}
		} else {
			$this->addReportLine('note', "Ready to add TGP {$this->getCurrent('tgp')} to young green groups.");
		}
	}

	function addMembership(){
		if(!$this->test){
			$params['contact_id']=$this->currentContactArray['contact_id'];				
			$params['membership_type_id']=1;
			$params['status_id']=9;
			$params['is_override']=1;
			$params['start_date']=$this->getCurrent('start_date')->format('Y-m-d');
			$result=civicrm_membership_contact_create($params); 
			if($result['is_error']) {
				$this->addReportLine('warning', "Failed to add membership for {$this->getContactLink()} (with TGP {$this->getCurrent('tgp')}).");
			} else {
				
				//add custom data to membership to say that it is paid by direct debit
				
				
				$params[1]=array( $result['id'], 'Integer');
				$query = "
					INSERT INTO civicrm_value_membership_information_9
					SET pays_membership_by_direct_debit_54 = 1, entity_id = %1
					ON DUPLICATE KEY UPDATE pays_membership_by_direct_debit_54 = 1;";				
				$result = CRM_Core_DAO::executeQuery( $query, $params );
				

				$this->addReportLine('note', "Membership added for contact TGP {$this->getContactLink()} (with TGP {$this->getCurrent('tgp')}).");
				
			}
		} else {
			$this->addReportLine('note', "Ready to add membership to {$this->getContactLink()} (with TGP {$this->getCurrent('tgp')}).");
		}
		
	}
	
	
	
	
		
		

}


