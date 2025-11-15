<?php 
	// Sanitize and escape all settings and outputs
	$Admin_Email = sanitize_email(get_option("EWD_FEUP_Admin_Email"));
	$Email_Field = sanitize_text_field(get_option("EWD_FEUP_Email_Field"));
	$Password_Reset_Email = sanitize_text_field(get_option("EWD_FEUP_Password_Reset_Email"));
	$Username_Is_Email = sanitize_text_field(get_option("EWD_FEUP_Username_Is_Email"));

	$Email_Messages_Array = get_option("EWD_FEUP_Email_Messages_Array");
	if (!is_array($Email_Messages_Array)) {$Email_Messages_Array = array();}

	$Email_Reminder_Background_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Background_Color"));
	$Email_Reminder_Inner_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Inner_Color"));
	$Email_Reminder_Text_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Text_Color"));
	$Email_Reminder_Button_Background_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Button_Background_Color"));
	$Email_Reminder_Button_Text_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Button_Text_Color"));
	$Email_Reminder_Button_Background_Hover_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Button_Background_Hover_Color"));
	$Email_Reminder_Button_Text_Hover_Color = sanitize_hex_color(get_option("EWD_FEUP_Email_Reminder_Button_Text_Hover_Color"));

	$Mailchimp_Integration = sanitize_text_field(get_option("EWD_FEUP_Mailchimp_Integration"));
	$Mailchimp_API_Key = sanitize_text_field(get_option("EWD_FEUP_Mailchimp_API_Key"));
	$Mailchimp_List_ID = sanitize_text_field(get_option("EWD_FEUP_Mailchimp_List_ID"));
	$Mailchimp_Sync_Fields = get_option("EWD_FEUP_Mailchimp_Sync_Fields");
	if (!is_array($Mailchimp_Sync_Fields)) {$Mailchimp_Sync_Fields = array();}

	$Levels = $wpdb->get_results("SELECT * FROM $ewd_feup_levels_table_name ORDER BY Level_Privilege ASC");
	$Fields = $wpdb->get_results("SELECT Field_Name, Field_ID FROM $ewd_feup_fields_table_name");

	$UWPM_Banner_Time = intval(get_option("EWD_FEUP_UWPM_Ask_Time"));
	if ($UWPM_Banner_Time == "") {$UWPM_Banner_Time = 0;}
?>
<div class="wrap">
<h2><?php esc_html_e('Email Settings', 'front-end-only-users'); ?></h2>

<?php if (time() > $UWPM_Banner_Time) { ?>
	<br />
	<div class="ewd-feup-uwpm-banner">
		<div class="ewd-feup-uwpm-banner-remove"><span>X</span></div>
		<div class="ewd-feup-uwpm-banner-icon">
			<img src='<?php echo esc_url(EWD_FEUP_CD_PLUGIN_URL . "/images/ewd-uwpm-icon.png"); ?>' alt="<?php esc_attr_e('Ultimate WP Mail icon', 'front-end-only-users'); ?>" />
		</div>
		<div class="ewd-feup-uwpm-banner-text">
			<div class="ewd-feup-uwpm-banner-title">
				<?php _e("Customize Your Emails With", 'front-end-only-users'); ?>
				<span><?php esc_html_e('Ultimate WP Mail', 'front-end-only-users'); ?></span>
			</div>
			<ul>
				<li><?php esc_html_e('Completely FREE', 'front-end-only-users'); ?></li>
				<li><?php esc_html_e('Uses Shortcodes and Variables', 'front-end-only-users'); ?></li>
				<li><?php esc_html_e('Integrates Seamlessly', 'front-end-only-users'); ?></li>
				<li><?php esc_html_e('Custom Subject Lines For Each Email', 'front-end-only-users'); ?></li>
				<li><?php esc_html_e('Visual Builder', 'front-end-only-users'); ?></li>
				<li><?php esc_html_e('An Easy Email Experience', 'front-end-only-users'); ?></li>
			</ul>
			<div class="ewd-feup-clear"></div>
		</div>
		<div class="ewd-feup-uwpm-banner-buttons">
			<a class="ewd-feup-uwpm-banner-download-button" href='<?php echo esc_url('plugin-install.php?s=ultimate+wp+mail&tab=search&type=term'); ?>'>
				<?php _e("Download Now", 'front-end-only-users'); ?>
			</a>
			<span class="ewd-feup-uwpm-banner-reminder"><?php esc_html_e("Remind Me Later", 'front-end-only-users'); ?></span>
		</div>
		<div class="ewd-feup-clear"></div>
	</div>
	<br />
<?php } ?>

