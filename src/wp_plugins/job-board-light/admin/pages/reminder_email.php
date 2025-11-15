<div class="form-group">
	<label  class="col-md-2   control-label"> 
	<?php esc_html_e( 'Reminder Email Subject :', 'jobboard' );?> </label>
	<div class="col-md-4 ">
		<?php
			$jobboard_reminder_email_subject = get_option( 'jobboard_reminder_email_subject');
		?>
		<input type="text" class="form-control" id="jobboard_reminder_email_subject" name="jobboard_reminder_email_subject" value="<?php echo esc_attr($jobboard_reminder_email_subject); ?>" placeholder="Enter reminder email subject">
	</div>
</div>
<div class="form-group">
	<label  class="col-md-2   control-label">
	<?php esc_html_e( 'Reminder Email Tempalte :', 'jobboard' );?> </label>
	<div class="col-md-10 ">
		<?php
			$settings_a = array(															
			'textarea_rows' =>20,															 
			);
			$content_reminder = get_option( 'jobboard_reminder_email');
			$editor_id = 'reminder_email_template';
		?>
		<textarea id="reminder_email_template" name="reminder_email_template" rows="20" class="col-md-12 ">
			<?php echo esc_html($content_reminder); ?>
		</textarea>
	</div>
</div>
<div class="form-group">
	<label  class="col-md-2   control-label"> 
	<?php esc_html_e( 'Send Email Before # Days :', 'jobboard' );?> </label>
	<div class="col-md-4 ">
		<?php
			$jobboard_reminder_day = get_option( 'jobboard_reminder_day');
		?>
		<input type="text" class="form-control" id="jobboard_reminder_day" name="jobboard_reminder_day" value="<?php echo esc_attr($jobboard_reminder_day); ?>" placeholder="Enter number of day">
	</div>
</div>
<div class="row form-group">
	<label  class="col-md-2   control-label"> <?php  esc_html_e('Short Code:','jobboard');?>  </label>
	<div class="col-md-4 ">			
		<h4>  <span class="label label-info">[jobboard_reminder_email_cron] </span></h4>
	</div>
</div>