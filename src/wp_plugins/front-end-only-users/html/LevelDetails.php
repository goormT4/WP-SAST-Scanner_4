<?php
$Level_ID = isset($_GET['Level_ID']) ? intval($_GET['Level_ID']) : 0;
$Level = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ewd_feup_levels_table_name WHERE Level_ID = %d", $Level_ID ) );
$Fields = $wpdb->get_results( "SELECT Field_Name, Field_ID, Level_Exclude_IDs FROM $ewd_feup_fields_table_name" );
$Users = $wpdb->get_results( $wpdb->prepare( "SELECT User_ID, Username FROM $ewd_feup_user_table_name WHERE Level_ID = %d", $Level_ID ) );
?>

<div class="OptionTab ActiveTab" id="EditLevel">
	<div id='col-right'>
		<div class='col-wrap'>
			<div class="tablenav top">
				<div class="alignleft actions">
					<p><?php esc_html_e("Users at this Level", 'front-end-only-users'); ?></p>
				</div>
			</div>
				
			<table id="ewd-feup-users-in-level-table" class="wp-list-table widefat tags fields-list ui-sortable" cellspacing="0">
				<thead>
					<tr>
						<th><?php esc_html_e('Users', 'front-end-only-users'); ?></th>
					</tr>
				</thead>
				<tbody id="the-list" class='list:tag'>			
				<?php
					if ($Users) { 
				  		foreach ($Users as $User) {
							echo "<tr id='Event-" . esc_attr($User->User_ID) ."'>";
							echo "<td class='name column-type'><a href='" . esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User&User_ID=" . intval($User->User_ID)) . "'>" .  esc_html($User->Username) . "</a></td>";
							echo "</tr>";
						}
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th><?php esc_html_e('Users', 'front-end-only-users'); ?></th>
					</tr>
				</tfoot>
			</table>

		</div>
	</div>

	<div id='col-left'>
		<div class='col-wrap'>
			<div class="form-wrap EditLevel">
				<a href="admin.php?page=EWD-FEUP-options&DisplayPage=Levels" class="NoUnderline">&#171; <?php esc_html_e("Back", 'front-end-only-users') ?></a>
				<h3><?php esc_html_e('Edit', 'front-end-only-users'); ?> <?php echo esc_html($Level->Level_Name);?></h3>
				<form id="addtag" method="post" action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_EditLevel&DisplayPage=Levels" class="validate" enctype="multipart/form-data">
					<input type="hidden" name="action" value="Edit_Level" />
					<?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' );  ?>
					<?php wp_referer_field(); ?>
					<input type='hidden' name='Level_ID' value='<?php echo esc_attr($Level->Level_ID); ?>'>
					<div class="form-field form-required">
						<label for="Level_Name"><?php esc_html_e("Name", 'front-end-only-users') ?></label>
						<input name="Level_Name" id="Level_Name" type="text" value="<?php echo esc_attr($Level->Level_Name); ?>" size="60" />
					</div>
					<div class="form-field">
						<label for="Level_Privilege"><?php esc_html_e("Privilege Level", 'front-end-only-users') ?></label>
						<select name="Level_Privilege" id="Level_Privilege">
							<?php
								$num_rows = $wpdb->get_var("SELECT COUNT(*) FROM $ewd_feup_levels_table_name");
								$Insert = intval($num_rows) + 1;
								echo "<option value='" . esc_attr($Insert) . "'>" . esc_html($Insert) . "</option>";
								for ($i=1; $i<=10; $i++) {
									echo "<option value='" . esc_attr($i) . "'";
									if ($Level->Level_Privilege == $i) { echo " selected"; }
									echo ">" . esc_html($i) . "</option>";
								}
							?>
						</select>
						<p><?php esc_html_e("The privilege level for this user level. Privilege levels can affect who can see what content. Inserting a new level will increase the privilege level of all above levels.", 'front-end-only-users') ?></p>
					</div>
					<h3><?php esc_html_e("Level Sign Up Fields", 'front-end-only-users'); ?></h3>
					<p><?php esc_html_e("Uncheck any fields that shouldn't be included for this level.", 'front-end-only-users'); ?></p>
					<?php foreach ($Fields as $Field) { ?>
						<?php $Field_Level_Exclude_IDs = @unserialize($Field->Level_Exclude_IDs); ?>
						<div class="form-field">
							<input type='checkbox' name="Field_IDs[]" value='<?php echo esc_attr($Field->Field_ID); ?>' <?php if (!is_array($Field_Level_Exclude_IDs) or !in_array($Level->Level_ID, $Field_Level_Exclude_IDs)) {echo "checked";} ?> />
							<label for="Field_ID"><?php echo esc_html($Field->Field_Name); ?></label>
						</div>
					<?php } ?>
					<input type='submit' name='Level_Details_Save' value='<?php esc_attr_e('Save Level', 'front-end-only-users'); ?>' />
				</form>
			</div>
		</div>
	</div>
</div>
