<p>My membership type is <b><?php echo strtolower($membership['membership_name']); ?></b>.
	
	<?php if ($membership['end_date']){ ?>
	It <?php echo $membership['end_date_text']; ?> <b><?php echo $membership['end_date']; ?></b>.</p>
  <?php } ?>
	<?php if ($membership_payment_is_recurring == 1) { ?>
  	<p>I'm currently paying by <b>direct debit/standing order</b>. My payment frequency is <b><?php echo strtolower($membership_payment_frequency); ?></b>.</p>
	<?php } ?>
  
  <?php if ($membership_status_name) { ?>
  	<p>My membership status is <b><?php echo strtolower($membership_status_name); ?></b>.</p>
	<?php } ?>
	
	<?php
	if ($is_primary){
	  $renew_text="<a href='$renew_link'>to renew click here.</a>";
	}
	else{
	  "The primary member must be logged in to renew this membership.";
	}
	?>
  <?php if ($membership['status_id'] == 3) {
    echo "You should renew your membership as soon as possible - ";
    echo "$renew_text";
    } ?>
  <?php if ($membership['status_id'] == 4) {
    echo "Your must renew this to continue to access content on the members' site - ";
    echo "$renew_text";
    } ?>
  </p>
  <p><?php echo $more_info; ?></p>