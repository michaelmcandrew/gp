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

require_once 'CRM/Report/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';

class CRM_Report_Form_Contribute_LocalCapitationDetail extends CRM_Report_Form {
    protected $_addressField = false;

    protected $_emailField   = false;

    protected $_summary      = null;

    protected $_customGroupExtends = array( 'Contribution' );
	protected $_customGroupFilters = FALSE;

    function __construct( ) {
	
		//get selects for local and regional parties
		$lps[0]=$rps[0]='- select party -';
		$lpq = CRM_Core_DAO::executeQuery( "SELECT id, display_name FROM `civicrm_contact` WHERE `contact_sub_type` = 'Local_Party'", array() );		
		while($lpq->fetch()){
			$lps[$lpq->id]=$lpq->display_name;
		}
		
		$rpq = CRM_Core_DAO::executeQuery( "SELECT id, display_name FROM `civicrm_contact` WHERE `contact_sub_type` = 'Regional_Party'", array() );
		while($rpq->fetch()){
			$rps[$rpq->id]=$rpq->display_name;
		}
	
        $this->_columns = 
            array( 'civicrm_contact'      =>
                   array( 'dao'     => 'CRM_Contact_DAO_Contact',
                          'fields'  =>
                          array(	'id'           => 
                          				array( 'no_repeat'  => true, ),
									'display_name' => 	
                                 		array(	'title' => ts( 'Contact Name' ),
		                                        'required'  => true,
		                                        'no_repeat' => true )
                          ),
                          
                          'grouping'=> 'contact-fields',
                          ),
 
                   'civicrm_email'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'fields'    =>
                          array( 'email' => 
                                 array( 'title'      => ts( 'Email' ),
                                        'default'    => true,
                                        'no_repeat'  => true
                                       ),  ),
                          'grouping'      => 'contact-fields',
                          ),

                   'civicrm_phone'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Phone',
                          'fields'    =>
                          array( 'phone' => 
                                 array( 'title'      => ts( 'Phone' ),
                                        'default'    => true,
                                        'no_repeat'  => true
                                        ), ),
                          'grouping'      => 'contact-fields',
                          ),

                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'fields' =>
                          array( 'street_address'    => null,
								 'supplemental_address_1'    => null,
								 'supplemental_address_2'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ),  
                                        'default' => true ), ),
                          'grouping'=> 'contact-fields',

                          ),

                   'civicrm_contribution' =>
                   array( 'dao'     => 'CRM_Contribute_DAO_Contribution',

'fields' => array(
	'contribution_id' => array( 
		'name' => 'id',
		'no_display' => true,
		'required' => true,
	),
	'contribution_type_id' => array(
		'title' => ts('Contribution Type'),
		'default' => true,
	),
	'trxn_id' => null,
	'payment_instrument_id' => array(
		'title' => ts('Payment instrument'),
	),
	'receive_date' => array(
		'default' => true
	),
	'receipt_date' => null,
	'fee_amount' => null,
	'net_amount' => null,
	'total_amount' => array(
		'title' => ts( 'Amount' ),
		'required'     => true,
		'statistics'   => array(
			'sum' => ts( 'Amount' )
		),
	),
),


'filters' => array(
	// 'local_party_10' => array(
	// 	'title' => ts( 'Local Party' ),
	// 	'operatorType' => CRM_Report_Form::OP_SELECT,
	// 	'options' => $lps,
	// ),
	// 'region_9' => array(
	// 	'title' => ts( 'Regional Party' ),
	// 	'operatorType' => CRM_Report_Form::OP_SELECT,
	// 	'options' => $rps,
	// ),
	'receive_date' => array(
		'operatorType' => CRM_Report_Form::OP_DATE
	),
	'contribution_type_id' => array(
		'title' => ts( 'Contribution Type' ), 
		'operatorType' => CRM_Report_Form::OP_MULTISELECT,
		'options' => CRM_Contribute_PseudoConstant::contributionType( ),
		'default' => array( 2 ),
		'no_display'=>TRUE
	),
	// 'contribution_status_id' => array(
	// 	'title' => ts( 'Contribution Status' ),
	// 	'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	// 	'options' => CRM_Contribute_PseudoConstant::contributionStatus( ),
	// 	'default' => array( 1 ),
	// ),
	// 'payment_instrument_id' => array(
	// 	'title' => ts( 'Payment instrument' ),
	// 	'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	// 	'options' => CRM_Contribute_PseudoConstant::paymentInstrument( ),
	// ),
	// 'total_amount' => array(
	// 	'title' => ts( 'Contribution Amount' )
	// ),
),




                          'grouping'=> 'contri-fields',
                          ),
                   
                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_GroupContact',
                          'alias'  => 'cgroup',
                           ),
                   
                   'civicrm_contribution_ordinality' =>                    
                   array( 'dao'    => 'CRM_Contribute_DAO_Contribution',
                          'alias'  => 'cordinality',
                           ),
                   );

	            $this->_columns['civicrm_value_capitation_4'] = array(
		          // 'fields' => array(
		          //   'custom_9' => array(
		          //     'name'        => 'region_9',
		          //     'title'       => ts( 'Regional party' ),
		          //     'type'        => CRM_Utils_Type::T_INT)),
					'filters' => array(
						'local_party_10' => array(
							'title' => ts( 'Local party' ),
							'operatorType' => CRM_Report_Form::OP_SELECT,
							'options' => $lps,
						)
					)
		        );
		    $this->_tagFilter = FALSE;
