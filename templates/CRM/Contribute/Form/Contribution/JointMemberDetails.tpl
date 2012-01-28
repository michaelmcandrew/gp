<h5 style="font-weight:bold;padding:4px;">Joint Member Details</h5>

<div class="crm-section {$form.JointMemberFirstName.name}-section">	
	<div class="label">{$form.JointMemberFirstName.label}</div>
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
	<div class="label">{$form.JointMemberLastName.label}</div>
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
	</div>
	<div class="clear"></div> 
</div>

