<div id="col-full">
<div class="col-wrap">

<!-- Display a list of the products which have already been created -->
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<?php 
	$Page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;
	$allowed_orderby = array( 'Username', 'Payment_Date', 'Payment_Amount', 'Next_Payment_Date', 'Discount_Code_Used' );
	$OrderBy = ( isset( $_GET['OrderBy'] ) && in_array( $_GET['OrderBy'], $allowed_orderby ) ) ? $_GET['OrderBy'] : 'Username';
	$Order = ( isset( $_GET['Order'] ) && $_GET['Order'] === 'DESC' ) ? 'DESC' : 'ASC';
	
	$Sql = "SELECT * FROM $ewd_feup_payments_table_name ";
	if (isset($_GET['OrderBy']) && isset($_GET['DisplayPage']) && $_GET['DisplayPage'] == "Payments") {
		$Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
	} else {
		$Sql .= "ORDER BY Payment_Date ";
	}
	$Sql .= "LIMIT " . (($Page - 1)*20) . ",20";
	$myrows = $wpdb->get_results($Sql);

	// For pagination
	$TotalPayments = $wpdb->get_results("SELECT Payment_ID FROM $ewd_feup_payments_table_name");
	$num_rows = count($TotalPayments);
	$Number_of_Pages = ceil($num_rows/20);
	$Current_Page_With_Order_By = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments";
	if (isset($_GET['OrderBy'])) {
		$Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
	}
?>

<form action="admin.php?page=EWD-FEUP-options&amp;Action=EWD_FEUP_MassDeletePayments&amp;DisplayPage=Payments" method="post">    
<div class="tablenav top">
	<div class="alignleft actions">
		<select name='action'>
			<option value='-1' selected='selected'><?php _e("Bulk Actions", 'front-end-only-users'); ?></option>
			<option value='delete'><?php _e("Delete", 'front-end-only-users'); ?></option>
		</select>
		<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php esc_attr_e('Apply', 'front-end-only-users'); ?>" />
	</div>
	<div class='tablenav-pages <?php echo ($Number_of_Pages == 1 ? "one-page" : ""); ?>'>
		<span class="displaying-num"><?php echo esc_html($num_rows); ?> <?php _e("items", 'front-end-only-users'); ?></span>
		<span class='pagination-links'>
			<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=1"); ?>'>&laquo;</a>
			<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . max(1, $Page-1)); ?>'>&lsaquo;</a>
			<span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users'); ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
			<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . min($Number_of_Pages, $Page+1)); ?>'>&rsaquo;</a>
			<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . $Number_of_Pages); ?>'>&raquo;</a>
		</span>
	</div>
</div>

<table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">
	<thead>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<input type="checkbox" />
			</th>
			<th scope='col' id='field-name' class='manage-column column-name sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Username" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Username&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Username", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='type' class='manage-column column-type sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Payment_Date" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Payment_Date&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Payment Date", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Payment_Amount" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Payment_Amount&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Amount", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Next_Payment_Date" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Next_Payment_Date&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Next Payment", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Discount_Code_Used" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Discount_Code_Used&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Discount Code", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<tfoot>
		<!-- Same as thead, repeat code for column headers with sorting links -->
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<input type="checkbox" />
			</th>
			<th scope='col' id='field-name' class='manage-column column-name sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Username" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Username&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Username", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='type' class='manage-column column-type sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Payment_Date" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Payment_Date&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Payment Date", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Payment_Amount" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Payment_Amount&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Amount", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Next_Payment_Date" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Next_Payment_Date&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Next Payment", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'  style="">
				<?php
					$current_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] === "Discount_Code_Used" && isset($_GET['Order']) && $_GET['Order'] === "ASC" ) ? "DESC" : "ASC";
					$url = "admin.php?page=EWD-FEUP-options&DisplayPage=Payments&OrderBy=Discount_Code_Used&Order=" . $current_order;
				?>
				<a href="<?php echo esc_url($url); ?>">
					<span><?php _e("Discount Code", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>

	<tbody id="the-list" class='list:tag'>
		<?php
			if ($myrows) { 
				foreach ($myrows as $Payment) {
					$payment_id = isset($Payment->Payment_ID) ? intval($Payment->Payment_ID) : 0;
					$username = isset($Payment->Username) ? esc_html($Payment->Username) : '';
					$payment_date = isset($Payment->Payment_Date) ? esc_html($Payment->Payment_Date) : '';
					$payment_amount = isset($Payment->Payment_Amount) ? esc_html($Payment->Payment_Amount) : '';
					$next_payment_date = isset($Payment->Next_Payment_Date) ? esc_html($Payment->Next_Payment_Date) : '';
					$discount_code_used = isset($Payment->Discount_Code_Used) ? esc_html($Payment->Discount_Code_Used) : '';
					
					echo "<tr id='list-item-{$payment_id}' class='list-item'>";
					echo "<th scope='row' class='check-column'>";
					echo "<input type='checkbox' name='Payments_Bulk[]' value='" . esc_attr($payment_id) . "' />";
					echo "</th>";
					echo "<td class='name column-name'>";
					echo "<strong>";
					echo "<a class='row-title' href='" . esc_url( "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Payment_Details&Payment_ID={$payment_id}" ) . "' title='" . esc_attr__('View', 'front-end-only-users') . " {$username}'>" . $username . "</a></strong>";
					echo "<br />";
					echo "<div class='row-actions'>";
					echo "<span class='delete'>";
					echo "<a class='delete-tag' href='" . esc_url( "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeletePayment&DisplayPage=Payments&Payment_ID={$payment_id}" ) . "'>" . esc_html__("Delete", 'front-end-only-users') . "</a>";
					echo "</span>";
					echo "</div>";
					echo "<div class='hidden' id='inline_{$payment_id}'>";
					echo "<div class='name'>{$username}</div>";
					echo "</div>";
					echo "</td>";
					echo "<td class='description column-date'>{$payment_date}</td>";
					echo "<td class='users column-amount'>{$payment_amount}</td>";
					echo "<td class='users column-next-date'>{$next_payment_date}</td>";
					echo "<td class='users column-discount'>{$discount_code_used}</td>";
					echo "</tr>";
				}
			}
		?>
	</tbody>
</table>
</form>
</div>
</div>
