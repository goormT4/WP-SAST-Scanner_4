<div class="OptionTab ActiveTab" id="Statistics_Details">
<?php
	// Sanitize and decode the event target
	$Event_Target_Title = isset($_GET['Event_Target_Title']) ? urldecode( sanitize_text_field( $_GET['Event_Target_Title'] ) ) : '';

	$Current_Page = "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Statistics_Details&Selected=Event&Event_Target_Title=" . urlencode($Event_Target_Title);
	$Users_Page = "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User";

	$allowed_orderby = array( 'Event_Location_Title', 'Event_Date' );
	$OrderBy = ( isset( $_GET['OrderBy'] ) && in_array( $_GET['OrderBy'], $allowed_orderby ) ) ? $_GET['OrderBy'] : 'Event_Date';
	$Order = ( isset( $_GET['Order'] ) && $_GET['Order'] == 'DESC' ) ? 'DESC' : 'ASC';

	$Sql = "SELECT * FROM $ewd_feup_user_events_table_name WHERE Event_Target_Title=%s ";
	if (isset($_GET['Statistic_Type']) && $_GET['Statistic_Type'] == "Page_Loads") {
		$Sql .= "AND Event_Type='Page Load' ";
	} else {
		$Sql .= "AND Event_Type!='Page Load' ";
	}
	if (isset($_GET['OrderBy'])) {
		$Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
	} else {
		$Sql .= "ORDER BY Event_Date DESC ";
	}
	$myrows = $wpdb->get_results($wpdb->prepare($Sql, $Event_Target_Title));
	if (isset($_GET['OrderBy'])) {
		$Current_Page_With_Order_By = $Current_Page . "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
	} else {
		$Current_Page_With_Order_By = $Current_Page;
	}

	// Pagination setup
	$Page = isset($_GET['Page']) ? max(1, intval($_GET['Page'])) : 1;
	$EventCount = is_array($myrows) ? count($myrows) : 0;
	$Number_of_Pages = max(1, ceil($EventCount / 20));
?>

	<div class="OptionTab ActiveTab" id="EditProduct">
		<div class="tablenav top">
			<div class="alignleft actions">
				<p>
					<?php
					// esc_html_e is not for concatenated strings, so use esc_html() for variables and __() for the text
					echo esc_html( __("Recent Clicks on", 'front-end-only-users') . " " . $Event_Target_Title );
					?>
				</p>
			</div>
		</div>
		
		<table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">
			<thead>
				<tr>
					<th scope='col' class='manage-column column-cb check-column'>
						<span><?php esc_html_e('Username of Clicker', 'front-end-only-users'); ?></span>
					</th>
					<th scope='col' class='manage-column column-cb check-column'>
						<?php
							$loc_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Location_Title" && isset($_GET['Order']) && $_GET['Order'] == "ASC" ) ? "DESC" : "ASC";
						?>
						<a href="<?php echo esc_url($Current_Page . "&OrderBy=Event_Location_Title&Order=" . $loc_order); ?>">
							<span><?php esc_html_e('Event Location', 'front-end-only-users'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope='col' class='manage-column column-cb check-column'>
						<?php
							$date_order = ( isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Date" && isset($_GET['Order']) && $_GET['Order'] == "ASC" ) ? "DESC" : "ASC";
						?>
						<a href="<?php echo esc_url($Current_Page . "&OrderBy=Event_Date&Order=" . $date_order); ?>">
							<span><?php esc_html_e('Event Date', 'front-end-only-users'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
		
			<tfoot>
				<tr>
					<th scope='col' class='manage-column column-cb check-column'>
						<span><?php esc_html_e('Username of Clicker', 'front-end-only-users'); ?></span>
					</th>
					<th scope='col' class='manage-column column-cb check-column'>
						<a href="<?php echo esc_url($Current_Page . "&OrderBy=Event_Location_Title&Order=" . $loc_order); ?>">
							<span><?php esc_html_e('Event Location', 'front-end-only-users'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope='col' class='manage-column column-cb check-column'>
						<a href="<?php echo esc_url($Current_Page . "&OrderBy=Event_Date&Order=" . $date_order); ?>">
							<span><?php esc_html_e('Event Date', 'front-end-only-users'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
		
			<tbody id="the-list" class='list:tag'>
			<?php
				if ($myrows) {
					$start = ($Page - 1) * 20;
					$end = min($EventCount, $Page * 20);
					for ($i = $start; $i < $end; $i++) {
						$Event = $myrows[$i];
						$User_ID = isset($Event->User_ID) ? intval($Event->User_ID) : 0;
						$User_Event_ID = isset($Event->User_Event_ID) ? intval($Event->User_Event_ID) : 0;
						$Username = $wpdb->get_var( $wpdb->prepare( "SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID=%d", $User_ID ) );
						$Username = $Username ? esc_html($Username) : '';
						$Event_Location_Title = isset($Event->Event_Location_Title) ? esc_html($Event->Event_Location_Title) : '';
						$Event_Date = isset($Event->Event_Date) ? esc_html($Event->Event_Date) : '';
						echo "<tr id='Event-" . esc_attr($User_Event_ID) ."'>";
						echo "<td class='name column-username'><a href='" . esc_url($Users_Page . "&User_ID=" . $User_ID) . "'>" .  $Username . "</a></td>";
						echo "<td class='name column-location'>" . $Event_Location_Title . "</td>";
						echo "<td class='name column-date'>" . $Event_Date . "</td>";
						echo "</tr>";
					}
				}
			?>
			</tbody>
		</table>
		
		<div class="tablenav bottom">
			<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
				<span class="displaying-num"><?php echo esc_html($EventCount); ?> <?php _e("events", 'front-end-only-users'); ?></span>
				<span class='pagination-links'>
					<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=1"); ?>'>&laquo;</a>
					<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . max(1, $Page-1)); ?>'>&lsaquo;</a>
					<span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users'); ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
					<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . min($Number_of_Pages, $Page+1)); ?>'>&rsaquo;</a>
					<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By . "&Page=" . $Number_of_Pages); ?>'>&raquo;</a>
				</span>
			</div>
			<br class="clear" />
		</div>
	</div>
</div>
