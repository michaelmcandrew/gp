{if $jointMember OR 1}
<fieldset class="label-left">
    <div class="crm-group">
		<div class="header-dark">
	        {ts}Joint member details{/ts}
	    </div>
	</div>
	
	<div class="crm-section">
		<div class="label">
			First name
		</div>
		<div class="content">
			{$jointMember.JointMemberFirstName}
		</div>
		<div class="clear">
		</div>
	</div>

	<div class="crm-section">
		<div class="label">
			Middle name
		</div>
		<div class="content">
			{$jointMember.JointMemberMiddleName}
		</div>
		<div class="clear">
		</div>
	</div>

	<div class="crm-section">
		<div class="label">
			Last name
		</div>
		<div class="content">
			{$jointMember.JointMemberLastName}
		</div>
		<div class="clear">
		</div>
	</div>

	<div class="crm-section">
		<div class="label">
			Email
		</div>
		<div class="content">
			{$jointMember.JointMemberEmailAddress}
		</div>
		<div class="clear">
		</div>
	</div>

	<div class="crm-section">
		<div class="label">
			Date of birth
		</div>
		<div class="content">
			{$jointMember.JointMemberDateOfBirth}
		</div>
		<div class="clear">
		</div>
	</div>
</fieldset>
    
{/if}
