<?php

require_once 'CustomImport/Parser/Custom.php';


class CustomImport_Parser_WardMapping extends CustomImport_Parser_Custom 
{
	
	public $output;

	function initExport(){

		$config = CRM_Core_Config::singleton( );
		$output[] = implode($config->fieldSeparator, array('local_party_name', 'local_party_contact_id', 'ons_code', 'ward_name', 'DC_MD_UA_LB_name', 'county_name'));

		// IF(cc.organization_name IS NULL, 'NULL', cc.organization_name) AS local_party_name,
		// IF(cc.id IS NULL, 'NULL', cc.id) AS local_party_contact_id,
		// w.code AS ons_code,
		// w.name AS ward_name,
		// w.name AS ward_name, b.name AS DC_MD_UA_LB_name,
		// IF(a.name IS NULL, 'NULL', a.name) AS county_name
		
		$query="
		SELECT
			cc.organization_name AS local_party_name,
			cc.id AS local_party_contact_id,
			w.code AS ons_code,
			w.name AS ward_name,
			w.name AS ward_name,
			b.name AS DC_MD_UA_LB_name,
			a.name AS county_name
			
		FROM civicrm_gpew_ons AS w
		LEFT JOIN civicrm_gpew_ons AS b ON w.code_4=b.code
		LEFT JOIN civicrm_gpew_ons AS a ON w.code_2=a.code
		LEFT JOIN ukgr_crm.civicrm_gpew_ward_local_party AS cwlp ON w.code=cwlp.ward_ons_code
		LEFT JOIN ukgr_crm.civicrm_contact cc ON cwlp.local_party_contact_id=cc.id
		WHERE (w.country='E' OR w.country='W') AND length(w.code)=6
		ORDER BY cc.organization_name IS NULL, cc.organization_name, w.code
		";
		
		$WardMappingResult = CRM_Core_DAO::executeQuery( $query, $params );

		while($WardMappingResult->fetch()){
			if ($WardMappingResult->local_party_name==''){
				$WardMappingResult->local_party_name='NULL';
			}
			if ($WardMappingResult->local_party_contact_id==''){
				$WardMappingResult->local_party_contact_id='NULL';
			}
			if ($WardMappingResult->county_name==''){
				$WardMappingResult->county_name='NULL';
			}
			$fields=array(
				"\"$WardMappingResult->local_party_name\"",
				"\"$WardMappingResult->local_party_contact_id\"",
				"\"$WardMappingResult->ons_code\"",
				"\"$WardMappingResult->ward_name\"",
				"\"$WardMappingResult->DC_MD_UA_LB_name\"",
				"\"$WardMappingResult->county_name\"",
			);
			$output[] = implode($config->fieldSeparator, $fields);
			
            
		}
		$this->output = implode("\n",$output);
		
	}
			
	function getWards() {
		// get all wards from the temp table that has just been created as the keys of an array (called $this->ward)
		$params = array();
		$this->ward = CRM_Core_DAO::executeQuery("SELECT * FROM {$this->db_table}", $params);
	//	echo $this->db_table;
	}
	

	function test(){
		
		//carry out all the tests
	}
	
	function wardsValid(){
		//this function checks to see if all the wards in the import file are valid, i.e. if they appear in the civicrm_gpew_ons table in CiviCRM
		
		// get all wards from the temp table that has just been created as the keys of an array (called $this->ward)
		$this->getWards();
		
		// get all the wards that exist in CiviCRM
		$params=array();
		$query="
			SELECT code
			FROM civicrm_gpew_ons
			WHERE (country='E' OR country='W') AND length(code)=6
		";
		
		$WardMappingResult = CRM_Core_DAO::executeQuery( $query, $params );
		while($WardMappingResult->fetch()){
			$remainingWardsInCivi[$WardMappingResult->code]=1;
		}
		$allWardsInCivi=$remainingWardsInCivi;
		
		//foreach ward in CiviCRM
		while($this->ward->fetch()){
			
			// if the ward code in the file is present in the list of all ward codes from Civi, remove it from 
			if(array_key_exists($this->ward->ons_code, $remainingWardsInCivi)){
				unset($remainingWardsInCivi[$this->ward->ons_code]);
			}
			// if the ward code is not in the list of all ward codes from Civi, add it to non existent wards. 
			if(!array_key_exists($this->ward->ons_code, $allWardsInCivi)){
				$nonExistentWards[$this->ward->ons_code] = 1;
			}
		}
		
		// by this point, if the ward file contains all the wards in the database, the array $allWardsInCivi should be empty.  If not, the code below reports which wards are missing
		if(count($remainingWardsInCivi)){
			foreach($remainingWardsInCivi as $ons_code => $void){
				$this->addReportLine('warning', "Ward code $ons_code missing from import file.");
			}	
		}
		// if there were any files in the ward file that weren't in the database they will be mentioned here.
		if(count($nonExistentWards)){
			foreach($nonExistentWards as $ons_code => $void){
				$this->addReportLine('warning', "Ward code $ons_code is invalid (does not exist in postcode - ward mapping file).");
			}	
		}		
		$this->nonExistentWards=$nonExistentWards;
		
	}
	
