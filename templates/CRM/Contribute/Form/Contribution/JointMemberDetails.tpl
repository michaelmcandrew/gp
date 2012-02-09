<fieldset id="jointMemberFieldSet">
<legend>Joint Member Details</legend>
<div class="messages help"><p>Please fill in the details of the joint member below.</p></div>
	
<div class="crm-section {$form.JointMemberFirstName.name}-section">	
	<div class="label">{$form.JointMemberFirstName.label} <span title="This field is required." class="crm-marker">*</span></div>
    <div class="content">
        {$form.JointMemberFirstName.html}
	</div>
	<div class="clear"></div> 
</div>

<div class="crm-section {$form.JointMemberMiddleName.name}-section">	
	<div class="label">{$form.JointMemberMiddleName.label}</div>
    <div class="content">
        {$form.JointMemberMiddleName.html}
	</div>
	<div class="clear"></div> 
</div>

<div class="crm-section {$form.JointMemberLastName.name}-section">	
	<div class="label">{$form.JointMemberLastName.label} <span title="This field is required." class="crm-marker">*</span></div>
    <div class="content">
        {$form.JointMemberLastName.html}
	</div>
	<div class="clear"></div> 
</div>

<div class="crm-section {$form.JointMemberEmailAddress.name}-section">	
	<div class="label">{$form.JointMemberEmailAddress.label}</div>
    <div class="content">
        {$form.JointMemberEmailAddress.html}
	</div>
	<div class="clear"></div> 
</div>

<div class="crm-section {$form.JointMemberMobile.name}-section">	
	<div class="label">{$form.JointMemberMobile.label}</div>
    <div class="content">
        {$form.JointMemberMobile.html}
	</div>
	<div class="clear"></div> 
</div>

<div class="crm-section {$form.JointMemberDateOfBirth.name}-section">	
	<div class="label">{$form.JointMemberDateOfBirth.label}</div>
    <div class="content">
        {include file="CRM/common/jcalendar.tpl" elementName=JointMemberDateOfBirth}
	<br /><span class="description">If you are under 30 and would like to receive a free annual magazine and monthly email updates from the Young Greens, you must provide your date of birth.</span></div>
	<div class="clear"></div> 
</div>

</fieldset>

