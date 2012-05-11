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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Import/Parser/Contact.php';

/**
 * This class previews the uploaded file and returns summary
 * statistics
 */
class CustomImport_DDMandate_Form_Preview extends CRM_Core_Form {

    public function preProcess()
    {
        //get the data from the session
        if($this->get( 'import_done')!=TRUE) {             
			require_once('CustomImport/Parser/DDMandate.php');
			$ImportJob = new CustomImport_Parser_DDMandate();
			$ImportJob->db_table=$this->get('importTableName');
			$ImportJob->test = TRUE;
			$ImportJob->import();        
	        $this->assign( 'report', $ImportJob->getReport());
		}
    }

    public function buildQuickForm( ) {
        $path = "_qf_MapField_display=true";
        $qfKey = CRM_Utils_Request::retrieve( 'qfKey', 'String', $form );
        if ( CRM_Utils_Rule::qfKey( $qfKey ) ) $path .= "&qfKey=$qfKey";
        
        $previousURL = CRM_Utils_System::url('civicrm/import/ddmandate', $path, false, null, false);
        $cancelURL   = CRM_Utils_System::url('civicrm/import/ddmandate', 'reset=1');
        
        $buttons = array(
                         array ( 'type'      => 'back',
                                 'name'      => ts('<< Previous'),
                                 'js'        => array( 'onclick' => "location.href='{$previousURL}'; return false;" ) ),
                         array ( 'type'      => 'next',
                                 'name'      => ts('Import Now >>'),
                                 'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'isDefault' => true,
                                 'js'        => array( 'onclick' => "return verify( );" )

                                 ),
                         array ( 'type'      => 'cancel',
                                 'name'      => ts('Cancel'),
                                 'js'        => array( 'onclick' => "location.href='{$cancelURL}'; return false;" ) ),
                         );
        
        $this->addButtons( $buttons );

    }


    public function getTitle( ) {
        return ts('Preview');
    }

    public function postProcess( ) {
	
        $session =& CRM_Core_Session::singleton( );
        $userID  = $session->get( 'userID' );
        require_once 'CRM/ACL/BAO/Cache.php';
        CRM_ACL_BAO_Cache::updateEntry( $userID );

        // run the import
        if($this->get( 'import_done')!=TRUE) {             
			require_once('CustomImport/Parser/DDMandate.php');
			$ImportJob = new CustomImport_Parser_DDMandate();
			$ImportJob->db_table=$this->get('importTableName');
			$ImportJob->import();        
	        $this->set( 'final_report', $ImportJob->getReport());
	        $this->set( 'import_done', TRUE);
	
		}
               
        // update cache after we done with runImport
        require_once 'CRM/ACL/BAO/Cache.php';
        CRM_ACL_BAO_Cache::updateEntry( $userID );

        // add all the necessary variables to the form
        $this->set( 'final_report', $ImportJob->getReport());
//		$importJob->setFormVariables( $this );
        
        // check if there is any error occured
        
        $errorMessage = array();
       
		foreach($ImportJob->getReport() as $key => $value) {
		    $errorMessage[] = strip_tags('"'.$value['type'].'","'.$value['message'].'"');
		}

		$config = CRM_Core_Config::singleton( );
		$errorFilename = "ddmandate." . $this->get('importTableName') . ".custom.report.csv"; 
		$this->set('final_report_csv_url', $config->imageUploadURL . $errorFilename);
		$errorFilePath = $config->imageUploadDir . $errorFilename;
		if ( $fd = fopen( $errorFilePath, 'w' ) ) {
		    fwrite($fd, implode("\n", $errorMessage));
		}
		fclose($fd);

    }


}
