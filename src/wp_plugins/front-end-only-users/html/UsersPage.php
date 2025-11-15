<?php 
// Securely get options with appropriate defaults
$Admin_Approval = get_option("EWD_FEUP_Admin_Approval", "No");
$Username_Is_Email = get_option("EWD_FEUP_Username_Is_Email", "No");
$Include_WP_Users = get_option("EWD_FEUP_Include_WP_Users", "No");

// Sanitize and validate input parameters
$Page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;
$Valid_OrderBy_Fields = array('User_Admin_Approved', 'User_WP_ID', 'User_Last_Login', 'User_Date_Created');
$OrderBy = isset($_GET['OrderBy']) ? sanitize_text_field($_GET['OrderBy']) : 'User_Date_Created';
$OrderBy = in_array($OrderBy, $Valid_OrderBy_Fields) || ewd_feup_field_name_exists($OrderBy) ? $OrderBy : 'User_Date_Created';
$Order = (isset($_GET['Order']) && strtoupper($_GET['Order']) === 'DESC') ? 'DESC' : 'ASC';

// Escape SQL table names (defined elsewhere)
$Fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}EWD_FEUP_Fields WHERE Field_Show_In_Admin = %s", 'Yes'));
$AllFields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}EWD_FEUP_Fields");

// Build SQL safely
$where_clauses = array();
if (!empty($_REQUEST['UserSearchValue'])) {
    $search_field = sanitize_text_field($_REQUEST['UserSearchField']);
    $search_operator = $_REQUEST['UserSearchOperator'] === 'LIKE' ? 'LIKE' : '=';
    $search_value = sanitize_text_field($_REQUEST['UserSearchValue']);

    if ($search_field === 'Username') {
        $where_clauses[] = $wpdb->prepare("Username $search_operator %s", $search_operator === 'LIKE' ? "%$search_value%" : $search_value);
    } else {
        $where_clauses[] = $wpdb->prepare("Field_ID = %d AND Field_Value $search_operator %s", intval($search_field), $search_operator === 'LIKE' ? "%$search_value%" : $search_value);
    }
}
$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql = "SELECT DISTINCT uf.User_ID FROM {$wpdb->prefix}EWD_FEUP_Users uf ";
$sql .= "INNER JOIN {$wpdb->prefix}EWD_FEUP_User_Fields uff ON uf.User_ID = uff.User_ID ";
$sql .= $where_sql . " ";

if (isset($_GET['OrderBy']) && $_GET['DisplayPage'] === 'Users') {
    if (in_array($OrderBy, $Valid_OrderBy_Fields)) {
        $sql .= "ORDER BY $OrderBy $Order ";
    } else {
        $OrderBy_Field_ID = $wpdb->get_var($wpdb->prepare("SELECT Field_ID FROM {$wpdb->prefix}EWD_FEUP_Fields WHERE Field_Name=%s", $OrderBy));
        if ($OrderBy_Field_ID) {
            $User_IDs = $wpdb->get_results($wpdb->prepare("SELECT User_ID FROM {$wpdb->prefix}EWD_FEUP_User_Fields WHERE Field_ID=%d ORDER BY Field_Value $Order", $OrderBy_Field_ID));
            $OrderBy_User_IDs = array_map('intval', wp_list_pluck($User_IDs, 'User_ID'));
            if (!empty($OrderBy_User_IDs)) {
                $sql .= "ORDER BY FIELD(uf.User_ID, " . implode(",", $OrderBy_User_IDs) . ") ";
            }
        }
    }
} else {
    $sql .= "ORDER BY User_Date_Created ";
}

$RowCount = $wpdb->get_results($sql);
$UserCount = $wpdb->num_rows;
$Number_of_Pages = ceil($UserCount / 20);
$sql .= $wpdb->prepare("LIMIT %d, %d", ($Page - 1) * 20, 20);
$myrows = $wpdb->get_results($sql);

