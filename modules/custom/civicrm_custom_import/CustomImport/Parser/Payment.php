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
require_once 'api/v2/Contribution.php';
require_once 'api/v2/MembershipContributionLink.php';

require_once 'DD.php';

define ('CIVICRM_GPEW_DD_MANDATE_TABLE', 'civicrm_value_external_identifiers_5');
define ('CIVICRM_GPEW_DD_MANDATE_ID', 'direct_debit_reference_16');
define ('CIVICRM_GPEW_DD_MANDATE_FREQUENCY', 'payment_frequency_37');


/**
 * class to parse contact csv files
 */
class CustomImport_Parser_Payment extends CustomImport_Parser_DD
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
//				print_r($this->current);
			}
		}
		function initCurrent(){
//			print_r($this->candidate);
			unset($this->currentContributionArray);
			unset($this->currentMembershipArray);
			unset($this->currentMembershipArray);
			$this->current=array();
			$this->current['tgp']=$this->candidate->urn_0;
			$this->current['frequency']=$this->candidate->frequency_10;
			$this->current['amount']=$this->candidate->dd_amount_11;
			$this->current['date']=$this->RapiDataToDate($this->candidate->dd_date_12);
			$this->current['source']=$this->candidate->source_13;
		}
		
	
		function parseCandidate(){
			
			//find out if the TGP is matched to exactly one contact.  If it doesn't, report and exit
			if($this->searchForTGP() != 'one'){
				if($this->searchForTGP() == 'none'){
					$this->addReportLine('warning', "Payment for {$this->getCurrent('tgp')} will not be imported (no matching TGP number).");
				}
				if($this->searchForTGP() == 'multiple'){
					$this->addReportLine('warning', "Payment for {$this->getCurrent('tgp')} will not be imported (multiple matching TGP numbers).");
				}
				return;
			}
			
			$this->current['contact_id'] = $this->currentContactArray['contact_id'];
			
			//work out if this is should be added to a membership
			$this->current['is_membership_contribution'] = ($this->getCurrent('is_membership_tgp') AND $this->isAMember($this->currentContactArray['contact_id'])) ? TRUE : FALSE ;
			
			//set all the contribution parameters
			$this->currentContributionArray['contribution_type_id'] = $this->current['is_membership_contribution'] ? 2 : 1 ;
			$this->currentContributionArray['contact_id'] = $this->currentContactArray['contact_id'];
			$this->currentContributionArray['total_amount'] = $this->current['amount'];
			$this->currentContributionArray['receive_date'] = $this->current['date']->format('Y-m-d');
			$this->currentContributionArray['contribution_status_id']=1;
			$this->currentContributionArray['source']=$this->current['frequency'];
			
			//record the contribution
			if(!$this->test){
				$contResult=civicrm_contribution_add($this->currentContributionArray);
				if(!$contResult['is_error']){
					$this->addReportLine('note', "Added contribution of {$this->currentContributionArray['total_amount']} to {$this->getContactLink()}");
				} else {
					$this->addReportLine('warning', "Could not add contribution of {$this->currentContributionArray['total_amount']} to {$this->getContactLink()}");
					return;
				}
			} else {
				$this->addReportLine('note', "Ready to add contribution of {$this->currentContributionArray['total_amount']} to {$this->getContactLink()}");
			}
			
			// if this is not a membership contribution, nothing else to do - return
			if(!$this->current['is_membership_contribution']){
				return;
			}
			
			//edit the membership record the membership (potentially extend membership)

			$freqTrans=array(
				'Annually'=>'+1 YEAR',
				'Half Yearly'=>'+6 MONTH',
				'Monthly'=>'+1 MONTH',
				'Quarterly'=>'+3 MONTH'					
			);
			
			$MembershipParams=array('contact_id'=>$this->current['contact_id']);
			$memberships=civicrm_membership_contact_get($MembershipParams);
			$membership=current(current($memberships));
			

			$this->addReportLine('info', "End date of membership for {$this->getContactLink()} is {$membership['end_date']}");
			$currentMembershipEndDate = new DateTime($membership['end_date']);
			$potentialEndDate = clone $this->current['date'];
			$potentialEndDate->modify($freqTrans[$this->current['frequency']]);

			if($potentialEndDate>$currentMembershipEndDate) {
				$report[]=array('info', "End date according to DD payment ({$potentialEndDate->format('Y-m-d')}) for {$this->getContactLink()} AFTER membership end date ({$currentMembershipEndDate->format('Y-m-d')})");
				if(!$this->test){
					$membership['end_date']=$potentialEndDate->format('Y-m-d');
					$memResult=civicrm_membership_contact_create($membership);
					if(!$memResult['is_error']) {
						$this->addReportLine('note', "Extended membership for contact {$this->getContactLink()} by {$freqTrans[$this->current['frequency']]} to {$potentialEndDate->format('Y-m-d')}.");
					} else {
						$this->addReportLine('warning', "Failed to extend membership for {$this->getContactLink()} by {$freqTrans[$this->current['frequency']]} {$potentialEndDate->format('Y-m-d')}.");
					}
				} else {
					$this->addReportLine('note', "Ready to extend membership for {$this->getContactLink()} by {$freqTrans[$this->current['frequency']]} {$potentialEndDate->format('Y-m-d')}.");
				}					
			} else {
				$this->addReportLine('info', "End date according to DD payment ({$potentialEndDate->format('Y-m-d')}) for {$this->getContactLink()} BEFORE membership end date ({$currentMembershipEndDate->format('Y-m-d')}) so will not extend membership.");	
			}
			//Link it up!
			$mcl=array(
				'contribution_id' => $contResult['id'],
				'membership_id' => $memResult['membership_id']
			);
			if(!$this->test){
				$mclResult=civicrm_membershipcontributionlink_create($mcl);
				if(!$memResult['is_error']) {
					$this->addReportLine('info', "Linked contribution to membership");
				} else {
					$this->addReportLine('warning', "Could not link contribution to membership for {$this->getContactLink()} by {$freqTrans[$this->current['frequency']]}");
				}
			}
		}
}























