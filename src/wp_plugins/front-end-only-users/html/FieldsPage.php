<?php
$Username_Is_Email = get_option("EWD_FEUP_Username_Is_Email");

$valid_orderbys = array('Field_Name', 'Field_Type', 'Field_Description', 'Field_Required');
$OrderBy = (isset($_GET['OrderBy']) && in_array($_GET['OrderBy'], $valid_orderbys)) ? sanitize_text_field($_GET['OrderBy']) : 'Field_Name';
$Order = (isset($_GET['Order']) && $_GET['Order'] === 'DESC') ? 'DESC' : 'ASC';
$Page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;

$Current_Page_With_Order_By = "admin.php?page=EWD-FEUP-options&DisplayPage=Fields";
if (isset($_GET['OrderBy'])) {
    $Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
}

// Build query safely
if (isset($_GET['OrderBy']) && isset($_GET['DisplayPage']) && $_GET['DisplayPage'] == "Fields" && in_array($OrderBy, $valid_orderbys)) {
    $Sql = $wpdb->prepare(
        "SELECT * FROM $ewd_feup_fields_table_name ORDER BY $OrderBy $Order LIMIT %d, 200",
        ($Page - 1) * 200
    );
} else {
    $Sql = $wpdb->prepare(
        "SELECT * FROM $ewd_feup_fields_table_name ORDER BY Field_Order LIMIT %d, 200",
        ($Page - 1) * 200
    );
}
$myrows = $wpdb->get_results($Sql);
$TotalFields = $wpdb->get_results("SELECT Field_ID FROM $ewd_feup_fields_table_name");
$num_rows = $wpdb->num_rows;
$Number_of_Pages = ceil($num_rows / 200);

?>
<div id="col-right">
<div class="col-wrap">

<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<form action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_MassDeleteFields&DisplayPage=Fields" method="post">    
<div class="tablenav top">
		<div class="alignleft actions">
				<select name='action'>
  					<option value='-1' selected='selected'><?php esc_html_e("Bulk Actions", 'front-end-only-users') ?></option>
						<option value='delete'><?php esc_html_e("Delete", 'front-end-only-users') ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php esc_attr_e('Apply', 'front-end-only-users') ?>"  />
		</div>
		<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
				<span class="displaying-num"><?php echo esc_html($wpdb->num_rows); ?> <?php esc_html_e("items", 'front-end-only-users') ?></span>
				<span class='pagination-links'>
						<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=1"); ?>'>&laquo;</a>
						<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . ($Page-1)); ?>'>&lsaquo;</a>
						<span class="paging-input"><?php echo esc_html($Page); ?> <?php esc_html_e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
						<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . ($Page+1)); ?>'>&rsaquo;</a>
						<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . $Number_of_Pages); ?>'>&raquo;</a>
				</span>
		</div>
</div>

