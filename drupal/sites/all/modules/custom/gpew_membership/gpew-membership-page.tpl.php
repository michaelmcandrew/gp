<p><p>My membership type is:</br><b><?php echo $membership['membership_name']; ?></b></br></p>
	<!--  TEMP REMOVED UNTIL 1950 ERROR IS FIXED WITH MEMBERSHIPS
	Start date:  <b><?php echo $membership['start_date']; ?></b></br>
	-->
	<?php if ($membership['end_date']){ ?>
	<p>End date:  <b><?php echo $membership['end_date']; ?></b></br></p>
	<?php } ?>

	<?php if ($is_primary && ($membership['status_id'] == 3)) : ?>
	<p><b>Your <?php echo $membership['membership_name']; ?> membership is in grace, <a href='<?php echo $renew_link; ?>'> to renew click here.</a></b></p>
	<?php elseif(($membership['status_id'] == 3) && ($is_joint ==  7)) : ?>
	<p><b>Your <?php echo $membership['membership_name']; ?> membership is in grace, to renew this membership the primary member must logged in.</b></p>
	<?php endif; ?>
	<p>My payment frequency is:  <b><?php echo $membership_payment_frequency; ?></b></p>
	<?php echo $more_info; ?>
	</p>