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

class arbitraryClass {};

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
		$this->current['postal_code']=$this->candidate->postcode_14;
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
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')} has no valid start date (not imported).");
	                $valid = FALSE;
	        }
	        if($this->getCurrent('account_details_validated')=='FALSE'){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')}: AccountDetailsValidated is FALSE (not imported).");
	                $valid = FALSE;
	        }
	        if($this->getCurrent('start_date') > new DateTime('+100 days')){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')}: start date more than 100 days in the future (not imported).");
	                $valid = FALSE;
	        }
	        if($contact['IsTestData']=='TRUE'){
	                $this->addReportLine('warning', "{$this->getCurrent('tgp')}: is test data (not imported).");
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
			array('first_name','last_name','postal_code'),
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
			//'i could not find the person and want to do some more looser searching and report what i find but not do anything';
			$searchFieldSets=array(
				array('last_name','postal_code'),
				array('last_name','email'),
			);
			foreach($searchFieldSets as $searchFieldSet){
				$this->SearchForContact($searchFieldSet, 'interesting');
				unset($this->currentContactArray);
			}
			$this->addContactFromMandate();
			$this->contact_was_added_to_civicrm=TRUE;
		}
		
		$this->addTGPToMandate();
		if(!$this->contact_was_added_to_civicrm==TRUE){
			//if the contact wasn't added to CiviCRM, we must have updated an already existing contact.  Therefore, we need to do some updating
			$this->updateDetails();
		}

		//if this is for real, we need to say whether or not this is intended as a membership payment
		if(!$this->test){

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
		}
		if($this->wantsToBeAMember()){
			if($this->isAMember($this->currentContactArray['contact_id'])){
				$membership_id=$this->isAMember($this->currentContactArray['contact_id'], TRUE);
				$this->updateCustomMembershipData($membership_id, $this->getCurrent('frequency'));
				//set there membership status to DD pending				
			}else{
				$this->addMembership();
			}
		}
		if($this->couldBeYoungGreen()){
			$this->addToYoungGreens();
		}
	}	
	
	
	function SearchForContact($fields, $message='info') {
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
			$this->addReportLine($message, "Match found for {$this->getCurrent('tgp')}: {$this->getContactLink(current($result))} (using fields: {$searchedFields}).");
		} elseif(count($result)>1) {
			foreach($result as $contact){
				$contactText[]=$this->getContactLink($contact);
			}
			$contactsText=implode(', ', $contactText);
			$this->addReportLine('warning', "More than one contacts could be assigned payment integration reference {$this->getCurrent('tgp')}: {$contactsText} (using fields: {$searchedFields}).  Please investigate and add manually.");
			$this->multiplePossibleContacts = TRUE;
		} elseif(count($searchResult)==0) {
			$this->addReportLine('info', "No contact found for payment integration reference {$this->getCurrent('tgp')} (using fields: {$searchedFields}).");
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
		$address=array(
			'location_type_id'=>1,
			'street_address'=>$this->getCurrent('address_line_1'),
			'supplemental_address_1'=>$this->getCurrent('address_line_2'),
			'supplemental_address_2'=>$this->getCurrent('address_line_3'),
			'city'=>$this->getCurrent('city'),
			'postal_code'=>$this->getCurrent('postal_code'),
			'version'=>3
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
				$this->addReportLine('warning', "Failed to add the contact with payment integration reference: {$this->getCurrent('tgp')}.");
				//TODO: If this fails, really we should abort the whole process.  In practice, it is unlikely to fail.
			} else {
				$this->currentContactArray=$contact_result; //TODO check that this is as you would expect, i.e. not nested in a result key.
				$this->addReportLine('note', "Added contact ({$this->getContactLink($contact_result)}) for TGP: {$this->getCurrent('tgp')}.");
			}
			$this->setCurrentContactID($contact_result['contact_id']);
			$address['contact_id']=$location_params['contact_id'] = $contact_result['contact_id'];
			
			$location_result=civicrm_location_add($location_params);
			//the very beginnings of the move to civicrm api v3 :)
			$result=civicrm_api('address', 'create', $address);
			if($location_result['is_error']) {
				$this->addReportLine('warning', "Could not add location information for contact with payment integration reference: {$this->getCurrent('tgp')}.");
			}
			
			$object=new arbitraryClass;
			
			//at this point, we should update the contact info again so that the area and party info gets set
			civimapit_civicrm_post( 'edit', 'Individual', $contact_result['contact_id'], $object );
			gpew_setparty_civicrm_post( 'edit', 'Individual', $contact_result['contact_id'], $object );
			
			
									
		} else {
			$this->addReportLine('note', "Ready to add contact with payment integration reference {$this->getCurrent('tgp')}.");
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
			$this->addReportLine('note', "Ready to add payment integration reference {$this->getCurrent('tgp')} to contact {$this->getContactLink()}.");
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
				$this->addReportLine('warning', "Failed to add contact with payment integration reference {$contact['ConfirmationReference']} to young greens group");
			} else {
				$this->addReportLine('note', "Contact with payment integration reference {$contact['ConfirmationReference']} added to young greens group");
			}
		} else {
			$this->addReportLine('note', "Ready to add contact with payment integration reference {$this->getCurrent('tgp')} to young green groups.");
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
				$this->addReportLine('warning', "Failed to add contact with payment integration reference {$contact['ConfirmationReference']} to young greens group");
			} else {
				$this->addReportLine('note', "Contact with payment integration reference {$contact['ConfirmationReference']} added to young greens group");
			}
		} else {
			$this->addReportLine('note', "Ready to add payment integration reference {$this->getCurrent('tgp')} to young green groups.");
		}
	}

	function addMembership(){
		if(!$this->test){
			$params['contact_id']=$this->currentContactArray['contact_id'];				
			$params['membership_type_id']=1;
			$params['status_id']=9;
			$params['is_override']=1;
			$params['start_date']=$this->getCurrent('start_date')->format('Y-m-d');
			$freqTrans=array(
				'Annually'=>'+1 YEAR',
				'Half Yearly'=>'+6 MONTH',
				'Monthly'=>'+1 MONTH',
				'Quarterly'=>'+3 MONTH'					
			);
			$enddate = clone $this->getCurrent('start_date');
			$enddate->modify($freqTrans[$this->getCurrent('frequency')]);
			$params['end_date']=$enddate->format('Y-m-d');
			$result=civicrm_membership_contact_create($params); 
			if($result['is_error']) {
				$this->addReportLine('warning', "Failed to add membership for {$this->getContactLink()} (with payment integration reference {$this->getCurrent('tgp')}).");
			} else {
				
				//add custom data to membership to say that it is paid by direct debit
				$this->updateCustomMembershipData($result['id'], $this->getCurrent('frequency'));
				$this->addReportLine('note', "Membership added for contact {$this->getContactLink()} (with payment integration reference {$this->getCurrent('tgp')}).");
				
			}
		} else {
			$this->addReportLine('note', "Ready to add membership to {$this->getContactLink()} (with payment integration reference {$this->getCurrent('tgp')}).");
			$this->updateCustomMembershipData($result['id'], $this->getCurrent('frequency'));
			
		}
		
	}
	
	function updateCustomMembershipData($membership_id, $frequency) {
		if(!$this->test){
			$params[1]=array( $membership_id, 'Integer');
			$freqTrans=array(
				'Annually'=>'Annually',
				'Half Yearly'=>'Half-yearly',
				'Monthly'=>'Monthly',
				'Quarterly'=>'Quarterly'					
			);

			//TODO: we should also update the membership to pays by direct debit even if the membership isn't added

			$params[2]=array( $freqTrans[$frequency], 'String');
			$query = "
				INSERT INTO civicrm_value_membership_information_9
					SET
						pays_membership_by_direct_debit_54 = 1,
						membership_payment_frequency_63 = %2,
						entity_id = %1
				ON DUPLICATE KEY
					UPDATE
						pays_membership_by_direct_debit_54 = 1,
						membership_payment_frequency_63 = %2;";				
			$result = CRM_Core_DAO::executeQuery( $query, $params );
			$result = CRM_Core_DAO::executeQuery( 'UPDATE civicrm_membership SET status_id=9, is_override=1 WHERE id = %1', $params );
			$this->addReportLine('note', "Membership for {$this->getContactLink()} updated to DD pending with status overridden.");			
		}else{
			$this->addReportLine('note', "Ready to update membership for {$this->getContactLink()} to DD pending with status overridden.");
		}
	}
	
	function updateDetails(){
		if(!$this->test){
			return;
		}
		// print_r($this->currentContactArray);
		// print_r($this->current);
		// exit;
		if($this->current['postal_code']!=$this->currentContactArray['postal_code']){ //the postcode has changed
			if($this->currentContactArray['address_id']>0){
				$address_params[1]=array( $this->currentContactArray['address_id'], 'Integer');
				$address_params[2]=array( $this->current['address_line_1'], 'String');
				$address_params[3]=array( $this->current['address_line_2'], 'String');
				$address_params[4]=array( $this->current['address_line_3'], 'String');
				$address_params[5]=array( $this->current['city'], 'String');
				$address_params[6]=array( $this->current['postal_code'], 'String');
				
				CRM_Core_DAO::executeQuery( '
				UPDATE civicrm_address
				SET
					street_address=%2,
					supplemental_address_1=%3,
					supplemental_address_2=%4,
					city=%5,
					postal_code=%6,
					country_id=NULL
				WHERE id=%1', $address_params);
			}//update address
		}
		if($this->current['email']!=$this->currentContactArray['email']){ //there in another email
			$email_params=array();
			$email_params[1]=array( $this->current['email'], 'String');
			$email_params[2]=array( $this->currentContactArray['contact_id'], 'Integer');
			$result = CRM_Core_DAO::executeQuery( 'SELECT email FROM civicrm_email WHERE contact_id = %2 AND email = %1', $email_params );
			if($result->N==0){
				CRM_Core_DAO::executeQuery( "INSERT INTO civicrm_email (contact_id, email) VALUES (%2, %1)", $email_params );
			}
		}

		//if any of the phone numbers from rapidata are not there
		$phones=array('phone_home','phone_mobile');
		foreach($phones as $phone){
			$phone_params=array();
			$phone_params[1]=array( $this->current[$phone], 'String');
			$phone_params[2]=array( $this->currentContactArray['contact_id'], 'Integer');
			$result = CRM_Core_DAO::executeQuery( 'SELECT phone FROM civicrm_phone WHERE contact_id = %2 AND phone = %1', $phone_params );
			if($result->N==0){
				CRM_Core_DAO::executeQuery( "INSERT INTO civicrm_phone (contact_id, phone) VALUES (%2, %1)", $phone_params );
			}
		}
	}
}