<table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">
		<thead>
				<tr>
        		    <th scope='col' class='manage-column column-cb check-column'>
        		      <input type="checkbox" /></th>
        		    <?php
        		    foreach ($valid_orderbys as $orderby_option) {
        		      $new_order = ($OrderBy === $orderby_option && $Order === 'ASC') ? 'DESC' : 'ASC';
        		      $link = esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Fields&OrderBy={$orderby_option}&Order={$new_order}");
        		      $label = ucwords(str_replace('Field_', '', $orderby_option));
        		      echo "<th scope='col' class='manage-column sortable desc'><a href='{$link}'><span>" . esc_html(__($label, 'front-end-only-users')) . "</span><span class='sorting-indicator'></span></a></th>";
        		    }
        		    ?>
        		</tr>
		</thead>

		<tfoot>
				<tr>
        		    <th scope='col' class='manage-column column-cb check-column'>
        		      <input type="checkbox" /></th>
        		    <?php
        		    foreach ($valid_orderbys as $orderby_option) {
        		      $new_order = ($OrderBy === $orderby_option && $Order === 'ASC') ? 'DESC' : 'ASC';
        		      $link = esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Fields&OrderBy={$orderby_option}&Order={$new_order}");
        		      $label = ucwords(str_replace('Field_', '', $orderby_option));
        		      echo "<th scope='col' class='manage-column sortable desc'><a href='{$link}'><span>" . esc_html(__($label, 'front-end-only-users')) . "</span><span class='sorting-indicator'></span></a></th>";
        		    }
        		    ?>
        		</tr>
		</tfoot>

	<tbody id="the-list" class='list:tag'>
        <?php
        if ($myrows) {
            foreach ($myrows as $Field) {
              $field_id = intval($Field->Field_ID);
              echo "<tr id='list-item-{$field_id}' class='list-item'>";
              echo "<th scope='row' class='check-column'><input type='checkbox' name='Fields_Bulk[]' value='" . esc_attr($field_id) . "' /></th>";
              echo "<td class='name column-name'><strong><a class='row-title' href='" . esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Field_Details&Selected=Product&Field_ID={$field_id}") . "' title='" . esc_attr__('Edit ', 'front-end-only-users') . esc_attr($Field->Field_Name) . "'>" . esc_html($Field->Field_Name) . "</a></strong>";
              echo "<div class='row-actions'><span class='delete'><a class='delete-tag' href='" . esc_url(add_query_arg('_wp_nonce', wp_create_nonce('ewd-feup-admin'), "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteField&DisplayPage=Fields&Field_ID={$field_id}")) . "'>" . esc_html__('Delete', 'front-end-only-users') . "</a></span></div>";
              echo "<div class='hidden' id='inline_{$field_id}'><div class='name'>" . esc_html($Field->Field_Name) . "</div></div></td>";
              echo "<td class='description column-type'>" . esc_html(ucfirst($Field->Field_Type)) . "</td>";
              echo "<td class='description column-description'>" . esc_html(mb_strimwidth($Field->Field_Description, 0, 60, '...')) . "</td>";
              echo "<td class='users column-required'>" . esc_html($Field->Field_Required) . "</td>";
              echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>

<div class="tablenav bottom">
		<div class="alignleft actions">
				<select name='action'>
  					<option value='-1' selected='selected'><?php esc_html_e("Bulk Actions", 'front-end-only-users') ?></option>
						<option value='delete'><?php esc_html_e("Delete", 'front-end-only-users') ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php esc_attr_e('Apply', 'front-end-only-users') ?>"  />
		</div>
		<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
				<span class="displaying-num"><?php echo esc_html($wpdb->num_rows); ?> <?php esc_html_e("items", 'front-end-only-users') ?></span>
				<span class='pagination-links'>
						<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=1"); ?>'>&laquo;</a>
						<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . ($Page-1)); ?>'>&lsaquo;</a>
						<span class="paging-input"><?php echo esc_html($Page); ?> <?php esc_html_e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
						<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . ($Page+1)); ?>'>&rsaquo;</a>
						<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . $Number_of_Pages); ?>'>&raquo;</a>
				</span>
		</div>
		<br class="clear" />
</div>
</form>

<br class="clear" />
</div>
</div> <!-- /col-right -->


<!-- Form to upload a list of new products from a spreadsheet -->
<div id="col-left">
<div class="col-wrap">

<div class="form-wrap">
<h2><?php esc_html_e("Add New Field", 'front-end-only-users') ?></h2>
<!-- Form to create a new field -->
<form id="addtag" method="post" action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_AddField&DisplayPage=Field" class="validate" enctype="multipart/form-data">
<input type="hidden" name="action" value="Add_Field" />
<?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' );  ?>
<?php wp_referer_field(); ?>
<div class="form-field">
	<label for="Field_Name"><?php esc_html_e("Name", 'front-end-only-users') ?></label>
	<input name="Field_Name" id="Field_Name" type="text" value="" size="60" />
</div>
<div class="form-field">
	<label for="Field_Slug"><?php esc_html_e("Slug", 'front-end-only-users') ?></label>
	<input name="Field_Slug" id="Field_Slug" type="text" value="" size="60" />
	<p><?php esc_html_e("The slug of the field your users will see (lower-case letters and dashes only).", 'front-end-only-users') ?></p>