<div class="ewd-feup-shortcode-reminder-two">
	<?php echo wp_kses_post(__('<strong>REMINDER:</strong> If you\'re having trouble with sending emails, we recommend you use a plugin such as <a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">WP Mail SMTP</a> to configure your SMTP settings.', 'front-end-only-users')); ?>
</div>

<form method="post" action="admin.php?page=EWD-FEUP-options&DisplayPage=Emails&Action=EWD_FEUP_UpdateEmailSettings">
<?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' );  ?>

<br />

<div class="ewd-feup-admin-section-heading"><?php esc_html_e('Emails', 'front-end-only-users'); ?></div>

<table class="form-table">
<?php if ($Username_Is_Email == "No") { ?>
<tr>
<th scope="row"><?php esc_html_e('Email Field Name', 'front-end-only-users'); ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Email Field Name', 'front-end-only-users'); ?></span></legend>
	<label title='<?php esc_attr_e('Email Field Name', 'front-end-only-users'); ?>'>
		<select name='email_field'> 
			<?php foreach ($Fields as $Field) { ?>
				<option value='<?php echo esc_attr($Field->Field_Name); ?>' <?php selected($Field->Field_Name, $Email_Field); ?>><?php echo esc_html($Field->Field_Name); ?></option>
			<?php } ?>
		</select>
	</label><br />
	<p><?php esc_html_e('The name of the field that should be used to send the email to for your registration form, if "Username is Email" on the "Options" tab isn\'t set to "Yes". Note: this field can be left blank if "Username is Email" is set to "Yes".', 'front-end-only-users'); ?></p>
	</fieldset>
</td>
</tr>
<?php } ?>
<tr>
<th scope="row"><?php esc_html_e('Admin Email', 'front-end-only-users'); ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Admin Email', 'front-end-only-users'); ?></span></legend>
	<label title='<?php esc_attr_e('Admin Email', 'front-end-only-users'); ?>'><input type='text' name='admin_email' value='<?php echo esc_attr($Admin_Email); ?>' /> </label><br />
	<p><?php esc_html_e('If "Admin Email on Registration" is set to "Yes", what email address should the notification email be sent to?', 'front-end-only-users'); ?></p>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row" class="ewd-feup-admin-no-info-button"><?php esc_html_e('Password Reset Email', 'front-end-only-users'); ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Password Reset Email', 'front-end-only-users'); ?></span></legend>
		<select name='password_reset_email'>
			<?php foreach ($Email_Messages_Array as $Email_Message_Item) { ?>
				<option value='<?php echo esc_attr($Email_Message_Item['ID']); ?>' <?php selected($Password_Reset_Email, $Email_Message_Item['ID']); ?>><?php echo esc_html($Email_Message_Item['Name']); ?></option>
			<?php } ?>
			<optgroup label='Ultimate WP Mail'>
				<?php $UWPM_Emails = get_posts(array('post_type' => 'uwpm_mail_template', 'posts_per_page' => -1));
					foreach ($UWPM_Emails as $Email) { ?>
						<option value='-<?php echo esc_attr($Email->ID); ?>' <?php selected($Password_Reset_Email * -1, $Email->ID); ?>><?php echo esc_html($Email->post_title); ?></option>
				<?php } ?>
			</optgroup>
		</select>
	</fieldset>
</td>
</tr>

<tr class="ewd-feup-email-options-table-border ewd-feup-email-options-table-spacer">
	<th class="ewd-feup-admin-no-info-button"></th>
	<td></td>
</tr>
<tr class="ewd-feup-email-options-table-spacer">
	<th class="ewd-feup-admin-no-info-button"></th>
	<td></td>
</tr>