// Escape URLs properly
$Current_Page_With_Order_By = esc_url_raw(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users"));
if (!empty($OrderBy)) {
    $Current_Page_With_Order_By .= "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
}
if (!empty($_REQUEST['UserSearchValue'])) {
    $Current_Page_With_Order_By .= "&UserSearchValue=" . rawurlencode(sanitize_text_field($_REQUEST['UserSearchValue']));
    $Current_Page_With_Order_By .= "&UserSearchField=" . rawurlencode(sanitize_text_field($_REQUEST['UserSearchField']));
    $Current_Page_With_Order_By .= "&UserSearchOperator=" . rawurlencode(sanitize_text_field($_REQUEST['UserSearchOperator']));
}
?>

<div id="col-right" class="feup-overlfow-auto ewd-feup-admin-products-table-full">
<div class="col-wrap">

<div class="ewd-feup-admin-new-product-page-top-part">
    <div class="ewd-feup-admin-new-product-page-top-part-left">
        <h3 class="ewd-feup-admin-new-tab-headings"><?php _e('Add New User', 'front-end-only-users'); ?></h3>    
        <div class="ewd-feup-admin-add-new-product-buttons-area">
            <a href="<?php echo esc_url(admin_url('admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details')); ?>" class="button-primary ewd-feup-admin-add-new-product-button" id="ewd-feup-admin-manually-add-product-button"><?php _e('Manually', 'front-end-only-users'); ?></a>
            <div class="button-primary ewd-feup-admin-add-new-product-button" id="ewd-feup-admin-add-by-spreadsheet-button"><?php _e('From Spreadsheet', 'front-end-only-users'); ?></div>
        </div>
    </div>
    <div class="ewd-feup-admin-new-product-page-top-part-right">
        <h3 class="ewd-feup-admin-new-tab-headings"><?php _e('Export Users to Spreadsheet', 'front-end-only-users'); ?></h3>    
        <div class="ewd-feup-admin-export-buttons-area">
            <?php if($EWD_FEUP_Full_Version == 'Yes'){ ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=EWD-FEUP-options&DisplayPage=Users&Action=EWD_FEUP_ExportToExcel')); ?>">
                    <input type="submit" name="Export_Submit" class="button button-secondary ewd-feup-admin-export-button" value="<?php esc_attr_e('Export to Excel', 'front-end-only-users'); ?>"  />
                </form>
            <?php } else{
                _e("The full version of the Front End Users plugin is required to export products.", 'front-end-only-users'); ?><br /><a href="https://www.etoilewebdesign.com/plugins/front-end-only-users/#buy" target="_blank" rel="noopener noreferrer"><?php _e("Please upgrade to unlock this feature!", 'front-end-only-users'); ?></a>
            <?php } ?>
        </div>
    </div>
</div>

<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<form action="<?php echo esc_url(add_query_arg( '_wp_nonce', wp_create_nonce( 'ewd-feup-admin' ), admin_url('admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_MassUserAction&DisplayPage=Users') )); ?>" method="post">
<p class="search-box">
    <label class="screen-reader-text" for="post-search-input"><?php esc_html_e("Search Users:", "front-end-only-users"); ?></label>
    <select name='UserSearchField' class='ewd-admin-select-search'>
        <option value='Username'><?php esc_html_e('Username', 'front-end-only-users'); ?></option>
        <?php
            foreach ($AllFields as $Field) {
                echo "<option value='" . esc_attr( $Field->Field_ID ) . "'>" . esc_html( $Field->Field_Name ) . "</option>";
            }
        ?>
    </select>
    <select name='UserSearchOperator' class='ewd-admin-select-search'>
        <option value='LIKE'><?php esc_html_e('Like', 'front-end-only-users'); ?></option>
        <option value='EQUALS'><?php esc_html_e('Equals', 'front-end-only-users'); ?></option>
    </select>
    <input type="search" id="post-search-input" name="UserSearchValue" value="">
    <input type="submit" name="" id="search-submit" class="button" value="<?php esc_attr_e('Search Users', 'front-end-only-users'); ?>">
    <br />
    <a class='feup-confirm-all-users button-secondary action ewd-feup-admin-delete-all-products-button' href='<?php echo esc_url(add_query_arg( '_wp_nonce', wp_create_nonce( 'ewd-feup-admin' ), admin_url('admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteAllUsers&DisplayPage=Users') )); ?>'><?php _e("Delete All Users", 'front-end-only-users'); ?></a>
</p>
<div class="tablenav top">
    <div class="alignleft actions">
        <select name='action'>
            <option value='-1' selected='selected'><?php _e("Bulk Actions", 'front-end-only-users') ?></option>
            <option value='delete'><?php _e("Delete", 'front-end-only-users') ?></option>
            <option value='approve'><?php _e("Approve", 'front-end-only-users') ?></option>
            <option value='0'><?php _e('Level: None (0)', 'front-end-only-users'); ?></option>
            <?php
                $Levels = $wpdb->get_results("SELECT * FROM $ewd_feup_levels_table_name");
                if (is_array($Levels)) {
                    foreach ($Levels as $Level) {
                        echo "<option value='" . esc_attr( $Level->Level_ID ) . "'>Level: " . esc_html( $Level->Level_Name ) . " (" . esc_html( $Level->Level_Privilege ) . ")</option>";
                    }
                }
            ?>
        </select>
        <input type="submit" name="" id="doaction" class="button-secondary action" value="<?php esc_attr_e('Apply', 'front-end-only-users') ?>"  />
    </div>
    <div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
        <span class="displaying-num"><?php echo esc_html( $UserCount ); ?> <?php _e("users", 'front-end-only-users') ?> <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "out of 100";}?></span>
        <span class='pagination-links'>
            <a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=1'>&laquo;</a>
            <a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page-1);?>'>&lsaquo;</a>
            <span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
            <a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page+1);?>'>&rsaquo;</a>
            <a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By) . "&Page=" . esc_attr( $Number_of_Pages ); ?>'>&raquo;</a>
        </span>
    </div>
