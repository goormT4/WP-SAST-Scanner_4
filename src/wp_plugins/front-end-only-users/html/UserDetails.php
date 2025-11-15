<?php 
    $Admin_Approval     = get_option("EWD_FEUP_Admin_Approval");
    $Email_Confirmation = get_option("EWD_FEUP_Email_Confirmation");
    $Payment_Frequency  = get_option("EWD_FEUP_Payment_Frequency");
?>
<!-- The details of a specific user for editing, based on the user ID -->
<?php
    $User_ID = isset($_GET['User_ID']) ? intval($_GET['User_ID']) : 0;
    $UserDetails = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $ewd_feup_user_fields_table_name WHERE User_ID = %d", $User_ID ) );
    $UserAdmin   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ewd_feup_user_table_name WHERE User_ID = %d", $User_ID ) );
    $Levels      = $wpdb->get_results( "SELECT * FROM $ewd_feup_levels_table_name ORDER BY Level_Privilege ASC" ); 

    $Page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;

    if ( isset( $_GET['OrderBy'] ) && in_array( $_GET['OrderBy'], array( 'Event_Type', 'Event_Location_Title', 'Event_Target_Title', 'Event_Date' ) ) ) {
        $OrderBy = sanitize_text_field($_GET['OrderBy']);
    } else {
        $OrderBy = 'Event_Date';
    }

    $Order = ( isset( $_GET['Order'] ) && $_GET['Order'] == 'ASC' ) ? 'ASC' : 'DESC';

    if ( is_object( $UserAdmin ) ) {
        $User_ID = $UserAdmin->User_ID;
    } else {
        $User_ID = 0;
    }

    $Current_Page = "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User&User_ID=" . esc_attr($User_ID);

    $Sql = "SELECT * FROM $ewd_feup_user_events_table_name WHERE User_ID=%d ";
    if (isset($_GET['OrderBy'])) {
        $Sql .= "ORDER BY " . esc_sql($OrderBy) . " " . esc_sql($Order) . " ";
    } else {
        $Sql .= "ORDER BY Event_Date DESC ";
    }
    $Sql .= "LIMIT " . (($Page - 1) * 100) . ",100";
    $myrows = $wpdb->get_results( $wpdb->prepare( $Sql, $User_ID ) );
    $Number_of_Pages = ceil($wpdb->num_rows/100);
    if (isset($_GET['OrderBy'])) {
        $Current_Page_With_Order_By = $Current_Page . "&OrderBy=" . urlencode($OrderBy) . "&Order=" . urlencode($Order);
    } else {
        $Current_Page_With_Order_By = $Current_Page;
    }
    $EventCount = $wpdb->num_rows;
    $Fields = $wpdb->get_results("SELECT * FROM $ewd_feup_fields_table_name");
?>

