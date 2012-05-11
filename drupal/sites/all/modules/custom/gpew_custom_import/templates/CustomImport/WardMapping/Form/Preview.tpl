{*
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
*}
<div class="crm-block crm-form-block crm-import-preview-form-block">

{literal}
<script type="text/javascript">
function setIntermediate( ) {
	var dataUrl = "{/literal}{$statusUrl}{literal}";
	cj.getJSON( dataUrl, function( response ) {
	   var dataStr = response.toString();
	   var result  = dataStr.split(",");
	   cj("#intermediate").html( result[1] );
	   cj("#importProgressBar").progressBar( result[0] );
	});
}

function pollLoop( ){
	setIntermediate( );
	window.setTimeout( pollLoop, 10*1000 ); // 10 sec
}

function verify( ) {
    if (! confirm('{/literal}{ts}Are you sure you want to Import now{/ts}{literal}?') ) {
        return false;
    }
	-- 
	-- cj("#id-processing").show( ).dialog({
	-- 	modal         : true,
	-- 	width         : 350,
	-- 	height        : 160,
	-- 	resizable     : false,
	-- 	bgiframe      : true,
	-- 	draggable     : true,
	-- 	closeOnEscape : false,
	-- 	overlay       : { opacity: 0.5, background: "black" },
	-- 	open          : function ( ) {
	-- 	    cj("#id-processing").dialog().parents(".ui-dialog").find(".ui-dialog-titlebar").remove();
	-- 	}
	-- });
	-- 
	-- var imageBase = "{/literal}{$config->resourceBase}{literal}packages/jquery/plugins/images/";
	--     cj("#importProgressBar").progressBar({
	--         boxImage:       imageBase + 'progressbar.gif',
	--         barImage: { 0 : imageBase + 'progressbg_red.gif',
	--                     20: imageBase + 'progressbg_orange.gif',
	--                     50: imageBase + 'progressbg_yellow.gif',
	--                     70: imageBase + 'progressbg_green.gif'
	--                   }
	-- }); 
	-- cj("#importProgressBar").show( );
	-- pollLoop( );
}
</script>
{/literal}

{* Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}

<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div> 
{* Import Progress Bar and Info *}
<div id="id-processing" class="hiddenElement">
	<h3>Importing records...</h3><br />
	<div class="progressBar" id="importProgressBar" style="margin-left:45px;display:none;"></div>
	<div id="intermediate"></div>
	<div id="error_status"></div>
</div>

<div id="preview-info">
<h2>Preview</h2>

<table class="report">
{foreach from=$report item=line}
	{if $line.type eq 'warning'}
		<tr><td style="color:red">{$line.type}</td><td style="color:red">{$line.message}</td></tr>
	{else}
		<tr><td>{$line.type}</td><td>{$line.message}</td></tr>
	{/if}	
{/foreach}
</table>




</div>
<div class="crm-submit-buttons">
   {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
</div>

{literal}
<script type="text/javascript">
cj(function() {
   cj().crmaccordions(); 
});

{/literal}{if $invalidGroupName}{literal}
cj("#new-group").removeClass( 'crm-accordion-closed' ).addClass( 'crm-accordion-open' );
{/literal}{/if}{literal}

{/literal}{if $invalidTagName}{literal}
cj("#new-tag").removeClass( 'crm-accordion-closed' ).addClass( 'crm-accordion-open' );
{/literal}{/if}{literal}

</script>
{/literal} 