</div>

<table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">
    <thead>
        <tr>
            <th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
                <input type="checkbox" />
            </th>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <span><?php esc_html_e('Username', 'front-end-only-users'); ?></span>
            </th>
            <?php if ($Admin_Approval == "Yes") { ?>
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Admin_Approved" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Admin_Approved&Order=" . esc_attr($Order))); ?>">
                        <span><?php esc_html_e('Admin Approved', 'front-end-only-users'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <?php foreach ($Fields as $Field) { ?>
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == $Field->Field_Name && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=" . esc_attr($Field->Field_Name) . "&Order=" . esc_attr($Order))); ?>">
                        <span><?php echo esc_html($Field->Field_Name); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <?php if ($Include_WP_Users == "Yes" ) { ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_WP_ID" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                          else {$Order = "ASC";} ?>
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_WP_ID&Order=" . esc_attr($Order))); ?>">
                        <span><?php esc_html_e('WordPress User?', 'front-end-only-users'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Last_Login" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Last_Login&Order=" . esc_attr($Order))); ?>">
                    <span><?php esc_html_e('Last Login', 'front-end-only-users'); ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Date_Created" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Date_Created&Order=" . esc_attr($Order))); ?>">
                    <span><?php esc_html_e('Joined Date', 'front-end-only-users'); ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </thead>
    
    <tfoot>
        <tr>
            <th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
                <input type="checkbox" />
            </th>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <span><?php esc_html_e('Username', 'front-end-only-users'); ?></span>
            </th>
            <?php if ($Admin_Approval == "Yes") { ?>
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Admin_Approved" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Admin_Approved&Order=" . esc_attr($Order))); ?>">
                        <span><?php esc_html_e('Admin Approved', 'front-end-only-users'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <?php foreach ($Fields as $Field) { ?>
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == $Field->Field_Name && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=" . esc_attr($Field->Field_Name) . "&Order=" . esc_attr($Order))); ?>">
                        <span><?php echo esc_html($Field->Field_Name); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <?php if ($Include_WP_Users == "Yes" ) { ?>
                <th scope='col' class='manage-column column-cb check-column'  style="">
                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_WP_ID" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                          else {$Order = "ASC";} ?>
                    <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_WP_ID&Order=" . esc_attr($Order))); ?>">
                        <span><?php esc_html_e('WordPress User?', 'front-end-only-users'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            <?php } ?>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Last_Login" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Last_Login&Order=" . esc_attr($Order))); ?>">
                    <span><?php esc_html_e('Last Login', 'front-end-only-users'); ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th scope='col' class='manage-column column-cb check-column'  style="">
                <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "User_Date_Created" && $_GET['Order'] == "ASC") {$Order = "DESC";}
                      else {$Order = "ASC";} ?>
                <a href="<?php echo esc_url(admin_url("admin.php?page=EWD-FEUP-options&DisplayPage=Users&OrderBy=User_Date_Created&Order=" . esc_attr($Order))); ?>">
                    <span><?php esc_html_e('Joined Date', 'front-end-only-users'); ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </tfoot>

    <tbody id="the-list" class='list:tag'>
    <?php
        if ($myrows) {
            foreach ($myrows as $User_ID) {
                $FieldCount = 0;
                $User = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ewd_feup_user_table_name WHERE User_ID=%d", $User_ID->User_ID ) );
                echo "<tr id='User" . esc_attr($User->User_ID) ."'>";
                echo "<th scope='row' class='check-column'>";
                echo "<input type='checkbox' name='Users_Bulk[]' value='" . esc_attr($User->User_ID) ."' />";
                echo "</th>";
                $Username = $wpdb->get_var( $wpdb->prepare( "SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID=%d", $User->User_ID ) );
                echo "<td class='username column-name'>" . esc_html($Username) . "</td>";
                if ($Admin_Approval == "Yes") {
                    echo "<td class='name column-name'>" . esc_html($User->User_Admin_Approved) . "</td>";
                }
                foreach ($Fields as $Field) {
                    $User_Field_Value = $wpdb->get_var($wpdb->prepare("SELECT Field_Value FROM $ewd_feup_user_fields_table_name WHERE User_ID=%d and Field_Name=%s", $User->User_ID, $Field->Field_Name));
                    echo "<td class='name column-name'>";
                    if ($FieldCount == 0) {
                        echo "<strong>";
                        echo "<a class='row-title' href='" . esc_url(admin_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User&User_ID=" . esc_attr($User->User_ID))) . "' title='" . esc_attr__('Edit User', 'front-end-only-users') . "'>";
                    }
                    echo esc_html(substr(strval($User_Field_Value), 0, 60));
                    if (strlen(strval($User_Field_Value)) > 60) { echo "..."; }
                    if ($FieldCount == 0) {
                        echo "</a></strong>";
                        echo "<br />";
                        echo "<div class='row-actions'>";
                        echo "<span class='delete'>";
                        echo "<a class='delete-tag feup-confirm-one-user' href='" . esc_url(admin_url("admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteUser&DisplayPage=Users&User_ID=" . esc_attr($User->User_ID))) . "'>";
                        esc_html_e("Delete", 'front-end-only-users');
                        echo "</a>";
                        echo "</span>";
                        echo "</div>";
                        echo "<div class='hidden' id='inline_" . esc_attr($User->User_ID) ."'>";
                    }
                    echo "</td>";
                    $FieldCount++;
                }
                if ($Include_WP_Users == "Yes") {
                    echo "<td class='name column-wpuser'>";
                    if ($User->User_WP_ID != 0) {
                        esc_html_e("Yes", "front-end-only-users");
                    } else {
                        esc_html_e("No", "front-end-only-users");
                    }
                    echo "</td>";
                }
                echo "<td class='name column-name'>" . esc_html($User->User_Last_Login) . "</td>";
                echo "<td class='name column-name'>" . esc_html($User->User_Date_Created) . "</td>";
                echo "</tr>";
            }
        }
    ?>
    </tbody>
</table>

<div class="tablenav bottom">
    <div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
        <span class="displaying-num"><?php echo esc_html($UserCount); ?> <?php _e("users", 'front-end-only-users') ?></span>
        <span class='pagination-links'>
            <a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the first page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=1'>&laquo;</a>
            <a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the previous page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page-1);?>'>&lsaquo;</a>
            <span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
            <a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the next page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page+1);?>'>&rsaquo;</a>
            <a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='<?php esc_attr_e('Go to the last page', 'front-end-only-users'); ?>' href='<?php echo esc_url($Current_Page_With_Order_By) . "&Page=" . esc_attr( $Number_of_Pages ); ?>'>&raquo;</a>
        </span>
    </div>
    <br class="clear" />
