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

/**
 * class to parse contact csv files
 */
class CustomImport_Parser_Mandate 
{
	//DB object containing all candidates
    protected $candidate;

	//array showing results for each candidate as they are processed.
    public $results = array();

    public $db_table;

	function test(){
		$this->test=TRUE;
		$this->getCanditates();
		while($this->candidate->fetch()){
			$this->parseCandidate();
		}
	}

	function import(){
		$this->test=FALSE;
		$this->getCanditates();
		while($this->candidate->fetch()){
			$this->parseCandidate();
		}
	}
	
	function parseCandidate(){
		
		//create shortcut for tgp (for brevity)
		$this->tgp=$this->candidate->confirmationreference_34;
		//if it is invalid (acording to the three tests set up by GP, return)
		if(!$this->isValidCandidate()){
			return;
		}
		
		//if the TGP is already in CiviCRM, no need to do any more, return
		if($this->tgpAlreadyInCivi()){
			return;
		}
		
		$this->searchForMatch();
		
		if(count($this->results[$this->tgp]['matches'])>1){
			// TODO report multiple matches
			return;
		}
		
		if(count($this->results[$this->tgp]['matches'])==0){
			// add the person to CiviCRM
		}
		
		// add the TGP number to CiviCRM
		
		if($this->couldBeYoungGreen()){
			$this->addToYoungGreens();
		}
		
		if($this->wantsToBeAMember() AND !$this->isAMember()){
			$this->addMembership();
		}
	}	
	

	function getCanditates(){
		$this->candidate = CRM_Core_DAO::executeQuery("SELECT * FROM {$this->db_table}",$params);
	}
	
	function isValidCandidate() {
		return TRUE;
		//TODO the three tests go here
	}

	function tgpAlreadyInCivi() {
		return FALSE;
		//TODO search in CiviCRM for the TGP number and add it to the 
	}
	
	function searchForMatch(){
		print_r($this->tgp);//->ConfirmationReference;
	}

	function couldBeYoungGreen(){
		return TRUE;
	}

	function addToYoungGreens(){
		return TRUE;
	}

	function wantsToBeAMember(){
		return TRUE;
	}
	
	function isAMember(){
		return FALSE;
	}

	function addMembership(){
	}
	
	
	
		
		

}


