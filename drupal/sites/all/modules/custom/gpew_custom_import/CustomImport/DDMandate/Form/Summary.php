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

/**
 * This class summarizes the import results
 */
class CustomImport_DDMandate_Form_Summary extends CRM_Core_Form {

    public function preProcess( ) {
		$this->assign( 'final_report', $this->get('final_report'));
		$this->assign( 'final_report_csv_url', $this->get('final_report_csv_url'));
		$config = CRM_Core_Config::singleton( );
        
    }

    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Done'),
                                         'isDefault' => true   ),
                                 )
                           );
    }
    
    public function postProcess( ) {
        $dao = new CRM_Core_DAO( );
        $db = $dao->getDatabaseConnection( );
        
        $importTableName = $this->get( 'importTableName' );
        // do a basic sanity check here
        if (strpos( $importTableName, 'civicrm_import_job_' ) === 0) {
            $query = "DROP TABLE IF EXISTS $importTableName";
            $db->query( $query );
        }
    }

    public function getTitle( ) {
        return ts('Summary');
    }

}