<tr>
	<th scope="row" class="ewd-feup-admin-no-info-button"><?php esc_html_e('Email Messages', 'front-end-only-users'); ?></th>
	<td>
		<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Email Messages', 'front-end-only-users'); ?></span></legend>
		<table id='ewd-feup-email-messages-table'>
			<tr>
				<th class="ewd-feup-admin-no-info-button"><?php esc_html_e('Email Name', 'front-end-only-users'); ?></th>
				<th class="ewd-feup-admin-no-info-button"><?php esc_html_e('Message Subject', 'front-end-only-users'); ?></th>
				<th class="ewd-feup-admin-no-info-button"><?php esc_html_e('Message', 'front-end-only-users'); ?></th>
				<th class="ewd-feup-admin-no-info-button"></th>
			</tr>
			<?php
				$Counter = 0;
				$Max_ID = 0;
				foreach ($Email_Messages_Array as $Email_Message_Item) {
					echo "<tr id='ewd-feup-email-message-" . esc_attr($Counter) . "'>";
						echo "<td><input class='ewd-feup-array-text-input' type='text' name='Email_Message_" . esc_attr($Counter) . "_Name' value='" . esc_attr($Email_Message_Item['Name']) . "'/></td>";
						echo "<td><input class='ewd-feup-array-text-input' type='text' name='Email_Message_" . esc_attr($Counter) . "_Subject' value='" . esc_attr($Email_Message_Item['Subject']) . "'/></td>";
						echo "<td><textarea class='ewd-feup-array-textarea' name='Email_Message_" . esc_attr($Counter) . "_Body' rows='5'>" . esc_textarea(stripslashes($Email_Message_Item['Message'])) . "</textarea></td>";
						echo "<td><input type='hidden' name='Email_Message_" . esc_attr($Counter) . "_ID' value='" . esc_attr($Email_Message_Item['ID']) . "' /><a class='ewd-feup-delete-message' data-messagecounter='" . esc_attr($Counter) . "'>Delete</a></td>";
					echo "</tr>";
					$Counter++;
					$Max_ID = max($Max_ID, $Email_Message_Item['ID']);
				}
				$Max_ID++;
				echo "<tr><td colspan='3'><a class='ewd-feup-add-email ewd-feup-admin-new-add-button' data-nextcounter='" . esc_attr($Counter) . "' data-maxid='" . esc_attr($Max_ID) . "'>&plus; " . esc_html__('ADD', 'front-end-only-users') . "</a></td></tr>";
			?>
		</table>
		<ul>
			<li><?php esc_html_e('Use the table above to build emails for your users.', 'front-end-only-users'); ?></li>
			<li><?php esc_html_e("You can use [section]...[/section] and [footer]...[/footer] to split up the content of your email. You can also include a link button, like so: [button link='LINK_URL_GOES_HERE']BUTTON_TEXT[/button]", 'front-end-only-users'); ?></li>
			<li><?php esc_html_e("You can also put any of the field values for the fields you've created in the \"Fields\" tab by putting in [field-slug] (the field's slug surrounded by square brackets).", 'front-end-only-users'); ?></li>
			<li><?php esc_html_e('Use the area at the bottom of the page to send yourself a sample email.', 'front-end-only-users'); ?></li>
		</ul>
		</fieldset>
	</td>
</tr>

<tr class="ewd-feup-email-options-table-border ewd-feup-email-options-table-spacer">
	<th class="ewd-feup-admin-no-info-button"></th>
	<td></td>
</tr>
<tr class="ewd-feup-email-options-table-spacer">
	<th class="ewd-feup-admin-no-info-button"></th>
	<td></td>
</tr>

<tr>
	<th scope="row"><?php esc_html_e('Send Sample Email', 'front-end-only-users'); ?></th>
	<td>
		<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Send Sample Email', 'front-end-only-users'); ?></span></legend>
			<div class="ewd-feup-send-sample-email-labels"><?php esc_html_e('Select Email:', 'front-end-only-users'); ?></div>
			<select class='ewd-feup-test-email-selector'>
				<?php foreach ($Email_Messages_Array as $Email_Message_Item) { ?>
					<option value="<?php echo esc_attr($Email_Message_Item['ID']); ?>"><?php echo esc_html($Email_Message_Item['Name']); ?></option>
				<?php } ?>
			</select><br/>
			<div class="ewd-feup-send-sample-email-labels"><?php esc_html_e('Email Address:', 'front-end-only-users'); ?></div>
			<input type='text' class='ewd-feup-test-email-address' />
			<p><button type='button' class='ewd-feup-send-test-email'><?php esc_html_e('Send Sample Email', 'front-end-only-users'); ?></button></p>
			<p><?php esc_html_e('Make sure that you click the "Save Changes" button below before sending the test message, to receive the most recent version of your email.', 'front-end-only-users'); ?></p>
		</fieldset>
	</td>
</tr>
</table>

<br />

<div class="ewd-feup-admin-section-heading"><?php esc_html_e('Premium Email Options', 'front-end-only-users'); ?></div>