</div>
</form>


<br class="clear" />
</div>
</div> <!-- /col-right -->

<!-- Form to upload a list of new products from a spreadsheet -->
<div id="col-left" class="feup-hidden">
<div class="col-wrap">

<div class="form-wrap">

<div id="ewd-feup-admin-add-manually">

<h2><?php _e("Add New User", 'front-end-only-users') ?></h2>
<?php
$Fields = $AllFields;
$Levels = $wpdb->get_results("SELECT * FROM $ewd_feup_levels_table_name ORDER BY Level_Privilege ASC");
?>
<!-- Form to create a new user -->
<form id="addtag" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_AddUser&DisplayPage=Users' ) ); ?>" class="validate" enctype="multipart/form-data">
    <input type="hidden" name="action" value="Add_User" />
    <input type='hidden' name='ewd-registration-type' value='FEUP'>
    <?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' );  ?>
    <?php wp_referer_field(); ?>
    <?php if($Username_Is_Email == "Yes") { ?>
        <label for='Username' id='ewd-feup-register-username-div' class='ewd-feup-field-label'><?php _e('Email', 'front-end-only-users');?>: </label>
        <input type='email' class='ewd-feup-text-input' name='Username'>
    <?php } else {?>
        <label for='Username' id='ewd-feup-register-username-div' class='ewd-feup-field-label'><?php _e('Username', 'front-end-only-users');?>: </label>
        <input type='text' class='ewd-feup-text-input' name='Username'>
    <?php } ?>
    <label for='Password' id='ewd-feup-register-password-div' class='ewd-feup-field-label'><?php _e('Password', 'front-end-only-users')?>: </label>
    <input type='password' class='ewd-feup-text-input ewd-feup-password-input' name='User_Password'>
    <label for='Repeat Password' id='ewd-feup-register-password-confirm-div' class='ewd-feup-field-label'><?php _e('Repeat Password', 'front-end-only-users');?>: </label>
    <input type='password' class='ewd-feup-text-input ewd-feup-check-password-input' name='Confirm_User_Password'>
    <label for='Password Strength' id='ewd-feup-password-strength' class='ewd-feup-field-label'><?php _e('Password Strength', 'front-end-only-users') ?>: </label>
    <span id='ewd-feup-password-result'><?php esc_html_e('Too Short', 'front-end-only-users'); ?></span>
    <label for='Level ID' id='ewd-feup-register-user-level-div' class='ewd-feup-field-label'><?php _e('User Level', 'front-end-only-users');?>: </label>
    <select name='Level_ID'>
        <option value='0'><?php esc_html_e('None (0)', 'front-end-only-users'); ?></option>
        <?php foreach ($Levels as $Level) {
            echo "<option value='" . esc_attr( $Level->Level_ID ) . "'>" . esc_html( $Level->Level_Name ) . " (" . esc_html( $Level->Level_Privilege ) . ")</option>";
        }?>
    </select>
    <?php if ($Admin_Approval == "Yes") { ?>
        <label for='Admin Approved' id='ewd-feup-register-admin-approved-div' class='ewd-feup-field-label'><?php _e('Admin Approved', 'front-end-only-users');?>: </label>
        <input type='radio' class='ewd-feup-text-input' name='Admin_Approved' value='Yes'><?php esc_html_e('Yes', 'front-end-only-users'); ?><br />
        <input type='radio' class='ewd-feup-text-input' name='Admin_Approved' value='No'><?php esc_html_e('No', 'front-end-only-users'); ?><br />
    <?php } ?>
    <?php foreach ($Fields as $Field) { ?>
        <div class="form-field form-required">
            <label for="<?php echo esc_attr( $Field->Field_Name ); ?>"><?php echo esc_html( $Field->Field_Name ); ?></label>
            <?php if ($Field->Field_Type == "text") {?>
                <input name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>" type="text" value="" size="60" />
            <?php } elseif ($Field->Field_Type == "mediumint") {?>
                <input name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>" type="number" value="" size="60" />
            <?php } elseif ($Field->Field_Type == "email") {?>
                <input name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>" type="email" value="" size="60" />
            <?php } elseif ($Field->Field_Type == "tel") {?>
                <input name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>" type="tel" value="" size="60" />
            <?php } elseif ($Field->Field_Type == "url") {?>
                <input name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>" type="url" value="" size="60" />
            <?php } elseif ($Field->Field_Type == "date") {?>
                <input name='<?php echo esc_attr( $Field->Field_Name ); ?>' id='ewd-feup-register-input-<?php echo esc_attr( $Field->Field_ID ); ?>' class='ewd-feup-date-input pure-input-1-3' type='date' value='' />
            <?php } elseif ($Field->Field_Type == "datetime") { ?>
                <input name='<?php echo esc_attr( $Field->Field_Name ); ?>' id='ewd-feup-register-input-<?php echo esc_attr( $Field->Field_ID ); ?>' class='ewd-feup-datetime-input pure-input-1-3' type='datetime-local' value='' />
            <?php } elseif ($Field->Field_Type == "textarea") { ?>
                <textarea name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>"></textarea>
            <?php } elseif ($Field->Field_Type == "file") {?>
                <input name='<?php echo esc_attr( $Field->Field_Name ); ?>' id='ewd-feup-register-input-<?php echo esc_attr( $Field->Field_ID ); ?>' class='ewd-feup-file-input pure-input-1-3' type='file' value='' />
            <?php } elseif ($Field->Field_Type == "picture") {?>
                <input name='<?php echo esc_attr( $Field->Field_Name ); ?>' id='ewd-feup-register-input-<?php echo esc_attr( $Field->Field_ID ); ?>' class='ewd-feup-file-input pure-input-1-3' type='file' value='' />
            <?php } elseif ($Field->Field_Type == "select" || $Field->Field_Type == "countries") { ?>
                <?php $Options = explode(",", $Field->Field_Options); ?>
                <?php if ($Field->Field_Type == "countries") {$Options = EWD_FEUP_Return_Country_Array();} ?>
                <select name="<?php echo esc_attr( $Field->Field_Name ); ?>" id="<?php echo esc_attr( $Field->Field_Name ); ?>">
                    <?php foreach ($Options as $Option) { ?>
                        <option value='<?php echo esc_attr( $Option ); ?>'><?php echo esc_html( $Option ); ?></option>
                    <?php } ?>
                </select>
            <?php } elseif ($Field->Field_Type == "radio") { ?>
                <?php $Options = explode(",", $Field->Field_Options); ?>
                <?php foreach ($Options as $Option) { ?>
                    <input type='radio' class='ewd-admin-small-input' name="<?php echo esc_attr( $Field->Field_Name ); ?>" value="<?php echo esc_attr( $Option ); ?>"><?php echo esc_html( $Option ); ?><br/>
                <?php } ?>
            <?php } elseif ($Field->Field_Type == "checkbox") { ?>
                <?php $Options = explode(",", $Field->Field_Options); ?>
                <?php foreach ($Options as $Option) { ?>
                    <input type="checkbox" class='ewd-admin-small-input' name="<?php echo esc_attr( $Field->Field_Name ); ?>[]" value="<?php echo esc_attr( $Option ); ?>"><?php echo esc_html( $Option ); ?><br/>
                <?php } ?>
            <?php } ?>
            <p><?php echo esc_html( $Field->Field_Description ); ?></p>
        </div>
    <?php } ?>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Add New User', 'front-end-only-users') ?>"  /></p>
