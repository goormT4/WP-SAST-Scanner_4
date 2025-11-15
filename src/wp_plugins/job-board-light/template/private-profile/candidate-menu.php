<ul>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_menu_listinghome' ) ) {
			$account_menu_check= get_option('epjbjobboard_menu_listinghome');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="">
			<a href="<?php echo get_post_type_archive_link( 'job' ) ; ?>">
				<i class="fas fa-home"></i>
			<?php  esc_html_e('Job Search','jobboard');	 ?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_mylevel' ) ) {
			$account_menu_check= get_option('epjbjobboard_mylevel');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='level'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=level">
				<i class="fas fa-user-clock"></i>
			<?php  esc_html_e('Membership','jobboard');	 ?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_messageboard' ) ) {
			$account_menu_check= get_option('epjbjobboard_messageboard');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='messageboard'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=messageboard">
				<i class="far fa-envelope"></i>
			<?php  esc_html_e('Message','jobboard');?></a>
		</li>	
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_notification' ) ) {
			$account_menu_check= get_option('epjbjobboard_notification');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='notification'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=notification">
				<i class="far fa-bell"></i>
			<?php  esc_html_e('Job Notifications','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_edit_resume' ) ) {
			$account_menu_check= get_option('epjbjobboard_edit_resume');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='edit_resume'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=edit_resume">
				<i class="fas fa-edit"></i>
			<?php   esc_html_e('Edit Resume','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_menusetting' ) ) {
			$account_menu_check= get_option('epjbjobboard_menusetting');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='setting'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=setting">
				<i class="fas fa-user-cog"></i>
			<?php  esc_html_e('Edit Profile','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option( 'jobboard_candidate_applied' ) ) {
			$account_menu_check= get_option('jobboard_candidate_applied');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='candidate-applied'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=candidate-applied">
				<i class="fas fa-check-double"></i>
			<?php   esc_html_e('My Applied','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_candidate_bookmarks' ) ) {
			$account_menu_check= get_option('epjbjobboard_candidate_bookmarks');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='candidate-bookmarks'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=candidate-bookmarks">
				<i class="fas fa-user-check"></i>
			<?php   esc_html_e('Saved Candidate','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_employer_bookmarks' ) ) {
			$account_menu_check= get_option('epjbjobboard_employer_bookmarks');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='employer_bookmarks'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=employer_bookmarks">
				<i class="fas fa-chalkboard-teacher"></i>
			<?php   esc_html_e('Saved Employer','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<?php
		$account_menu_check= '';
		if( get_option('epjbjobboard_job_bookmarks' ) ) {
			$account_menu_check= get_option('epjbjobboard_job_bookmarks');
		}
		if($account_menu_check!='yes'){
		?>
		<li class="<?php echo ($active=='job_bookmark'? 'active':''); ?> ">
			<a href="<?php echo get_permalink(); ?>?&profile=job_bookmark">
				<i class="fas fa-chalkboard-teacher"></i>
			<?php   esc_html_e('Job Bookmarks','jobboard');?> </a>
		</li>
		<?php
		}
	?>
	<li class="<?php echo ($active=='log-out'? 'active':''); ?> ">
		<a href="<?php echo wp_logout_url( home_url() ); ?>" >
			<i class="fas fa-sign-out-alt"></i>
			<?php  esc_html_e('Sign out','jobboard');?>
		</a>
	</li>
</ul>