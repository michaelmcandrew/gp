<p>My membership type is <b><?php echo $membership['membership_name']; ?></b>
	<!--  TEMP REMOVED UNTIL 1950 ERROR IS FIXED WITH MEMBERSHIPS
	Start date:  <b><?php echo $membership['start_date']; ?></b></br>
	-->
	<?php
	
	//need this line until the logic below is finished
	$date_text = "ends on";

	if ( /*current date is greater than end date*/ ) {
      //$date_text = "ended on";
  }	
  else{
   // $date_text = "ends on";
  }
	?>
	<?php if ($membership['end_date']){ ?>
	and <?php echo $date_text; ?> <b><?php echo $membership['end_date']; ?></b><?php } ?>.</p>

	<?php if ($membership_payment_is_recurring == 1) { ?>
  	<p>I'm currently paying by <b>direct debit/standing order</b>.</p>
	  <p>My payment frequency is <b><?php echo strtolower($membership_payment_frequency); ?></b>.</p>
	<?php } ?>
  
  <?php if ($membership_status_name) { ?>
  	<p>My membership status is <b><?php echo strtolower($membership_status_name); ?></b>.
	<?php } ?>
	
	<?php
	if ($is_primary){
	  $renew_text="<a href='<?php echo $renew_link; ?>'>To renew click here.</a>";
	}
	else{
	  "The primary member must be logged in to renew this membership.";
	}
	?>
  <?php if ($membership['status_id'] == 3) {
    echo "You should renew your membership as soon as possible.";
    echo "  $renew_text";
    } ?>
  <?php if ($membership['status_id'] == 4) {
    echo "Your must renew this to continue to access content on the members' site.";
    echo "  $renew_text";
    } ?>
  </p>
  <p><?php echo $more_info; ?></p>

	<!--  Split membership to first check status ID then write a nice sentence for each.	-->