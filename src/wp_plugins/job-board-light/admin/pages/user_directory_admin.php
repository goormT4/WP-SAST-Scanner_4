<?php
	wp_enqueue_style('wp-jobboard-style-11', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('dataTables', wp_jobboard_URLPATH . 'admin/files/css/jquery.dataTables.css');
	wp_enqueue_script('dataTables', wp_jobboard_URLPATH . 'admin/files/js/jquery.dataTables.js');
?> 

<div class="bootstrap-wrapper">
	<div class="dashboard-eplugin container-fluid">
		<div class="row">
			<div class="col-md-12">
				<h3 class="page-header" ><?php  esc_html_e('User Setting','jobboard')	;?>  <small>  </small> </h3>
			</div>
		</div>
		
		<div class="">
			<?php
				$no=20000;
				$paged = (isset($_REQUEST['paged'])) ? $_REQUEST['paged'] : 1;
				if($paged==1){
					$offset=0;
					}else {
					$offset= ($paged-1)*$no;
				}
				$args = array();
				$args['number']='99999999';		
				$args['orderby']='registered';
				$args['order']='DESC';
				$user_query = new WP_User_Query( $args );
			?>
			<table id="user-data" class="display table" width="100%">
				<thead>
					<tr>
						<th> <?php  esc_html_e('ID','jobboard')	;?> </th>
						<th> <?php  esc_html_e('Create Date','jobboard')	;?> </th>
						<th> <?php  esc_html_e('User Name','jobboard')	;?></th>
						<th> <?php  esc_html_e('Email','jobboard')	;?> </th>
						<th> <?php  esc_html_e('Expiry Date','jobboard')	;?> </th>
						<th> <?php  esc_html_e('Payment Status','jobboard')	;?> </th>
						<th> <?php  esc_html_e('Role','jobboard')	;?> </th>
						<th> <?php  esc_html_e('Type','jobboard')	;?> </th>
						<th><?php  esc_html_e('Action','jobboard')	;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						// User Loop
						if ( ! empty( $user_query->results ) ) {
							foreach ( $user_query->results as $user ) {
								
							?>
							<tr>
								<td><?php echo esc_html($user->ID); ?></td>
								<td><?php echo date("d-M-Y h:m:s A" ,strtotime($user->user_registered) ); ?></td>
								<td><?php echo esc_attr(get_user_meta($user->ID, 'first_name', true)).' '.esc_attr(get_user_meta($user->ID, 'last_name', true)).' ('. $user->display_name.')'; ?></td>
								<td><?php echo esc_html($user->user_email); ?></td>
								<td><?php
									$exp_date= get_user_meta($user->ID, 'jobboard_exprie_date', true);
									if($exp_date!=''){
										echo date('d-M-Y',strtotime($exp_date));
									}
								?></td>
								<td>
									<?php
										echo esc_attr(get_user_meta($user->ID, 'jobboard_payment_status', true));
									?>
								</td>
								<td><?php
									if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
										foreach ( $user->roles as $role )
										echo ucfirst($role);
									}
								?>
								</td>
								<td>
									<?php  echo ucwords(esc_attr(get_user_meta($user->ID, 'user_type', true)));?>
								</td>
								<td>		<a class="btn btn-primary btn-xs" href="?page=wp-jobboard-user_update&id=<?php echo esc_attr($user->ID); ?>"> <?php  esc_html_e('Edit','jobboard')	;?></a>
									<a class="btn btn-danger btn-xs" href="<?php echo admin_url().'/users.php'?>"><?php  esc_html_e('Delete','jobboard')	;?> </a>
								</td>
							</tr>
							<?php
							}
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>