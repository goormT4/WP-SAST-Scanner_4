<div class="EWD_FEUP_Menu">
	<h2 class="nav-tab-wrapper">
		<a id="ewd-feup-dash-mobile-menu-open" href="#" class="MenuTab nav-tab"><?php esc_html_e("MENU", 'front-end-only-users'); ?><span id="ewd-feup-dash-mobile-menu-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-feup-dash-mobile-menu-up-caret">&nbsp;&nbsp;&#9650;</span></a>
		<a id="Dashboard_Menu" class="MenuTab nav-tab <?php if ($Display_Page == '' || $Display_Page == 'Dashboard') {echo 'nav-tab-active';}?>" onclick="ShowTab('Dashboard');"><?php esc_html_e("Dashboard", 'front-end-only-users'); ?></a>
		<a id="Users_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Users') {echo 'nav-tab-active';}?>" onclick="ShowTab('Users');"><?php esc_html_e("Users", 'front-end-only-users'); ?></a>
		<a id="Statistics_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Statistics') {echo 'nav-tab-active';}?>" onclick="ShowTab('Statistics');"><?php esc_html_e("Statistics", 'front-end-only-users'); ?></a>
		<a id="Fields_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Field') {echo 'nav-tab-active';}?>" onclick="ShowTab('Fields');"><?php esc_html_e("Fields", 'front-end-only-users'); ?></a>
		<a id="Levels_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Levels') {echo 'nav-tab-active';}?>" onclick="ShowTab('Levels');"><?php esc_html_e("Levels", 'front-end-only-users'); ?></a>
		<a id="Options_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Options') {echo 'nav-tab-active';}?>" onclick="ShowTab('Options');"><?php esc_html_e("Options", 'front-end-only-users'); ?></a>
		<a id="Emails_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Emails') {echo 'nav-tab-active';}?>" onclick="ShowTab('Emails');"><?php esc_html_e("Emails", 'front-end-only-users'); ?></a>
		<a id="Payments_Menu" class="MenuTab nav-tab <?php if ($Display_Page == 'Payments') {echo 'nav-tab-active';}?>" onclick="ShowTab('Payments');"><?php esc_html_e("Payments", 'front-end-only-users'); ?></a>
	</h2>
</div>

<div class="clear"></div>

<!-- Add the individual pages to the admin area, and create the active tab based on the selected page -->
<div class="OptionTab <?php if ($Display_Page == "" || $Display_Page == 'Dashboard') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Dashboard">
	<?php include( plugin_dir_path( __FILE__ ) . 'DashboardPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Users' || $Display_Page == 'User') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Users">
	<?php include( plugin_dir_path( __FILE__ ) . 'UsersPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Statistics' || $Display_Page == 'Statistic') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Statistics">
	<?php include( plugin_dir_path( __FILE__ ) . 'StatisticsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Fields' || $Display_Page == 'Field') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Fields">
	<?php include( plugin_dir_path( __FILE__ ) . 'FieldsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Levels' || $Display_Page == 'Level') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Levels">
	<?php include( plugin_dir_path( __FILE__ ) . 'LevelsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Options' || $Display_Page == 'Option') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Options">
	<?php include( plugin_dir_path( __FILE__ ) . 'OptionsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Emails' || $Display_Page == 'Email') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Emails">
	<?php include( plugin_dir_path( __FILE__ ) . 'EmailsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'Payments' || $Display_Page == 'Payment') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Payments">
	<?php include( plugin_dir_path( __FILE__ ) . 'PaymentsPage.php'); ?>
</div>

<div class="OptionTab <?php if ($Display_Page == 'OneClickInstall') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="OneClickInstall">
	<?php include( plugin_dir_path( __FILE__ ) . 'OneClickInstall.php'); ?>
</div>