</div>
<div class="form-field">
	<label for="Field_Type"><?php esc_html_e("Type", 'front-end-only-users') ?></label>
	<select name="Field_Type" id="Field_Type">
			<option value='text'><?php esc_html_e('Short Text', 'front-end-only-users'); ?></option>
			<option value='mediumint'><?php esc_html_e('Integer', 'front-end-only-users'); ?></option>
			<option value='picture'><?php esc_html_e('Profile Picture', 'front-end-only-users'); ?></option>
			<option value='select'><?php esc_html_e('Select Box', 'front-end-only-users'); ?></option>
			<option value='radio'><?php esc_html_e('Radio Button', 'front-end-only-users'); ?></option>
			<option value='checkbox'><?php esc_html_e('Checkbox', 'front-end-only-users'); ?></option>
			<option value='textarea'><?php esc_html_e('Text Area', 'front-end-only-users'); ?></option>
			<option value='file'><?php esc_html_e('File', 'front-end-only-users'); ?></option>
			<option value='date'><?php esc_html_e('Date', 'front-end-only-users'); ?></option>
			<option value='datetime'><?php esc_html_e('Date/Time', 'front-end-only-users'); ?></option>
			<option value='countries'><?php esc_html_e('Country Select', 'front-end-only-users'); ?></option>
			<option value='email'><?php esc_html_e('Email', 'front-end-only-users'); ?></option>
			<option value='tel'><?php esc_html_e('Telephone', 'front-end-only-users'); ?></option>
			<option value='url'><?php esc_html_e('URL', 'front-end-only-users'); ?></option>			
			<option value='label'><?php esc_html_e('Label (No field, just a message)', 'front-end-only-users'); ?></option>
	</select>
	<p><?php esc_html_e("The input method for the field and type of data that the field will hold.", 'front-end-only-users') ?></p>
</div>
<div class="form-field">
	<label for="Field_Description"><?php esc_html_e("Description", 'front-end-only-users') ?></label>
	<textarea name="Field_Description" id="Field_Description" rows="2" cols="40"></textarea>
</div>
<div class="form-field">
		<label for="Field_Options"><?php esc_html_e("Input Values", 'front-end-only-users') ?></label>
		<input name="Field_Options" id="Field_Options" type="text" size="60" />
		<p><?php esc_html_e("A comma-separated list of acceptable input values for this field. These values will be the options for select, checkbox, and radio inputs. All values will be accepted if left blank.", 'front-end-only-users') ?></p>
</div>
<div class="form-field">
		<label for="Field_Show_In_Admin"><?php esc_html_e("Show in Admin Table?", 'front-end-only-users') ?></label>
		<input type='radio' name="Field_Show_In_Admin" value="Yes"><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
		<input type='radio' name="Field_Show_In_Admin" value="No" checked><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
</div>
<div class="form-field">
		<label for="Field_Show_In_Front_End"><?php esc_html_e("Show in User Profile", 'front-end-only-users') ?></label>
		<input type='radio' name="Field_Show_In_Front_End" value="Yes" checked><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
		<input type='radio' name="Field_Show_In_Front_End" value="No"><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
</div>
<div class="form-field">
		<label for="Field_Required"><?php esc_html_e("Make Field Required?", 'front-end-only-users') ?></label>
		<input type='radio' name="Field_Required" value="Yes"><?php esc_html_e('Yes', 'front-end-only-users'); ?><br/>
		<input type='radio' name="Field_Required" value="No" checked><?php esc_html_e('No', 'front-end-only-users'); ?><br/>
		<p><?php esc_html_e("Are users required to fill out this field?", 'front-end-only-users') ?></p>
</div>
<div class="form-field">
	<label for="Field_Equivalent"><?php esc_html_e("Field Meaning", 'front-end-only-users') ?></label>
	<select name="Field_Equivalent" id="Field_Equivalent">
			<option value='None'><?php esc_html_e('None', 'front-end-only-users'); ?></option>
			<option value='First_Name'><?php esc_html_e('First Name', 'front-end-only-users'); ?></option>
			<option value='Last_Name'><?php esc_html_e('Last Name', 'front-end-only-users'); ?></option>
			<?php if ($Username_Is_Email == "No") { ?><option value='Email'><?php esc_html_e('Email', 'front-end-only-users'); ?></option><?php } ?>
			<option value='Phone'><?php esc_html_e('Phone', 'front-end-only-users'); ?></option>
			<option value='Address'><?php esc_html_e('Address', 'front-end-only-users'); ?></option>
			<option value='City'><?php esc_html_e('City', 'front-end-only-users'); ?></option>
			<option value='Province'><?php esc_html_e('Province', 'front-end-only-users'); ?></option>
			<option value='Country'><?php esc_html_e('Country', 'front-end-only-users'); ?></option>
			<option value='Postal_Code'><?php esc_html_e('Postal Code', 'front-end-only-users'); ?></option>
	</select>
	<p><?php esc_html_e("The meaning of the field. This field is only necessary if WordPress users are being created using the plugin.", 'front-end-only-users') ?></p>
</div>

<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Add New Field', 'front-end-only-users') ?>"  /></p></form>

</div>

<br class="clear" />
</div>
</div><!-- /col-left -->