	function localPartiesValid(){
		
		$this->getWards();
		
		// get all local parties from the database and store them as the keys of an arrays (with names as values - useful later)
		$params=array();
		$query="
			SELECT id, display_name
			FROM civicrm_contact
			WHERE contact_sub_type = 'Local_party'
		";
		
		$LocalPartyResult = CRM_Core_DAO::executeQuery( $query, $params );
		while($LocalPartyResult->fetch()){
			$allLPs[$LocalPartyResult->id]=$LocalPartyResult->display_name;
		}
		
		$allLPsForValidCheck=$allLPs;
		
		// go through all LPs in the file
		
		while($this->ward->fetch()){

			// if the ward code in the file is present in the list of all ward codes, remove it.
			if(array_key_exists($this->ward->local_party_contact_id, $allLPs)){
				unset($allLPs[$this->ward->local_party_contact_id]);
			}
			if($this->ward->local_party_contact_id!='NULL' AND !array_key_exists($this->ward->local_party_contact_id, $allLPsForValidCheck)){
				$nonExistentPartyIds[$this->ward->local_party_contact_id]=1;
			}
			
		}
		
		// by this point, if the ward file contains all the local parties in the database, the array $allLPs should be empty.  If not, the code below reports which wards are missing
		
		if(count($allLPs)){
			foreach($allLPs as $localpartyid => $void){
				$this->addReportLine('warning', "No wards map to local party <a href='/civicrm/contact/view?cid={$localpartyid}'>{$allLPs[$localpartyid]}</a>.");
			}	
		}		
		if(count($nonExistentPartyIds)){
			foreach($nonExistentPartyIds as $localpartyid => $void){
				$this->addReportLine('warning', "$localpartyid is not an ID of any local party in the database.");
			}	
		}		
		$this->nonExistentPartyIds=$nonExistentPartyIds;
	}
	
			

	
	function import(){

		//check for wards / local parties that are either not specified in the import, or are not valid (i.e. don't exist in CiviCRM)
		$this->wardsValid();
		$this->localPartiesValid();


		$params=array();
		$query='SELECT * FROM civicrm_gpew_ward_local_party';
		$LPwardmappingQuery=CRM_Core_DAO::executeQuery($query,$params);
		while($LPwardmappingQuery->fetch()){
			$lpwardmap[$LPwardmappingQuery->ward_ons_code]=$LPwardmappingQuery->local_party_contact_id;
		}
		
		


		
		if(!$this->test){
		
			$this->getWards();
			while($this->ward->fetch()){
				if($lpwardmap[$this->ward->ons_code]!=$this->ward->local_party_contact_id &&
					$this->ward->local_party_contact_id!='NULL' &&
					!array_key_exists($this->ward->local_party_contact_id, $this->nonExistentPartyIds) &&
					!array_key_exists($this->ward->ons_code, $this->nonExistentWards)
				){
					$params=array();
					$params[1] = array( $this->ward->ons_code, 'String');
					$params[2] = array( $this->ward->local_party_contact_id, 'Integer');
					$query = "INSERT INTO civicrm_gpew_ward_local_party SELECT %1, %2 ON DUPLICATE KEY UPDATE local_party_contact_id = %2";
					$result = CRM_Core_DAO::executeQuery( $query, $params );	
				} elseif($this->ward->local_party_contact_id=='NULL') {
					$params=array();
					$params[1] = array( $this->ward->ons_code, 'String');
					$query = "DELETE FROM civicrm_gpew_ward_local_party WHERE ward_ons_code=%1";
					$result = CRM_Core_DAO::executeQuery( $query, $params );	
					
				}		
			}
		}
	}
	
	function initCurrent(){
		
	}
	function parseWard(){
		print_r($this->ward);
		exit;
		
		
	}
}