<!-- The details of a specific product for editing, based on the product ID -->

<?php
$Field_ID = isset($_GET['Field_ID']) ? intval($_GET['Field_ID']) : 0;
$Field = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ewd_feup_fields_table_name WHERE Field_ID = %d", $Field_ID));
?>

<div class="OptionTab ActiveTab" id="EditField">
	<div class="form-wrap EditField">
		<a href="admin.php?page=EWD-FEUP-options&amp;DisplayPage=Field" class="NoUnderline">&#171; <?php esc_html_e("Back", 'front-end-only-users'); ?></a>
		<h3><?php esc_html_e('Edit', 'front-end-only-users'); ?> <?php echo esc_html($Field->Field_Name); ?></h3>
		<form id="addtag" method="post" action="admin.php?page=EWD-FEUP-options&amp;Action=EWD_FEUP_EditField&amp;DisplayPage=Field" class="validate" enctype="multipart/form-data">
			<input type="hidden" name="action" value="Edit_Field" />
			<?php wp_nonce_field('EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce'); ?>
			<?php wp_referer_field(); ?>
			<input type='hidden' name='Field_ID' value='<?php echo esc_attr($Field->Field_ID); ?>'>
			
			<div class="form-field">
				<label for="Field_Name"><?php esc_html_e("Name", 'front-end-only-users'); ?></label>
				<input name="Field_Name" class='ewd-admin-regular-text' id="Field_Name" type="text" value="<?php echo esc_attr($Field->Field_Name); ?>" size="60" />
			</div>
			
			<div class="form-field">
				<label for="Field_Slug"><?php esc_html_e("Slug", 'front-end-only-users'); ?></label>
				<input name="Field_Slug" class='ewd-admin-regular-text' id="Field_Slug" type="text" value="<?php echo esc_attr($Field->Field_Slug); ?>" size="60" />
				<p><?php esc_html_e("The slug of the field your users will see (lower-case letters and dashes only).", 'front-end-only-users'); ?></p>
			</div>
			
			<div class="form-field">
				<label for="Field_Type"><?php esc_html_e("Type", 'front-end-only-users'); ?></label>
				<select name="Field_Type" id="Field_Type">
					<option value='text' <?php selected($Field->Field_Type, 'text'); ?>><?php esc_html_e('Short Text', 'front-end-only-users'); ?></option>
					<option value='mediumint' <?php selected($Field->Field_Type, 'mediumint'); ?>><?php esc_html_e('Integer', 'front-end-only-users'); ?></option>
					<option value='picture' <?php selected($Field->Field_Type, 'picture'); ?>><?php esc_html_e('Profile Picture', 'front-end-only-users'); ?></option>
					<option value='select' <?php selected($Field->Field_Type, 'select'); ?>><?php esc_html_e('Select Box', 'front-end-only-users'); ?></option>
					<option value='radio' <?php selected($Field->Field_Type, 'radio'); ?>><?php esc_html_e('Radio Button', 'front-end-only-users'); ?></option>
					<option value='checkbox' <?php selected($Field->Field_Type, 'checkbox'); ?>><?php esc_html_e('Checkbox', 'front-end-only-users'); ?></option>
					<option value='textarea' <?php selected($Field->Field_Type, 'textarea'); ?>><?php esc_html_e('Text Area', 'front-end-only-users'); ?></option>
					<option value='file' <?php selected($Field->Field_Type, 'file'); ?>><?php esc_html_e('File', 'front-end-only-users'); ?></option>
					<option value='date' <?php selected($Field->Field_Type, 'date'); ?>><?php esc_html_e('Date', 'front-end-only-users'); ?></option>
					<option value='datetime' <?php selected($Field->Field_Type, 'datetime'); ?>><?php esc_html_e('Date/Time', 'front-end-only-users'); ?></option>
					<option value='countries' <?php selected($Field->Field_Type, 'countries'); ?>><?php esc_html_e('Country Select', 'front-end-only-users'); ?></option>
					<option value='email' <?php selected($Field->Field_Type, 'email'); ?>><?php esc_html_e('Email', 'front-end-only-users'); ?></option>
					<option value='tel' <?php selected($Field->Field_Type, 'tel'); ?>><?php esc_html_e('Telephone', 'front-end-only-users'); ?></option>
					<option value='url' <?php selected($Field->Field_Type, 'url'); ?>><?php esc_html_e('Website', 'front-end-only-users'); ?></option>
					<option value='label' <?php selected($Field->Field_Type, 'label'); ?>><?php esc_html_e('Label (No field, just a message)', 'front-end-only-users'); ?></option>
				</select>
				<p><?php esc_html_e("The input method for the field and type of data that the field will hold.", 'front-end-only-users'); ?></p>
			</div>
			
			<div class="form-field">
				<label for="Field_Description"><?php esc_html_e("Description", 'front-end-only-users'); ?></label>
				<textarea name="Field_Description" class='ewd-admin-large-text' id="Field_Description" rows="2" cols="40"><?php echo esc_textarea($Field->Field_Description); ?></textarea>
			</div>
			
			<div>
				<label for="Field_Options"><?php esc_html_e("Input Values", 'front-end-only-users'); ?></label>
				<input name="Field_Options" class='ewd-admin-regular-text' id="Field_Options" type="text" value="<?php echo esc_attr($Field->Field_Options); ?>" size="60" />
				<p><?php esc_html_e("A comma-separated list of acceptable input values for this field. These values will be the options for select, checkbox, and radio inputs. All values will be accepted if left blank.", 'front-end-only-users'); ?></p>
			</div>
			
			<div>
				<label for="Field_Show_In_Admin"><?php esc_html_e("Show in Admin Table?", 'front-end-only-users'); ?></label>
				<input type='radio' name="Field_Show_In_Admin" value="Yes" <?php checked($Field->Field_Show_In_Admin, "Yes"); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
				<input type='radio' name="Field_Show_In_Admin" value="No" <?php checked($Field->Field_Show_In_Admin, "No"); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
			</div>
			
			<div>
				<label for="Field_Show_In_Front_End"><?php esc_html_e("Show in User Profile", 'front-end-only-users'); ?></label>
				<input type='radio' name="Field_Show_In_Front_End" value="Yes" <?php checked($Field->Field_Show_In_Front_End, "Yes"); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
				<input type='radio' name="Field_Show_In_Front_End" value="No" <?php checked($Field->Field_Show_In_Front_End, "No"); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
			</div>
			
			<div>
				<label for="Field_Required"><?php esc_html_e("Make Field Required?", 'front-end-only-users'); ?></label>
				<input type='radio' name="Field_Required" value="Yes" <?php checked($Field->Field_Required, "Yes"); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
				<input type='radio' name="Field_Required" value="No" <?php checked($Field->Field_Required, "No"); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
				<p><?php esc_html_e("Are users required to fill out this field?", 'front-end-only-users'); ?></p>
			</div>
			
			<div class="form-field">
				<label for="Field_Equivalent"><?php esc_html_e("Field Meaning", 'front-end-only-users'); ?></label>
				<select name="Field_Equivalent" id="Field_Equivalent">
					<option value='None' <?php selected($Field->Field_Equivalent, 'None'); ?>><?php esc_html_e('None', 'front-end-only-users'); ?></option>
					<option value='First_Name' <?php selected($Field->Field_Equivalent, 'First_Name'); ?>><?php esc_html_e('First Name', 'front-end-only-users'); ?></option>
					<option value='Last_Name' <?php selected($Field->Field_Equivalent, 'Last_Name'); ?>><?php esc_html_e('Last Name', 'front-end-only-users'); ?></option>
					<?php if ($Username_Is_Email == "No") { ?>
						<option value='Email' <?php selected($Field->Field_Equivalent, 'Email'); ?>><?php esc_html_e('Email', 'front-end-only-users'); ?></option>
					<?php } ?>
					<option value='Phone' <?php selected($Field->Field_Equivalent, 'Phone'); ?>><?php esc_html_e('Phone', 'front-end-only-users'); ?></option>
					<option value='Address' <?php selected($Field->Field_Equivalent, 'Address'); ?>><?php esc_html_e('Address', 'front-end-only-users'); ?></option>
					<option value='City' <?php selected($Field->Field_Equivalent, 'City'); ?>><?php esc_html_e('City', 'front-end-only-users'); ?></option>
					<option value='Province' <?php selected($Field->Field_Equivalent, 'Province'); ?>><?php esc_html_e('Province', 'front-end-only-users'); ?></option>
					<option value='Country' <?php selected($Field->Field_Equivalent, 'Country'); ?>><?php esc_html_e('Country', 'front-end-only-users'); ?></option>
					<option value='Postal_Code' <?php selected($Field->Field_Equivalent, 'Postal_Code'); ?>><?php esc_html_e('Postal Code', 'front-end-only-users'); ?></option>
				</select>
				<p><?php esc_html_e("The meaning of the field. This field is only necessary if WordPress users are being created using the plugin, or if data is being pulled from Facebook.", 'front-end-only-users'); ?></p>
			</div>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Edit Field', 'front-end-only-users'); ?>"  /></p>
		</form>
	</div>
</div>