<div class="OptionTab ActiveTab" id="EditProduct">
    <div id="ewd-feup-new-edit-user-screen">
        <div id="col-left">
            <div class="col-wrap ewd-feup-user-details-wrap">
                <div class="form-wrap UserDetail">
                    <a href="admin.php?page=EWD-FEUP-options&DisplayPage=Users" class="NoUnderline">&#171; <?php _e("Back to Users list", 'front-end-only-users') ?></a>
                    <div style="clear: both;"></div>
                    <h2>
                        <?php _e("Edit User", 'front-end-only-users'); ?>:
                        <?php echo isset($UserAdmin->Username) ? esc_html($UserAdmin->Username) : ''; ?>
                    </h2>
                    <p>
                        <?php
                            esc_html_e("Member since ", 'front-end-only-users');
                            echo isset($UserAdmin->User_Date_Created) ? esc_html($UserAdmin->User_Date_Created) : '';
                        ?>
                    </p>
                    <!-- Form to update a user -->
                    <form id="addtag" method="post" action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_EditUser&DisplayPage=Users" class="validate" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="Edit_User" />
                        <input type="hidden" name="User_ID" value="<?php echo esc_attr($User_ID); ?>" />
                        <?php wp_nonce_field( 'EWD_FEUP_Admin_Nonce', 'EWD_FEUP_Admin_Nonce' ); ?>
                        <?php wp_referer_field(); ?>

                        <div class="ewd-feup-admin-edit-product-left">
                            <div class="ewd-feup-dashboard-new-widget-box ewd-widget-box-full ewd-feup-admin-closeable-widget-box ewd-feup-admin-edit-product-left-full-widget-box" id="ewd-feup-admin-edit-user-details-widget-box">
                                <div class="ewd-feup-dashboard-new-widget-box-top"><?php _e('User Details', 'front-end-only-users'); ?><span class="ewd-feup-admin-edit-product-down-caret">&nbsp;&nbsp;&#9660;</span><span class="ewd-feup-admin-edit-product-up-caret">&nbsp;&nbsp;&#9650;</span></div>
                                <div class="ewd-feup-dashboard-new-widget-box-bottom">
                                    <table class="form-table">
                                        <tr>
                                            <th><label for="Level_ID"><?php _e("Level", 'front-end-only-users') ?></label></th>
                                            <td>
                                                <select name='Level_ID' id="Level_ID">
                                                    <option value='0'>None (0)</option>
                                                    <?php foreach ($Levels as $Level) { ?>
                                                        <option value="<?php echo esc_attr($Level->Level_ID); ?>" <?php selected($UserAdmin->Level_ID, $Level->Level_ID); ?>>
                                                            <?php echo esc_html($Level->Level_Name . " (" . $Level->Level_Privilege . ")"); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php foreach ($Fields as $Field) {
                                            $Value = "";
                                            foreach ($UserDetails as $UserField) {
                                                if ($Field->Field_Name == $UserField->Field_Name) { $Value = $UserField->Field_Value; }
                                            }
                                        ?>
                                        <tr>
                                            <th>
                                                <label for="<?php echo esc_attr($Field->Field_Name); ?>">
                                                    <?php echo esc_html($Field->Field_Name); ?>
                                                </label>
                                            </th>
                                            <td>
                                                <?php if ($Field->Field_Type == "text" || $Field->Field_Type == "mediumint") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-regular-text" id="<?php echo esc_attr($Field->Field_Name); ?>" type="text" value="<?php echo esc_attr($Value); ?>" size="60" />
                                                <?php } elseif ($Field->Field_Type == "email") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-regular-text" id="<?php echo esc_attr($Field->Field_Name); ?>" type="email" value="<?php echo esc_attr($Value); ?>" size="60" />
                                                <?php } elseif ($Field->Field_Type == "tel") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-regular-text" id="<?php echo esc_attr($Field->Field_Name); ?>" type="tel" value="<?php echo esc_attr($Value); ?>" size="60" />
                                                <?php } elseif ($Field->Field_Type == "url") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-regular-text" id="<?php echo esc_attr($Field->Field_Name); ?>" type="url" value="<?php echo esc_attr($Value); ?>" size="60" />
                                                <?php } elseif ($Field->Field_Type == "date") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" id="ewd-feup-register-input-<?php echo esc_attr($Field->Field_ID); ?>" class="ewd-feup-date-input pure-input-1-3" type="date" value="<?php echo esc_attr($Value); ?>" />
                                                <?php } elseif ($Field->Field_Type == "datetime") { ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" id="ewd-feup-register-input-<?php echo esc_attr($Field->Field_ID); ?>" class="ewd-feup-datetime-input pure-input-1-3" type="datetime-local" value="<?php echo esc_attr($Value); ?>" />
                                                <?php } elseif ($Field->Field_Type == "textarea") { ?>
                                                    <textarea name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-large-text" id="<?php echo esc_attr($Field->Field_Name); ?>"><?php echo esc_textarea($Value); ?></textarea>
                                                <?php } elseif ($Field->Field_Type == "file") { ?>
                                                    <?php echo esc_html__("Current file:", 'front-end-only-users') . " " . esc_html(substr($Value, 10)); ?>
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" id="ewd-feup-register-input-<?php echo esc_attr($Field->Field_ID); ?>" class="ewd-feup-date-input pure-input-1-3" type="file" value="" />
                                                <?php } elseif ($Field->Field_Type == "picture") { ?>
                                                    <?php _e("Current Picture: ", 'front-end-only-users'); ?>
                                                    <br />
                                                    <img src="<?php echo esc_url( content_url("/uploads/ewd-feup-user-uploads/") . $Value ); ?>" alt="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-feup-profile-picture" /><br />
                                                    <input name="<?php echo esc_attr($Field->Field_Name); ?>" id="ewd-feup-register-input-<?php echo esc_attr($Field->Field_ID); ?>" class="ewd-feup-file-input" type="file" value=""/>
                                                <?php } elseif ($Field->Field_Type == "select" || $Field->Field_Type == "countries") { 
                                                    $Options = ($Field->Field_Type == "countries") ? EWD_FEUP_Return_Country_Array() : explode(",", $Field->Field_Options); ?>
                                                    <select name="<?php echo esc_attr($Field->Field_Name); ?>" id="<?php echo esc_attr($Field->Field_Name); ?>">
                                                        <?php foreach ($Options as $Option) { ?>
                                                            <option value="<?php echo esc_attr($Option); ?>" <?php selected($Value, $Option); ?>><?php echo esc_html($Option); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } elseif ($Field->Field_Type == "radio") {
                                                    $Options = explode(",", $Field->Field_Options);
                                                    foreach ($Options as $Option) { ?>
                                                        <input type="radio" name="<?php echo esc_attr($Field->Field_Name); ?>" class="ewd-admin-small-input" value="<?php echo esc_attr($Option); ?>" <?php checked($Value, $Option); ?>><?php echo esc_html($Option); ?><br/>
                                                    <?php }
                                                } elseif ($Field->Field_Type == "checkbox") {
                                                    $Options = explode(",", $Field->Field_Options);
                                                    $User_Checkbox = explode(",", $Value);
                                                    foreach ($Options as $Option) { ?>
                                                        <input type="checkbox" class="ewd-admin-small-input" name="<?php echo esc_attr($Field->Field_Name); ?>[]" value="<?php echo esc_attr($Option); ?>" <?php if (in_array($Option, $User_Checkbox)) echo "checked"; ?>><?php echo esc_html($Option); ?><br/>
                                                    <?php }
                                                } ?>
                                                <p><?php echo esc_html($Field->Field_Description); ?></p>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>      
                            <div class="ewd-feup-dashboard-new-widget-box ewd-widget-box-full ewd-feup-admin-closeable-widget-box ewd-feup-admin-edit-product-left-full-widget-box ewd-feup-admin-widget-box-start-closed" id="ewd-feup-admin-edit-customer-details-widget-box">
                                <div class="ewd-feup-dashboard-new-widget-box-top"><?php _e('Recent User Activity', 'front-end-only-users'); ?><span class="ewd-feup-admin-edit-product-down-caret">&nbsp;&nbsp;&#9660;</span><span class="ewd-feup-admin-edit-product-up-caret">&nbsp;&nbsp;&#9650;</span></div>
                                <div class="ewd-feup-dashboard-new-widget-box-bottom">
                                    <div class="tablenav top">
                                        <div class='tablenav-pages <?php if ($Number_of_Pages == 1) echo "one-page"; ?>'>
                                            <span class="displaying-num"><?php echo esc_html($EventCount); ?> <?php _e("events", 'front-end-only-users') ?> <?php if ($EWD_FEUP_Full_Version != "Yes") echo "out of 100";?></span>
                                            <span class='pagination-links'>
                                                <a class='first-page <?php if ($Page == 1) echo "disabled"; ?>' title='Go to the first page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=1'>&laquo;</a>
                                                <a class='prev-page <?php if ($Page <= 1) echo "disabled"; ?>' title='Go to the previous page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page-1);?>'>&lsaquo;</a>
                                                <span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
                                                <a class='next-page <?php if ($Page >= $Number_of_Pages) echo "disabled"; ?>' title='Go to the next page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page+1);?>'>&rsaquo;</a>
                                                <a class='last-page <?php if ($Page == $Number_of_Pages) echo "disabled"; ?>' title='Go to the last page' href='<?php echo esc_url($Current_Page_With_Order_By) . "&Page=" . esc_attr($Number_of_Pages); ?>'>&raquo;</a>
                                            </span>
                                        </div>
                                    </div>
                                    <table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Type" && $_GET['Order'] == "ASC") { $Order = "DESC"; } else { $Order = "ASC"; } ?>
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Type&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Type", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Location_Title" && $_GET['Order'] == "ASC") { $Order = "DESC"; } else { $Order = "ASC"; } ?>
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Location_Title&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Location", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Target_Title" && $_GET['Order'] == "ASC") { $Order = "DESC"; } else { $Order = "ASC"; } ?>
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Target_Title&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Target Title", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Event_Date" && $_GET['Order'] == "ASC") { $Order = "DESC"; } else { $Order = "ASC"; } ?>
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Date&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Date", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Type&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Type", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Location_Title&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Location", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Target_Title&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Target Title", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                                <th scope='col' class='manage-column column-cb check-column'  style="">
                                                    <a href="<?php echo esc_url($Current_Page); ?>&OrderBy=Event_Date&Order=<?php echo esc_attr($Order); ?>">
                                                        <span><?php _e("Event Date", 'front-end-only-users'); ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                            </tr>
                                        </tfoot>
                                        <tbody id="the-list" class='list:tag'>
                                        <?php if ($EWD_FEUP_Full_Version == "Yes") {
                                            if ($myrows) {
                                                foreach ($myrows as $Event) { ?>
                                                    <tr id="Event-<?php echo esc_attr($Event->User_Event_ID); ?>">
                                                        <td class='name column-type'><?php echo esc_html($Event->Event_Type); ?></td>
                                                        <td class='name column-location'><?php echo esc_html($Event->Event_Location_Title != "" ? $Event->Event_Location_Title : $Event->Event_Location); ?></td>
                                                        <td class='name column-target'><?php echo esc_html($Event->Event_Target_Title != "" ? $Event->Event_Target_Title : $Event->Event_Value); ?></td>
                                                        <td class='name column-date'><?php echo esc_html($Event->Event_Date); ?></td>
                                                    </tr>
                                                <?php }
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="4">
                                                    <?php esc_html_e("The full version of Front-End Only Users is required to view user activity.", 'front-end-only-users'); ?>
                                                    <a href="http://www.etoilewebdesign.com/front-end-users-plugin/">
                                                        <?php esc_html_e(" Please upgrade to view this information!", 'front-end-only-users'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="tablenav bottom">
                                        <div class='tablenav-pages <?php if ($Number_of_Pages == 1) echo "one-page"; ?>'>
                                            <span class="displaying-num"><?php echo esc_html($EventCount); ?> <?php _e("events", 'front-end-only-users') ?></span>
                                            <span class='pagination-links'>
                                                <a class='first-page <?php if ($Page == 1) echo "disabled"; ?>' title='Go to the first page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=1'>&laquo;</a>
                                                <a class='prev-page <?php if ($Page <= 1) echo "disabled"; ?>' title='Go to the previous page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page-1);?>'>&lsaquo;</a>
                                                <span class="paging-input"><?php echo esc_html($Page); ?> <?php _e("of", 'front-end-only-users') ?> <span class='total-pages'><?php echo esc_html($Number_of_Pages); ?></span></span>
                                                <a class='next-page <?php if ($Page >= $Number_of_Pages) echo "disabled"; ?>' title='Go to the next page' href='<?php echo esc_url($Current_Page_With_Order_By); ?>&Page=<?php echo esc_attr($Page+1);?>'>&rsaquo;</a>
                                                <a class='last-page <?php if ($Page == $Number_of_Pages) echo "disabled"; ?>' title='Go to the last page' href='<?php echo esc_url($Current_Page_With_Order_By) . "&Page=" . esc_attr($Number_of_Pages); ?>'>&raquo;</a>
                                            </span>
                                        </div>
                                        <br class="clear" />
                                    </div>
                                </div> <!--widget-bottom-->
                            </div>
                        </div> <!--ewd-feup-admin-edit-product-left-->


						<div class="ewd-feup-admin-edit-product-right">

						    <p class="submit ewd-feup-admin-edit-product-submit-p">
						        <input type="submit" name="submit" id="submit" class="button-primary ewd-feup-admin-edit-product-save-button" value="<?php esc_attr_e('Edit User ', 'front-end-only-users') ?>" />
						    </p>
						    </form>
						
						    <div class="ewd-feup-dashboard-new-widget-box ewd-widget-box-full ewd-feup-admin-closeable-widget-box" id="ewd-feup-admin-edit-user-need-help-widget-box">
						        <div class="ewd-feup-dashboard-new-widget-box-top">
						            <?php _e('Need Help?', 'front-end-only-users'); ?>
						            <span class="ewd-feup-admin-edit-product-down-caret">&nbsp;&nbsp;&#9660;</span>
						            <span class="ewd-feup-admin-edit-product-up-caret">&nbsp;&nbsp;&#9650;</span>
						        </div>
						        <div class="ewd-feup-dashboard-new-widget-box-bottom">
						            <div class='ewd-feup-need-help-box'>
						                <div class='ewd-feup-need-help-text'><?php esc_html_e('Visit our Support Center for documentation and tutorials', 'front-end-only-users'); ?></div>
						                <a class='ewd-feup-need-help-button' href='https://www.etoilewebdesign.com/support-center/?Plugin=FEUP' target='_blank' rel="noopener noreferrer"><?php esc_html_e('GET SUPPORT', 'front-end-only-users'); ?></a>
						            </div>
						        </div>
						    </div>
						
						    <div class="ewd-feup-dashboard-new-widget-box ewd-widget-box-full ewd-feup-admin-closeable-widget-box" id="ewd-feup-admin-edit-user-custom-fields-widget-box">
						        <div class="ewd-feup-dashboard-new-widget-box-top">
						            <?php _e('User Approval and Payment', 'front-end-only-users'); ?>
						            <span class="ewd-feup-admin-edit-product-down-caret">&nbsp;&nbsp;&#9660;</span>
						            <span class="ewd-feup-admin-edit-product-up-caret">&nbsp;&nbsp;&#9650;</span>
						        </div>
						        <div class="ewd-feup-dashboard-new-widget-box-bottom">
						            <table class="form-table">
						                <?php if ($Admin_Approval == "Yes") { ?>
						                    <tr>
						                        <th>
						                            <label for="Admin_Approved" id="ewd-feup-register-admin-approved-div" class="ewd-feup-field-label"><?php _e('Admin Approved', 'front-end-only-users');?>: </label>
						                        </th>
						                        <td>
						                            <input type="radio" class="ewd-feup-text-input" name="Admin_Approved" value="Yes" <?php checked( $UserAdmin->User_Admin_Approved ?? '', 'Yes' ); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br />
						                            <input type="radio" class="ewd-feup-text-input" name="Admin_Approved" value="No" <?php checked( $UserAdmin->User_Admin_Approved ?? '', 'No' ); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br />
						                        </td>
						                    </tr>
						                <?php } ?>
						                <?php if ($Email_Confirmation == "Yes") { ?>
						                    <tr>
						                        <th>
						                            <label for="Email_Confirmation" id="ewd-feup-register-admin-approved-div" class="ewd-feup-field-label"><?php _e('Email Confirmed', 'front-end-only-users');?>: </label>
						                        </th>
						                        <td>
						                            <input type="radio" class="ewd-feup-text-input" name="Email_Confirmation" value="Yes" <?php checked( $UserAdmin->User_Email_Confirmed ?? '', 'Yes' ); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br />
						                            <input type="radio" class="ewd-feup-text-input" name="Email_Confirmation" value="No" <?php checked( $UserAdmin->User_Email_Confirmed ?? '', 'No' ); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br />
						                        </td>
						                    </tr>
						                <?php } ?>
						                <?php if ($Payment_Frequency != "None") { ?>
						                    <tr>
						                        <th>
						                            <label for="User_Membership_Fees_Paid" id="ewd-feup-register-admin-approved-div" class="ewd-feup-field-label"><?php _e('Membership Fees Paid', 'front-end-only-users');?>: </label>
						                        </th>
						                        <td>
						                            <input type="radio" class="ewd-feup-text-input" name="User_Membership_Fees_Paid" value="Yes" <?php checked( $UserAdmin->User_Membership_Fees_Paid ?? '', 'Yes' ); ?>><?php esc_html_e('Yes', 'front-end-only-users'); ?><br />
						                            <input type="radio" class="ewd-feup-text-input" name="User_Membership_Fees_Paid" value="No" <?php checked( $UserAdmin->User_Membership_Fees_Paid ?? '', 'No' ); ?>><?php esc_html_e('No', 'front-end-only-users'); ?><br />
						                        </td>
						                    </tr>
						                <?php } ?>
						                <?php if ($Payment_Frequency == "Yearly" || $Payment_Frequency == "Monthly") { ?>
						                    <tr>
						                        <th>
						                            <label for="User_Account_Expiry" id="ewd-feup-register-admin-approved-div" class="ewd-feup-field-label"><?php _e('Account Expiry Date', 'front-end-only-users');?>: </label>
						                        </th>
						                        <td>
						                            <input type="datetime-local" class="ewd-feup-text-input" name="User_Account_Expiry" value="<?php echo esc_attr( str_replace(" ", "T", $UserAdmin->User_Account_Expiry ?? '') ); ?>" >
						                        </td>
						                    </tr>
						                <?php } ?>
						            </table>
						        </div>
						    </div>
						
						    <div class="ewd-feup-dashboard-new-widget-box ewd-widget-box-full ewd-feup-admin-closeable-widget-box ewd-feup-admin-widget-box-start-closed" id="ewd-feup-admin-edit-user-custom-fields-widget-box">
						        <div class="ewd-feup-dashboard-new-widget-box-top">
						            <?php _e('Delete User', 'front-end-only-users'); ?>
						            <span class="ewd-feup-admin-edit-product-down-caret">&nbsp;&nbsp;&#9660;</span>
						            <span class="ewd-feup-admin-edit-product-up-caret">&nbsp;&nbsp;&#9650;</span>
						        </div>
						        <div class="ewd-feup-dashboard-new-widget-box-bottom">
						            <a class="delete-tag feup-confirm-one-user button-secondary ewd-feup-admin-delete-all-products-button ewd-feup-admin-edit-product-delete-user-button"
						               href="<?php echo esc_url(
						                    add_query_arg(
						                        '_wp_nonce',
						                        wp_create_nonce('ewd-feup-admin'),
						                        'admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteUser&DisplayPage=Users&User_ID=' . urlencode($UserAdmin->User_ID ?? 0)
						                    )
						                ); ?>">
						                <?php _e("Delete User", 'front-end-only-users') ?>
						            </a>
						        </div>
						    </div>
						
						</div> <!--ewd-feup-admin-edit-product-right-->



					</form>

				</div> <!--UserDetail-->
			</div> <!--col-wrap-->
		</div> <!--col-left-->

	</div> <!--ewd-feup-new-edit-user-screen-->


</div><!--ActiveTab-->



