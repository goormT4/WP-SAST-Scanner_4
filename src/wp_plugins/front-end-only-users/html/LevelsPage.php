<?php if ($EWD_FEUP_Full_Version == "Yes") { ?>
<div id="col-right">
<div class="col-wrap">

<!-- Display a list of the levels which have already been created -->
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<?php
	$Page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;

	$Sql = "SELECT * FROM $ewd_feup_levels_table_name ORDER BY Level_Privilege DESC ";
	$Levels = $wpdb->get_results($Sql);
	$num_rows = $wpdb->num_rows;
?>

<form action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_MassDeleteLevels&DisplayPage=Levels" method="post">
<div class="tablenav top">
	<div class="alignleft actions">
		<select name='action'>
  			<option value='-1' selected='selected'><?php esc_html_e("Bulk Actions", 'front-end-only-users') ?></option>
			<option value='delete'><?php esc_html_e("Delete", 'front-end-only-users') ?></option>
		</select>
		<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php esc_attr_e('Apply', 'front-end-only-users') ?>"  />
	</div>
</div>

<table class="wp-list-table striped widefat tags sorttable levels-list">
	<thead>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'><input type="checkbox" /></th>
			<th scope='col' id='level-name' class='manage-column column-name'><?php esc_html_e("Name", 'front-end-only-users') ?></th>
			<th><?php esc_html_e("User Count", 'front-end-only-users') ?></th>
			<th><?php esc_html_e("Privilege Level", 'front-end-only-users') ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'><input type="checkbox" /></th>
			<th scope='col' id='level-name' class='manage-column column-name'><?php esc_html_e("Name", 'front-end-only-users') ?></th>
			<th><?php esc_html_e("User Count", 'front-end-only-users') ?></th>
			<th><?php esc_html_e("Privilege Level", 'front-end-only-users') ?></th>
		</tr>
	</tfoot>
	<tbody id="the-list" class='list:tag'>
		<?php
			foreach ($Levels as $Level) {
				$user_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $ewd_feup_user_table_name WHERE Level_ID=%d", intval($Level->Level_ID) ) );
		?>
				<tr id="list-item-<?php echo esc_attr($Level->Level_ID); ?>" class="list-item">
					<th scope='row' class='check-column'>
						<input type='checkbox' name='Levels_Bulk[]' value='<?php echo esc_attr($Level->Level_ID); ?>' />
					</th>
					<td class="level-name">
						<a href='<?php echo esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Level_Details&Level_ID=" . intval($Level->Level_ID)); ?>'>
							<?php echo esc_html($Level->Level_Name); ?>
						</a><br />
						<div class='row-actions'><span class='delete'>
						<a class='delete-tag' href='<?php echo esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteLevel&DisplayPage=Levels&Level_ID=" . intval($Level->Level_ID)); ?>'>
							<?php esc_html_e("Delete", 'front-end-only-users'); ?>
						</a>
		 				</span></div>
					</td>
					<td class="level-user-count"><?php echo esc_html($user_count); ?></td>
					<td class="level-privilege-level"><?php echo esc_html($Level->Level_Privilege); ?></td>
				</tr>
			<?php } ?>
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
	<br class="clear" />
</div>
</form>

<br class="clear" />
</div>
</div> <!-- /col-right -->


<div id="col-left">
<div class="col-wrap">

<div class="form-wrap">
<h2><?php esc_html_e("Add New Level", 'front-end-only-users') ?></h2>
<!-- Form to create a new level -->
<form id="addtag" method="post" action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_AddLevel&DisplayPage=Levels" class="validate" enctype="multipart/form-data">
<input type="hidden" name="action" value="Add_Level" />
<?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' );  ?>
<?php wp_referer_field(); ?>
<div class="form-field form-required">
	<label for="Level_Name"><?php esc_html_e("Name", 'front-end-only-users') ?></label>
	<input name="Level_Name" id="Level_Name" type="text" value="" size="60" />
</div>
<div class="form-field">
	<label for="Level_Privilege"><?php esc_html_e("Privilege Level", 'front-end-only-users') ?></label>
	<select name="Level_Privilege" id="Level_Privilege">
		<?php 
			$Insert = intval($num_rows) + 1; 
			echo "<option value='" . esc_attr($Insert) . "'>" . esc_html($Insert) . "</option>";
			for ($i=1; $i<=10; $i++) {
				echo "<option value='" . esc_attr($i) . "'>" . esc_html($i) . "</option>";
			}
		?>
	</select>
	<p><?php esc_html_e("The privilege level for this user level. Privilege levels can affect who can see what content. Inserting a new level will increase the privilege level of all above levels.", 'front-end-only-users') ?></p>
</div>

<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Add New Level', 'front-end-only-users') ?>"  /></p></form>

</div>

<br class="clear" />
</div>
</div><!-- /col-left -->

<?php } else { ?>
<div class="Info-Div">
	<h2><?php esc_html_e("Full Version Required!", 'front-end-only-users') ?></h2>
	<div class="ewd-feup-full-version-explanation">
		<?php esc_html_e("The full version of Front-End Only Users is required to use tags.", 'front-end-only-users');?>
		<a href="http://www.etoilewebdesign.com/front-end-users-plugin/"><?php esc_html_e(" Please upgrade to unlock this page!", 'front-end-only-users'); ?></a>
	</div>
<?php } ?>