<div class="ewd-feup-admin-styling-section">
	<div class="ewd-feup-admin-styling-subsection noBottomBorder">
		<div class="ewd-feup-admin-styling-subsection-label"><?php esc_html_e('Send Email to Users', 'front-end-only-users'); ?></div>
		<div class="ewd-feup-admin-styling-subsection-content">
			<div class="ewd-feup-admin-styling-subsection-content-each">
				<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Send Email to Users', 'front-end-only-users'); ?></span></legend>
					<div class="ewd-feup-send-sample-email-labels"><?php esc_html_e('Select Email:', 'front-end-only-users'); ?></div>
					<select class='ewd-feup-email-blast-selector'>
						<?php foreach ($Email_Messages_Array as $Email_Message_Item) { ?>
							<option value="<?php echo esc_attr($Email_Message_Item['ID']); ?>"><?php echo esc_html($Email_Message_Item['Name']); ?></option>
						<?php } ?>
					</select><br/>
					<div class="ewd-feup-send-sample-email-labels"><?php esc_html_e('Select User Level:', 'front-end-only-users'); ?></div>
					<select class='ewd-feup-blast-level-selector'>
						<option value="0"><?php esc_html_e('All Levels', 'front-end-only-users'); ?></option>
						<?php  foreach ($Levels as $Level) { ?>
							<option value='<?php echo esc_attr($Level->Level_ID); ?>' ><?php echo esc_html($Level->Level_Name); ?></option>
						<?php } ?>
					</select><br/>
					<p><button type='button' class='ewd-feup-send-email-blast' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?>><?php esc_html_e('Send Email Blast', 'front-end-only-users'); ?></button></p>
					<p><?php esc_html_e('Make sure that you click the "Save Changes" button below before sending the test message, so users receive the most recent version of your email.', 'front-end-only-users'); ?></p>
				</fieldset>
			</div>
		</div>
	</div>
	<div class="ewd-feup-admin-styling-subsection">
	    <div class="ewd-feup-admin-styling-subsection-label"><?php esc_html_e('Colors', 'front-end-only-users'); ?></div>
	    <div class="ewd-feup-admin-styling-subsection-content">
	        <div class="ewd-feup-admin-styling-subsection-content-each">
	            <div class="ewd-feup-admin-styling-subsection-content-label"><?php esc_html_e('Email', 'front-end-only-users'); ?></div>
	            <div class="ewd-feup-admin-styling-subsection-content-right">
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Background', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_background_color' value='<?php echo esc_attr($Email_Reminder_Background_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Inner Background', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_inner_color' value='<?php echo esc_attr($Email_Reminder_Inner_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Text', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_text_color' value='<?php echo esc_attr($Email_Reminder_Text_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	            </div>
	        </div>
	        <div class="ewd-feup-admin-styling-subsection-content-each">
	            <div class="ewd-feup-admin-styling-subsection-content-label"><?php esc_html_e('Button', 'front-end-only-users'); ?></div>
	            <div class="ewd-feup-admin-styling-subsection-content-right">
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Background', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_button_background_color' value='<?php echo esc_attr($Email_Reminder_Button_Background_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Text', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_button_text_color' value='<?php echo esc_attr($Email_Reminder_Button_Text_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Hover Background', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_button_background_hover_color' value='<?php echo esc_attr($Email_Reminder_Button_Background_Hover_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	                <div class="ewd-feup-admin-styling-subsection-content-color-picker">
	                    <div class="ewd-feup-admin-styling-subsection-content-color-picker-label"><?php esc_html_e('Hover Text', 'front-end-only-users'); ?></div>
	                    <input type='text' class='ewd-feup-spectrum' name='email_reminder_button_text_hover_color' value='<?php echo esc_attr($Email_Reminder_Button_Text_Hover_Color); ?>' <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "disabled";} ?> />
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php if ($EWD_FEUP_Full_Version != "Yes") { ?>
	    <div class="ewd-feup-premium-options-table-overlay">
	        <div class="ewd-feup-unlock-premium">
	            <img src="<?php echo esc_url(plugins_url( '../images/options-asset-lock.png', __FILE__ )); ?>" alt="<?php esc_attr_e('Upgrade to Front End Users Premium', 'front-end-only-users'); ?>">
	            <p><?php esc_html_e('Access this section by by upgrading to premium', 'front-end-only-users'); ?></p>
	            <a href="<?php echo esc_url('https://www.etoilewebdesign.com/plugins/front-end-only-users/#buy'); ?>" class="ewd-feup-dashboard-get-premium-widget-button" target="_blank"><?php esc_html_e('UPGRADE NOW', 'front-end-only-users'); ?></a>
	        </div>
	    </div>
	<?php } ?>
</div>

<br />

<div class="ewd-feup-admin-section-heading"><?php esc_html_e('MailChimp Integration Options', 'front-end-only-users'); ?></div>

<table class="form-table">
<tr>
	<th scope="row"><?php esc_html_e('Enable MailChimp Integration', 'front-end-only-users'); ?></th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php esc_html_e('Enable MailChimp Integration', 'front-end-only-users'); ?></span></legend>
			<div class="ewd-feup-admin-hide-radios">
				<label title="<?php esc_attr_e('Yes', 'front-end-only-users'); ?>">
					<input type='radio' name='mailchimp_integration' value='Yes' <?php checked($Mailchimp_Integration, "Yes"); ?> /> <span><?php esc_html_e('Yes', 'front-end-only-users'); ?></span>
				</label><br />
				<label title="<?php esc_attr_e('No', 'front-end-only-users'); ?>">
					<input type='radio' name='mailchimp_integration' value='No' <?php checked($Mailchimp_Integration, "No"); ?> /> <span><?php esc_html_e('No', 'front-end-only-users'); ?></span>
				</label><br />
			</div>
			<label class="ewd-feup-admin-switch">
				<input type="checkbox" class="ewd-feup-admin-option-toggle" data-inputname="mailchimp_integration" <?php checked($Mailchimp_Integration, "Yes"); ?>>
				<span class="ewd-feup-admin-switch-slider round"></span>
			</label>
			<p><?php esc_html_e('Should users automatically be added to your MailChimp email list when a new user is created?', 'front-end-only-users'); ?></p>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e('MailChimp API Key', 'front-end-only-users'); ?></th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php esc_html_e('MailChimp API Key', 'front-end-only-users'); ?></span></legend>
			<label title="<?php esc_attr_e('Mailchimp API Key', 'front-end-only-users'); ?>">
				<input type='text' name='mailchimp_api_key' value='<?php echo esc_attr($Mailchimp_API_Key); ?>' />
			</label><br />
			<p><?php esc_html_e('Create an API key for your Mailchimp account, and enter that key in the field above.', 'front-end-only-users'); ?></p>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e('MailChimp List ID', 'front-end-only-users'); ?></th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php esc_html_e('MailChimp List ID', 'front-end-only-users'); ?></span></legend>
			<label title="<?php esc_attr_e('Mailchimp List ID', 'front-end-only-users'); ?>">
				<input type='text' name='mailchimp_list_id' value='<?php echo esc_attr($Mailchimp_List_ID); ?>' />
			</label><br />
			<p><?php esc_html_e('What is the ID of the MailChimp list that you\'d like to add your users to?', 'front-end-only-users'); ?></p>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row" class="ewd-feup-admin-no-info-button"><?php esc_html_e('MailChimp Import Fields', 'front-end-only-users'); ?></th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php esc_html_e('MailChimp Import Fields', 'front-end-only-users'); ?></span></legend>
			<table id='ewd-feup-mc-fields-table'>
				<tr>
					<th class="ewd-feup-admin-no-info-button"><?php esc_html_e('Field Name', 'front-end-only-users'); ?></th>
					<th class="ewd-feup-admin-no-info-button"><?php esc_html_e('MailChimp Merge Field Tag', 'front-end-only-users'); ?></th>
					<th class="ewd-feup-admin-no-info-button"></th>
				</tr>
				<?php
				$Counter = 0;
				$Max_ID = 0;
				foreach ($Mailchimp_Sync_Fields as $Mailchimp_Sync_Field) {
					echo "<tr id='ewd-feup-mc-field-" . esc_attr($Counter) . "'>";
						echo "<td><select class='ewd-feup-array-select' name='Field_ID_" . esc_attr($Counter) . "'>";
						foreach ($Fields as $Field) {
							echo "<option value='" . esc_attr($Field->Field_ID) . "' " . selected($Mailchimp_Sync_Field['Field_ID'], $Field->Field_ID, false) . ">" . esc_html($Field->Field_Name) . "</option>";
						}
						echo "</select></td>";
						echo "<td><input class='ewd-feup-array-text-input' type='text' name='Mailchimp_Field_ID_" . esc_attr($Counter) . "' value='" . esc_attr($Mailchimp_Sync_Field['Mailchimp_Field_ID']) . "'/></td>";
						echo "<td><a class='ewd-feup-delete-mc-field' data-mcfieldcounter='" . esc_attr($Counter) . "'>" . esc_html__('Delete', 'front-end-only-users') . "</a></td>";
					echo "</tr>";
					$Counter++;
				}
				echo "<tr><td colspan='2'><a class='ewd-feup-add-mc-field ewd-feup-admin-new-add-button' data-nextcounter='" . esc_attr($Counter) . "'>&plus; " . esc_html__('ADD', 'front-end-only-users') . "</a></td></tr>";
				?>
			</table>
			<ul>
				<li><?php esc_html_e('Use the table above to select fields to import into MailChimp.', 'front-end-only-users'); ?></li>
			</ul>
		</fieldset>
	</td>
</tr>
</table>
<p class="submit"><input type="submit" name="Options_Submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'front-end-only-users'); ?>"  /></p>
</form>


</div>