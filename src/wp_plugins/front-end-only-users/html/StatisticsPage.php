<?php if ($EWD_FEUP_Full_Version != "Yes") { ?>
	<div class="Info-Div">
		<h2><?php _e("Full Version Required!", 'front-end-only-users') ?></h2>
		<div class="ewd-feup-full-version-explanation">
			<?php _e("The full version of Front-End Only Users is required to view statistics.", 'front-end-only-users');?><a href="http://www.etoilewebdesign.com/front-end-users-plugin/"><?php _e(" Please upgrade to unlock this page!", 'front-end-only-users'); ?></a>
		</div>
	</div>
<?php } else { ?>
<?php $Track_Events = get_option("EWD_FEUP_Track_Events"); ?>
<div id="col-right">
<div class="col-wrap">

<h3><?php esc_html_e('Link Statistics', 'front-end-only-users'); ?></h3>

<?php
	$Page = isset($_GET['Page']) ? max(1, intval($_GET['Page'])) : 1;

	$allowed_orderby = array('Event_Location_Title', 'Count(Event_Value)', 'Event_Date');
	$OrderBy = ( isset($_GET['OrderBy']) && in_array($_GET['OrderBy'], $allowed_orderby) ) ? $_GET['OrderBy'] : 'Count(Event_Value)';
	$Order = ( isset($_GET['Order']) && $_GET['Order'] == 'ASC' ) ? 'ASC' : 'DESC';

	$Sql = "SELECT * FROM $ewd_feup_user_events_table_name WHERE Event_Type='Page Load' ";
	$Sql .= "GROUP BY Event_Location ";
	if ( isset($_GET['OrderBy']) && isset($_GET['DisplayPage']) && $_GET['DisplayPage'] == "Statistics" ) {
		$Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
	} else {
		$Sql .= "ORDER BY Count(Event_Value) DESC ";
	}
	$Sql .= "LIMIT " . (($Page - 1) * 20) . ",20";
	$myrows = $wpdb->get_results($Sql);

	$num_rows = intval($wpdb->get_var("SELECT COUNT(DISTINCT Event_Location) FROM $ewd_feup_user_events_table_name WHERE Event_Type='Page Load'"));
	$Number_of_Pages = max(1, ceil($num_rows / 20));
	$Current_Page_With_Order_By = "admin.php?page=EWD-FEUP-options&DisplayPage=Statistics";
	if (isset($_GET['OrderBy'])) {
		$Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
	}
?>

<div class="tablenav top">
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
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
			<th scope='col' class='manage-column sortable desc'>
				<?php
					$loc_order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Location_Title" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Location_Title&Order={$loc_order}") . "'>";
				?>
					<span><?php _e("Page Title", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'>
				<?php
					$count_order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Count(Event_Value)" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Count(Event_Value)&Order={$count_order}") . "'>";
				?>
					<span><?php _e("Page Views", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'>
				<?php
					$date_order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Date" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Date&Order={$date_order}") . "'>";
				?>
					<span><?php _e("Last View", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope='col' class='manage-column sortable desc'>
				<?php
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Location_Title&Order={$loc_order}") . "'>";
				?>
					<span><?php _e("Page Title", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'>
				<?php
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Count(Event_Value)&Order={$count_order}") . "'>";
				?>
					<span><?php _e("Page Views", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'>
				<?php
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Date&Order={$date_order}") . "'>";
				?>
					<span><?php _e("Last View", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>

	<tbody id="the-list" class='list:tag'>
	<?php
		if ($myrows) {
			foreach ($myrows as $Link) {
				// Defensive code to avoid notices and ensure proper escaping
				$Event_Location = isset($Link->Event_Location) ? $Link->Event_Location : '';
				$Event_Location_Title = isset($Link->Event_Location_Title) ? $Link->Event_Location_Title : '';
				$Click_Count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(User_Event_ID) FROM $ewd_feup_user_events_table_name WHERE Event_Location=%s",
					$Event_Location
				) );
				$Last_Click = $wpdb->get_var( $wpdb->prepare(
					"SELECT Event_Date FROM $ewd_feup_user_events_table_name WHERE Event_Location=%s ORDER BY Event_Date DESC",
					$Event_Location
				) );
				echo "<tr id='User-" . esc_attr($Event_Location) ."'>";
				echo "<td class='name column-name'>";
				echo "<strong>";
				echo "<a class='row-title' href='" . esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Statistics_Details&Selected=User&Event_Target_Title=" . urlencode($Event_Location_Title) . "&Statistic_Type=Page_Loads") . "' title='" . esc_attr__('View', 'front-end-only-users') . " " . esc_attr($Event_Location_Title) . "'>";
				echo esc_html($Event_Location_Title) . "</a></strong>";
				echo "<div class='page-title'>" . esc_html($Event_Location_Title) . "</div>";
				echo "</td>";
				echo "<td class='description column-view-count'>" . esc_html($Click_Count) . "</td>";
				echo "<td class='users column-last-view'>" . esc_html($Last_Click) . "</td>";
				echo "</tr>";
			}
		}
	?>
	</tbody>
</table>

<div class="tablenav bottom">
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
		<span class="displaying-num"><?php echo esc_html($num_rows); ?> <?php _e("items", 'front-end-only-users'); ?></span>
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

<br class="clear" />


<h3><?php esc_html_e('Link Statistics', 'front-end-only-users'); ?></h3>

<?php
	$Page = isset($_GET['Page']) ? max(1, intval($_GET['Page'])) : 1;

	$allowed_orderby = array('Event_Target_Title', 'Count(Event_Value)', 'Event_Type', 'Event_Date');
	$OrderBy = ( isset($_GET['OrderBy']) && in_array($_GET['OrderBy'], $allowed_orderby) ) ? $_GET['OrderBy'] : 'Count(Event_Value)';
	$Order = ( isset($_GET['Order']) && $_GET['Order'] == 'ASC' ) ? 'ASC' : 'DESC';

	$Sql = "SELECT * FROM $ewd_feup_user_events_table_name WHERE Event_Type!='Page Load' ";
	$Sql .= "GROUP BY Event_Value ";
	if ( isset($_GET['OrderBy']) && isset($_GET['DisplayPage']) && $_GET['DisplayPage'] == "Statistics" ) {
		$Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
	} else {
		$Sql .= "ORDER BY Count(Event_Value) DESC ";
	}
	$Sql .= "LIMIT " . (($Page - 1) * 20) . ",20";
	$myrows = $wpdb->get_results($Sql);

	$num_rows = intval($wpdb->get_var("SELECT COUNT(DISTINCT Event_Value) FROM $ewd_feup_user_events_table_name WHERE Event_Type!='Page Load'"));
	$Number_of_Pages = max(1, ceil($num_rows / 20));
	$Current_Page_With_Order_By = "admin.php?page=EWD-FEUP-options&DisplayPage=Statistics";
	if (isset($_GET['OrderBy'])) {
		$Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
	}
?>

<div class="tablenav top">
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
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

<table class="wp-list-table widefat tags sorttable fields-list ui-sortable" cellspacing="0">
	<thead>
		<tr>
			<th scope='col' class='manage-column sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Target_Title" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Target_Title&Order={$order}") . "'>";
				?>
					<span><?php _e("Page Title/Link", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='type' class='manage-column column-type sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Type" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Type&Order={$order}") . "'>";
				?>
					<span><?php _e("Link Type", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Count(Event_Value)" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Count(Event_Value)&Order={$order}") . "'>";
				?>
					<span><?php _e("Total Clicks", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Date" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Date&Order={$order}") . "'>";
				?>
					<span><?php _e("Last Click", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope='col' class='manage-column sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Target_Title" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Target_Title&Order={$order}") . "'>";
				?>
					<span><?php _e("Page Title/Link", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='type' class='manage-column column-type sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Type" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Type&Order={$order}") . "'>";
				?>
					<span><?php _e("Link Type", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='description' class='manage-column column-description sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Count(Event_Value)" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Count(Event_Value)&Order={$order}") . "'>";
				?>
					<span><?php _e("Total Clicks", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='required' class='manage-column column-users sortable desc'>
				<?php
					$order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Date" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
					echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Event_Date&Order={$order}") . "'>";
				?>
					<span><?php _e("Last Click", 'front-end-only-users'); ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>

	<tbody id="the-list" class='list:tag'>
	<?php
		if ($myrows) {
			foreach ($myrows as $Link) {
				$Event_Value = isset($Link->Event_Value) ? $Link->Event_Value : '';
				$Event_Target_Title = isset($Link->Event_Target_Title) ? $Link->Event_Target_Title : '';
				$Event_Type = isset($Link->Event_Type) ? $Link->Event_Type : '';
				$Click_Count = $wpdb->get_var(
					$wpdb->prepare("SELECT COUNT(User_Event_ID) FROM $ewd_feup_user_events_table_name WHERE Event_Value=%s", $Event_Value)
				);
				$Last_Click = $wpdb->get_var(
					$wpdb->prepare("SELECT Event_Date FROM $ewd_feup_user_events_table_name WHERE Event_Value=%s ORDER BY Event_Date DESC", $Event_Value)
				);

				echo "<tr id='User-" . esc_attr($Event_Value) ."'>";
				echo "<td class='name column-name'>";
				echo "<strong>";
				echo "<a class='row-title' href='" . esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_Statistics_Details&Selected=User&Event_Target_Title=" . urlencode($Event_Target_Title)) . "' title='" . esc_attr__('View', 'front-end-only-users') . " " . esc_attr($Event_Target_Title) . "'>";
				echo esc_html($Event_Target_Title) . "</a></strong>";
				echo "<br />";
				echo "<div class='target-title'>" . esc_html($Event_Target_Title) . "</div>";
				echo "</td>";
				echo "<td class='description column-event-type'>" . esc_html($Event_Type) . "</td>";
				echo "<td class='description column-click-count'>" . esc_html($Click_Count) . "</td>";
				echo "<td class='users column-last-click'>" . esc_html($Last_Click) . "</td>";
				echo "</tr>";
			}
		}
	?>
	</tbody>
</table>

<div class="tablenav bottom">
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
		<span class="displaying-num"><?php echo esc_html($num_rows); ?> <?php _e("items", 'front-end-only-users'); ?></span>
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

<br class="clear" />


<h3><?php esc_html_e('User Activity Table', 'front-end-only-users'); ?></h3>

<?php
$Page = isset($_GET['Page']) ? max(1, intval($_GET['Page'])) : 1;

$allowed_orderby = array('Username', 'User_Last_Login', 'User_Total_Logins', 'User_Date_Created');
$OrderBy = (isset($_GET['OrderBy']) && in_array($_GET['OrderBy'], $allowed_orderby)) ? $_GET['OrderBy'] : 'User_Last_Login';
$Order = (isset($_GET['Order']) && $_GET['Order'] == 'ASC') ? 'ASC' : 'DESC';

$Sql = "SELECT * FROM $ewd_feup_user_table_name ";
if (isset($_GET['OrderBy']) && isset($_GET['DisplayPage']) && $_GET['DisplayPage'] == "Statistics") {
    $Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
} else {
    $Sql .= "ORDER BY User_Last_Login DESC ";
}
$Sql .= "LIMIT " . (($Page - 1) * 20) . ",20";
$myrows = $wpdb->get_results($Sql);

$TotalFields = $wpdb->get_results("SELECT User_ID FROM $ewd_feup_user_table_name");
$num_rows = $wpdb->num_rows;
$Number_of_Pages = max(1, ceil($num_rows / 20));
$Current_Page_With_Order_By = "admin.php?page=EWD-FEUP-options&DisplayPage=Statistics";
if (isset($_GET['OrderBy']) && isset($_GET['Order'])) {
    $Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
}
?>

<div class="tablenav top">
    <div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
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

<table class="wp-list-table widefat tags sorttable fields-list ui-sortable" cellspacing="0">
    <thead>
        <tr>
            <th scope='col' class='manage-column sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Username" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Username&Order={$order}") . "'>";
                ?>
                <span><?php _e("Username", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='type' class='manage-column column-type sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Last_Login" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Last_Login&Order={$order}") . "'>";
                ?>
                <span><?php _e("Last Login", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='description' class='manage-column column-description sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Total_Logins" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Total_Logins&Order={$order}") . "'>";
                ?>
                <span><?php _e("Total Logins", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='required' class='manage-column column-users sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Date_Created" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Date_Created&Order={$order}") . "'>";
                ?>
                <span><?php _e("Joined Date", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th scope='col' class='manage-column sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Username" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=Username&Order={$order}") . "'>";
                ?>
                <span><?php _e("Username", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='type' class='manage-column column-type sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Last_Login" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Last_Login&Order={$order}") . "'>";
                ?>
                <span><?php _e("Last Login", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='description' class='manage-column column-description sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Total_Logins" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Total_Logins&Order={$order}") . "'>";
                ?>
                <span><?php _e("Total Logins", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' id='required' class='manage-column column-users sortable desc' style="">
                <?php
                $order = (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Date_Created" && isset($_GET['Order']) && $_GET['Order'] == "ASC") ? "DESC" : "ASC";
                echo "<a href='" . esc_url("admin.php?page=EWD-FEUP-options&DisplayPage=Statistics&OrderBy=User_Date_Created&Order={$order}") . "'>";
                ?>
                <span><?php _e("Joined Date", 'front-end-only-users'); ?></span>
                <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </tfoot>
    <tbody id="the-list" class='list:tag'>
    <?php
    if ($myrows) {
        foreach ($myrows as $User) {
            echo "<tr id='User-" . esc_attr($User->User_ID) . "'>";
            echo "<td class='name column-name'>";
            echo "<strong>";
            echo "<a class='row-title' href='" . esc_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User&User_ID=" . urlencode($User->User_ID)) . "' title='" . esc_attr__('Edit', 'front-end-only-users') . " " . esc_attr($User->Username) . "'>";
            echo esc_html($User->Username) . "</a></strong>";
            echo "<br />";
            echo "<div class='username'>" . esc_html($User->Username) . "</div>";
            echo "</td>";
            echo "<td class='description column-last-login'>" . esc_html($User->User_Last_Login) . "</td>";
            echo "<td class='description column-description'>" . esc_html($User->User_Total_Logins) . "</td>";
            echo "<td class='users column-required'>" . esc_html($User->User_Date_Created) . "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>

<div class="tablenav bottom">
    <div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
        <span class="displaying-num"><?php echo esc_html($num_rows); ?> <?php _e("items", 'front-end-only-users'); ?></span>
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

<br class="clear" />

</div>
</div> <!-- /col-right -->


<div id="col-left">
<div class="col-wrap">

<div class="form-wrap">
<h2><?php _e("Summary Statistics", 'front-end-only-users'); ?></h2>
<?php
    $wpdb->show_errors();
    $Total_Logins = $wpdb->get_var("SELECT SUM(User_Total_Logins) FROM $ewd_feup_user_table_name");
    $Total_Page_Loads = $wpdb->get_var("SELECT COUNT(User_Event_ID) FROM $ewd_feup_user_events_table_name WHERE Event_Type='Page Load'");
    $Most_Viewed_Page = $wpdb->get_var("SELECT Event_Location FROM $ewd_feup_user_events_table_name WHERE Event_Type='Page Load' GROUP BY Event_Location ORDER BY Count(Event_Location) DESC LIMIT 1");
    $Most_Viewed_Link = $wpdb->get_var("SELECT Event_Value FROM $ewd_feup_user_events_table_name WHERE Event_Type='Link' GROUP BY Event_Value ORDER BY Count(Event_Value) DESC LIMIT 1");
    $Most_Viewed_Attachment = $wpdb->get_var("SELECT Event_Value FROM $ewd_feup_user_events_table_name WHERE Event_Type='Attachment' GROUP BY Event_Value ORDER BY Count(Event_Value) DESC LIMIT 1");
    $Most_Viewed_Image = $wpdb->get_var("SELECT Event_Value FROM $ewd_feup_user_events_table_name WHERE Event_Type='Image' GROUP BY Event_Value ORDER BY Count(Event_Value) DESC LIMIT 1");
?>
<div>
    <p><strong><?php esc_html_e('Total logins by users:', 'front-end-only-users'); ?></strong> <?php echo esc_html($Total_Logins); ?></p>
    <?php if ($Track_Events == "No") { ?>
        <h4><?php esc_html_e('To enable the statistics below, please set "Track User Activity" to "Yes" in the "Options" tab', 'front-end-only-users'); ?></h4>
    <?php } ?>
    <p><strong><?php esc_html_e('Total page loads by users:', 'front-end-only-users'); ?></strong> <?php echo esc_html($Total_Page_Loads); ?></p>
    <p><strong><?php esc_html_e('Most common page visited:', 'front-end-only-users'); ?></strong> <?php echo esc_html($Most_Viewed_Page); ?></p>
    <p><strong><?php esc_html_e('Most common link clicked:', 'front-end-only-users'); ?></strong><br/><?php echo esc_html($Most_Viewed_Link); ?></p>
    <p><strong><?php esc_html_e('Most common attachment clicked:', 'front-end-only-users'); ?></strong><br/><?php echo esc_html($Most_Viewed_Attachment); ?></p>
    <p><strong><?php esc_html_e('Most common image clicked:', 'front-end-only-users'); ?></strong><br/><?php echo esc_html($Most_Viewed_Image); ?></p>
</div>

<br class="clear" />
</div>
</div>
</div><!-- /col-left -->
<?php } ?>