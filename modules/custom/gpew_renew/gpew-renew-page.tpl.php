	
	<p>You have a <b><?php echo $membership['membership_name']; ?></b> membership.</br>
	Start date:  <b><?php echo $membership['start_date']; ?></b></br>
	<?php if ($membership['end_date']){ ?>
	End date:  <b><?php echo $membership['end_date']; ?></b></br>
	<?php } ?>

	<?php if ($is_primary) : ?>
	<p><a href='<?php echo $renew_link; ?>'>To renew your <?php echo $membership['membership_name']; ?> membership click here.</a></p>
	<?php else : ?>
	<p><b>You cannot renew this membership, the primary member must be logged in.</b></p>
	<?php endif; ?>
	
	<?php echo $more_info; ?>
	
	</p>