;
        parent::__construct( );
    }

    function preProcess( ) {
        parent::preProcess( );
    }

    function select( ) {
        $select = array( );

        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( $tableName == 'civicrm_address' ) {
                            $this->_addressField = true;
                        } else if ( $tableName == 'civicrm_email' ) {
                            $this->_emailField = true;
                        }
                        
                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = 
                                        $field['type'];
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  =  
                                        $field['type'];
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                }
                            }   
                            
                        } else {
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value( 'type', $field );
                        }
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = null;

        $this->_from = "
        FROM  civicrm_contact      {$this->_aliases['civicrm_contact']} {$this->_aclFrom}
              INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id AND {$this->_aliases['civicrm_contribution']}.is_test = 0
              INNER JOIN (SELECT c.id, IF(COUNT(oc.id) = 0, 0, 1) AS ordinality FROM civicrm_contribution c LEFT JOIN civicrm_contribution oc ON c.contact_id = oc.contact_id AND oc.receive_date < c.receive_date GROUP BY c.id) {$this->_aliases['civicrm_contribution_ordinality']} 
                      ON {$this->_aliases['civicrm_contribution_ordinality']}.id = {$this->_aliases['civicrm_contribution']}.id
               LEFT JOIN  civicrm_phone {$this->_aliases['civicrm_phone']} 
                      ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                         {$this->_aliases['civicrm_phone']}.is_primary = 1)";
        
        if ( $this->_addressField OR ( !empty($this->_params['state_province_id_value']) OR !empty($this->_params['country_id_value']) ) ) { 
            $this->_from .= "
            LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                      {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        }
        
        if ( $this->_emailField ) {
            $this->_from .= " 
            LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND 
                      {$this->_aliases['civicrm_email']}.is_primary = 1\n";
        }

    }


    function groupBy( ) {
        $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_contribution']}.id ";
    }

    function orderBy( ) {
        $this->_orderBy = " ORDER BY {$this->_aliases['civicrm_contact']}.sort_name ";
    }

    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );

        $select = "
        SELECT COUNT({$this->_aliases['civicrm_contribution']}.total_amount ) as count,
               SUM( {$this->_aliases['civicrm_contribution']}.total_amount ) as amount,
               ROUND(AVG({$this->_aliases['civicrm_contribution']}.total_amount), 2) as avg
        ";

        $sql = "{$select} {$this->_from} {$this->_where}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        if ( $dao->fetch( ) ) {
            $statistics['counts']['amount']    = array( 'value' => $dao->amount,
                                                        'title' => 'Total Amount',
                                                        'type'  => CRM_Utils_Type::T_MONEY );
            $statistics['counts']['avg']       = array( 'value' => $dao->avg,
                                                        'title' => 'Average',
                                                        'type'  => CRM_Utils_Type::T_MONEY );
        }

        return $statistics;
    }

    function postProcess( ) {
        // get the acl clauses built before we assemble the query
        $this->buildACLClause( $this->_aliases['civicrm_contact'] );
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $checkList  = array();
        $entryFound = false;
        $display_flag = $prev_cid = $cid =  0;
        $contributionTypes = CRM_Contribute_PseudoConstant::contributionType( );
        $paymentInstruments = CRM_Contribute_PseudoConstant::paymentInstrument( );
        
        foreach ( $rows as $rowNum => $row ) {
            if ( !empty($this->_noRepeats) && $this->_outputMode != 'csv' ) {
                // don't repeat contact details if its same as the previous row
                if ( array_key_exists('civicrm_contact_id', $row ) ) {
                    if ( $cid =  $row['civicrm_contact_id'] ) {
                        if ( $rowNum == 0 ) {
                            $prev_cid = $cid;
                        } else {
                            if( $prev_cid == $cid ) {
                                $display_flag = 1;
                                $prev_cid = $cid;
                            } else {
                                $display_flag = 0;
                                $prev_cid = $cid;
                            }
                        }
                        
                        if ( $display_flag ) {
                            foreach ( $row as $colName => $colVal ) {
                                if ( in_array($colName, $this->_noRepeats) ) {
                                    unset($rows[$rowNum][$colName]);          
                                }
                            }
                        }
                        $entryFound = true;
                    }
                }
            }
            
            // handle state province
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvince( $value, false );

                    $url = CRM_Report_Utils_Report::getNextUrl( 'contribute/detail',
                                                                "reset=1&force=1&" . 
                                                                "state_province_id_op=in&state_province_id_value={$value}",
                                                                $this->_absoluteUrl, $this->_id );
                    $rows[$rowNum]['civicrm_address_state_province_id_link' ] = $url;
                    $rows[$rowNum]['civicrm_address_state_province_id_hover'] =
                        ts("List all contribution(s) for this State.");
                }
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );

                    $url = CRM_Report_Utils_Report::getNextUrl( 'contribute/detail',
                                                                "reset=1&force=1&" . 
                                                                "country_id_op=in&country_id_value={$value}",
                                                                $this->_absoluteUrl, $this->_id );
                    $rows[$rowNum]['civicrm_address_country_id_link' ] = $url;
                    $rows[$rowNum]['civicrm_address_country_id_hover'] = 
                        ts("List all contribution(s) for this Country.");
                }
                
                $entryFound = true;
            }

            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 CRM_Utils_Array::value( 'civicrm_contact_display_name', $rows[$rowNum] ) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( "civicrm/contact/view"  , 
                                              'reset=1&cid=' . $row['civicrm_contact_id'],
                                              $this->_absoluteUrl );
                $rows[$rowNum]['civicrm_contact_display_name_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_display_name_hover'] =  
                    ts("View Contact Summary for this Contact.");
            }
            if ( $value = CRM_Utils_Array::value( 'civicrm_contribution_contribution_type_id', $row ) ) {
                $rows[$rowNum]['civicrm_contribution_contribution_type_id'] = $contributionTypes[$value];
                $entryFound = true;
            }

            if ( $value = CRM_Utils_Array::value( 'civicrm_contribution_payment_instrument_id', $row ) ) {
                $rows[$rowNum]['civicrm_contribution_payment_instrument_id'] = $paymentInstruments[$value];
                $entryFound = true;
            }

            if ( ( $value = CRM_Utils_Array::value( 'civicrm_contribution_total_amount_sum', $row ) ) && 
                 CRM_Core_Permission::check( 'access CiviContribute' ) ) {
                $url = CRM_Utils_System::url( "civicrm/contact/view/contribution" , 
                                              "reset=1&id=".$row['civicrm_contribution_contribution_id']."&cid=".$row['civicrm_contact_id']."&action=view&context=contribution&selectedChild=contribute",
                                              $this->_absoluteUrl );
                $rows[$rowNum]['civicrm_contribution_total_amount_sum_link'] = $url;
                $rows[$rowNum]['civicrm_contribution_total_amount_sum_hover'] =  
                    ts("View Details of this Contribution.");
                $entryFound = true;
            }
            
            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
            $lastKey = $rowNum;
        }
    }

}
