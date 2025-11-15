<div class="bootstrap-wrapper">
 	<div class="dashboard-eplugin container-fluid">
 		<?php	
			global $wpdb, $post,$current_user;	
			//*************************	plugin file *********
			$jobboard_approve= get_post_meta( $post->ID,'jobboard_approve', true );
			$jobboard_current_author= $post->post_author;
			$userId=$current_user->ID;
			if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
			?>
			<div class="row">
				<div class="col-md-12">
					<?php esc_html_e( 'User ID :', 'jobboard' )?>
					<select class="form-control" id="jobboard_author_id" name="jobboard_author_id">
						<?php	
							$sql="SELECT * FROM $wpdb->users ";		
							$products_rows = $wpdb->get_results($sql); 	
							if(sizeof($products_rows)>0){									
								foreach ( $products_rows as $row ) 
								{	
									echo '<option value="'.$row->ID.'"'. ($jobboard_current_author == $row->ID ? "selected" : "").' >'. esc_html($row->ID).' | '.esc_html($row->user_email).' </option>';
								}
							}	
						?>
					</select>
				</div>  
				<div class="col-md-12"> <label>
					<input type="checkbox" name="jobboard_approve" id="jobboard_approve" value="yes" <?php echo ($jobboard_approve=="yes" ? 'checked': "" )  ; ?> />  <strong><?php esc_html_e( 'Approve', 'jobboard' )?></strong>
				</label>
				</div> 
			</div>	  
			<?php
			}
		?>
 		<br/>
		<div class="row">
 			<div class="col-md-12">
				<label>
					<?php
						$jobboard_featured= get_post_meta( $post->ID,'jobboard_featured', true );
					?>
					<label><input type="radio" name="jobboard_featured" id="jobboard_featured" value="featured" <?php echo ($jobboard_featured=="featured" ? 'checked': "" )  ; ?> />  <strong><?php esc_html_e( 'Featured (display on top)', 'jobboard' )?></strong></label>
					<br/>
					<label><input type="radio" name="jobboard_featured" id="jobboard_featured" value="Not-featured" <?php echo ($jobboard_featured=="Not-featured" ? 'checked': "" )  ; ?> />  <strong><?php esc_html_e( 'Not Featured', 'jobboard' )?></strong></label>
				</label>
			</div>
		</div>		
	</div>
</div>		