</form>

</div> <!-- ewd-feup-admin-add-manually -->


<div id="ewd-feup-admin-add-from-spreadsheet">
    <h3><?php _e("Add Users from Spreadsheet", 'front-end-only-users') ?></h3>
    <?php if ($EWD_FEUP_Full_Version == "Yes") { ?>
    <div class="wrap">

        <form id="addtag" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_AddUserSpreadsheet&DisplayPage=Users' ) ); ?>" class="validate" enctype="multipart/form-data">
            <?php wp_nonce_field(); ?>
            <div class="form-field form-required">
                <label for="Users_Spreadsheet"><?php _e("Spreadsheet Containing Users", 'front-end-only-users') ?></label>
                <input name="Users_Spreadsheet" id="Users_Spreadsheet" type="file" value=""/>
                <p><?php _e("The spreadsheet containing all of the users you wish to add. Make sure that the column title names are the same as the field names for users (ex: Username, Email, First Name, etc.).", 'front-end-only-users') ?></p>
            </div>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Add New Users', 'front-end-only-users') ?>"  /></p>
        </form>
    </div>

    <?php } else { ?>
    <div class="Info-Div">
        <h2><?php _e("Full Version Required!", 'front-end-only-users') ?></h2>
        <div class="ewd-feup-full-version-explanation">
            <?php _e("The full version of Front-End Only Users is required to use tags.", 'front-end-only-users');?>
            <a href="https://www.etoilewebdesign.com/front-end-users-plugin/" target="_blank" rel="noopener noreferrer"><?php _e(" Please upgrade to unlock this page!", 'front-end-only-users'); ?></a>
        </div>
    </div>
    <?php } ?>
</div>

</div>

<br class="clear" />

</div>
</div> <!-- col-left -->


<?php

function ewd_feup_field_name_exists( $field_name) {
	global $wpdb;
	global $ewd_feup_fields_table_name;

	$fields = $wpdb->get_results("SELECT Field_Name FROM $ewd_feup_fields_table_name");

	foreach ( $fields as $field ) {
		if ( $field_name == $field->Field_Name ) { return true; }
	}

	return false;
}

?>