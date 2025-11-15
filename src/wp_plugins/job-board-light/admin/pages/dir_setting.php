<h3  class=""><?php esc_html_e('Listing Setting','jobboard');  ?><small></small>	
</h3>
<br/>
<div id="update_message"> </div>		 
<form class="form-horizontal" role="form"  name='directory_settings' id='directory_settings'>											
	<?php											
		$opt_style=	get_option('jobboard_archive_template');
		if($opt_style==''){$opt_style='list';}
		$directory_url=get_option('epjbjobboard_url');
		if($directory_url==""){$directory_url='job';}
	?>	
	<div class="form-group row">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Default listing View','jobboard');  ?> <a class="btn btn-info btn-xs " href="<?php echo esc_html( get_post_type_archive_link( $directory_url)) ; ?>" target="blank"><?php esc_html_e('View Page','jobboard');  ?></a>
		</label>
		<div class="col-md-2">
			<label>												
				<input type="radio" name="option_archive"  value='list' <?php echo ($opt_style=='list' ? 'checked':'' ); ?> >
				
				<?php esc_html_e('List View','jobboard');  ?>
				
			</label>	
		</div>
		<div class="col-md-2">	
			<label>											
				<input type="radio"  name="option_archive" value='grid' <?php echo ($opt_style=='grid' ? 'checked':'' );  ?> > 
				
				<?php esc_html_e('Grid View','jobboard');  ?>
				
			</label>
		</div>	
		<div class="col-md-2">	
			<label>											
				<input type="radio"  name="option_archive" value='popupfilter' <?php echo ($opt_style=='popupfilter' ? 'checked':'' );  ?> > 
				
				<?php esc_html_e('Grid View-Popup Filter','jobboard');  ?>
				
			</label>
		</div>	
		
	</div>
	<!--
	<?php
		$job_details='';
		$dir_style5_email=get_option('dir_style5_email');	
		if($dir_style5_email==""){$dir_style5_email='yes';}
	?>	
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Email Button [List View]','jobboard');  ?></label>
		<div class="col-md-2">
			<label>												
				<input type="radio" name="dir_style5_email" id="dir_style5_email" value='yes' <?php echo ($dir_style5_email=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Email Button','jobboard');  ?>
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="dir_style5_email" id="dir_style5_email" value='no' <?php echo ($dir_style5_email=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Email Button','jobboard');  ?>
			</label>
		</div>	
	</div>		
	-->
	<div class="form-group row">
		<?php
			$dir_style5_perpage='20';						
			$dir_style5_perpage=get_option('dir_style5_perpage');	
			if($dir_style5_perpage==""){$dir_style5_perpage=20;}
		?>	
		<label  class="col-md-3 control-label">	<?php esc_html_e('Load Per Page','jobboard');  ?> </label>					
		<div class="col-md-2">																	
			<input  type="input" name="dir_style5_perpage" id="dir_style5_perpage" value='<?php echo esc_attr($dir_style5_perpage); ?>'>
		</div>						
	</div>
	<div class="form-group row">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Page Background color','jobboard');  ?></label>
		<?php
			$dir5_background_color=get_option('dir5_background_color');	
			if($dir5_background_color==""){$dir5_background_color='#EBEBEB';}	
		?>
		<div class="col-md-2">																		
			<input  type="input" name="dir5_background_color"  value='<?php echo esc_attr($dir5_background_color); ?>' >
		</div>
	</div>
	<div class="form-group row">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Content Background color [List View]','jobboard');  ?></label>
		<?php
			$dir5_content_color=get_option('dir5_content_color');	
			if($dir5_content_color==""){$dir5_content_color='#fff';}	
		?>
		<div class="col-md-2">																	
			<input  type="input" name="dir5_content_color" id="dir5_content_color" value='<?php echo esc_attr($dir5_content_color); ?>' >							
		</div>
	</div>
	<?php
		$jobboard_url=get_option('epjbjobboard_url');					
		if($jobboard_url==""){$jobboard_url='job';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('job Custom Post Type','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input  type="input" name="jobboard_url" id="jobboard_url" value='<?php echo esc_attr($jobboard_url); ?>' >
			</label>
		</div>
		<div class="col-md-2">
			<?php esc_html_e('No special characters, no upper case, no space','jobboard');  ?>
		</div>
	</div>
	<hr>
	<h4>
	<?php esc_html_e('List View','jobboard');  ?> </h4>
	Shortcode: [jobboard_all_jobs]
	<hr>	
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_cat_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Categories';						
			$dir_facet_title=get_option('dir_facet_cat_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__( 'Categories','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> <?php esc_html_e('Left Faceted Search','jobboard');  ?></label>
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_cat_show" id="dir_facet_cat_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_cat_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_postdeadline_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Deadline';						
			$dir_facet_title=get_option('dir_facet_postdeadline_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Deadline','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_postdeadline_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_postdeadline_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_salary_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Salary Range';						
			$dir_facet_title=get_option('dir_facet_salary_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Salary Range','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_salary_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_salary_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_jobtype_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Job Nature';						
			$dir_facet_title=get_option('dir_facet_jobtype_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Job Nature','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_jobtype_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_jobtype_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
		<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_experiencerange_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Experience';						
			$dir_facet_title=get_option('dir_facet_experiencerange_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Experience','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_experiencerange_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_experiencerange_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_educational_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Qualification';						
			$dir_facet_title=get_option('dir_facet_educational_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Qualification','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_educational_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_educational_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_joblevel_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Job Level';						
			$dir_facet_title=get_option('dir_facet_joblevel_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Job Level','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_joblevel_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_joblevel_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_gender_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Gender';						
			$dir_facet_title=get_option('dir_facet_gender_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Gender','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_gender_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_gender_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_location_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='City';						
			$dir_facet_title=get_option('dir_facet_location_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('City','jobboard'); }	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_location_show"  value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_location_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_features_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Features';						
			$dir_facet_title=get_option('dir_facet_features_title');	
			if($dir_facet_title==""){$dir_facet_title= esc_html__('Features','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_features_show"  value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_features_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_zipcode_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Zipcode';						
			$dir_facet_title=get_option('dir_facet_zipcode_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Zipcode','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_zipcode_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_zipcode_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_area_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Area';						
			$dir_facet_title=get_option('dir_facet_area_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Area','jobboard'); }	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_area_show"  value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_area_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<div class="form-group">
		<?php
			$dir_facet_show='yes';						
			$dir_facet_show=get_option('dir_facet_postdate_show');	
			if($dir_facet_show==""){$dir_facet_show='yes';}							
			$dir_facet_title='Post Date';						
			$dir_facet_title=get_option('dir_facet_postdate_title');	
			if($dir_facet_title==""){$dir_facet_title=esc_html__('Post Date','jobboard');}	
		?>	
		<label  class="col-md-3 control-label"> </label>					
		<div class="col-md-2">																
			<input type="checkbox" name="dir_facet_postdate_show" value="yes" <?php echo ($dir_facet_show=='yes'? 'checked':'' ); ?> > <?php esc_html_e('Show','jobboard');?>
		</div>
		<div class="col-md-5">	
			<input type="text"  name="dir_facet_postdate_title" value="<?php echo esc_attr($dir_facet_title);?>">
		</div>	
	</div>
	<hr>
	<h4><?php esc_html_e('Single Page','jobboard');  ?> </h4>
	<hr>	
	<?php
		$jobboard_apply=get_option('jobboard_apply');	
		if($jobboard_apply==""){$jobboard_apply='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Apply Button','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="jobboard_apply" id="jobboard_apply" value='yes' <?php echo ($jobboard_apply=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Apply Button','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="jobboard_apply" id="jobboard_apply" value='no' <?php echo ($jobboard_apply=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Apply Button','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$jobboard_single_bookmark=get_option('jobboard_single_bookmark');	
		if($jobboard_single_bookmark==""){$jobboard_single_bookmark='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Bookmark','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="jobboard_single_bookmark" id="jobboard_single_bookmark" value='yes' <?php echo ($jobboard_single_bookmark=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Bookmark Button','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="jobboard_single_bookmark" id="jobboard_single_bookmark" value='no' <?php echo ($jobboard_single_bookmark=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Bookmark Button','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$jobboard_single_pdf=get_option('jobboard_single_pdf');	
		if($jobboard_single_pdf==""){$jobboard_single_pdf='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('PDF','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="jobboard_single_pdf" id="jobboard_single_pdf" value='yes' <?php echo ($jobboard_single_pdf=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show PDF Button','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="jobboard_single_pdf" id="jobboard_single_pdf" value='no' <?php echo ($jobboard_single_pdf=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide PDF Button','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$dir_map=get_option('job_dir_map');	
		if($dir_map==""){$dir_map='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Map','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="dir_map" id="dir_map" value='yes' <?php echo ($dir_map=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Listing Map','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="dir_map" id="dir_map" value='no' <?php echo ($dir_map=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Listing Map','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$dir_video=get_option('job_dir_video');	
		if($dir_video==""){$dir_video='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Video','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="dir_video" id="dir_video" value='yes' <?php echo ($dir_video=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Listing Video','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="dir_video" id="dir_video" value='no' <?php echo ($dir_video=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Listing Video','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$jobboard_dir_images=get_option('jobboard_dir_images');	
		if($jobboard_dir_images==""){$jobboard_dir_images='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Images','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="jobboard_dir_images" id="jobboard_dir_images" value='yes' <?php echo ($jobboard_dir_images=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show images','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="jobboard_dir_images" id="jobboard_dir_images" value='no' <?php echo ($jobboard_dir_images=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide images','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$jooboard_single_tag=get_option('jooboard_single_tag');	
		if($jooboard_single_tag==""){$jooboard_single_tag='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Tags','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="jooboard_single_tag" id="jooboard_single_tag" value='yes' <?php echo ($jooboard_single_tag=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Tags','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="jooboard_single_tag" id="jooboard_single_tag" value='no' <?php echo ($jooboard_single_tag=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Tags','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$dir_share=get_option('epjbdir_share');	
		if($dir_share==""){$dir_share='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Share Job','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="dir_share" id="dir_share" value='yes' <?php echo ($dir_share=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show Share listing','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="dir_share" id="dir_share" value='no' <?php echo ($dir_share=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide Share Listing','jobboard');  ?>
			</label>
		</div>	
	</div>
	<?php
		$similar_job=get_option('epjbsimilar_job');	
		if($similar_job==""){$similar_job='yes';}
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Similar Jobs','jobboard');  ?></label>					
		<div class="col-md-2">
			<label>												
				<input type="radio" name="similar_job" id="similar_job" value='yes' <?php echo ($similar_job=='yes' ? 'checked':'' ); ?> ><?php esc_html_e('Show similar jobs','jobboard');  ?> 
			</label>	
		</div>
		<div class="col-md-3">	
			<label>											
				<input type="radio"  name="similar_job" id="similar_job" value='no' <?php echo ($similar_job=='no' ? 'checked':'' );  ?> > <?php esc_html_e('Hide similar jobs','jobboard');  ?>
			</label>
		</div>	
	</div>
	<hr>
	<h4><?php esc_html_e('Other Settings','jobboard');  ?> </h4>
	<hr>
	<?php
		$user_can_publish=get_option('user_can_publish');	
		if($user_can_publish==""){$user_can_publish='yes';}	
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Non admin user can publish Job','ivdirectories');  ?></label>
		<div class="col-md-2">
			<label>												
				<input type="radio" name="user_can_publish" id="user_can_publish" value='yes' <?php echo ($user_can_publish=='yes' ? 'checked':'' ); ?> > <?php esc_html_e( 'Yes, Non Admin User can Publish', 'ivdirectories' );?>  
			</label>	
		</div>
		<div class="col-md-2">	
			<label>											
				<input type="radio"  name="user_can_publish" id="user_can_publish" value='no' <?php echo ($user_can_publish=='no' ? 'checked':'' );  ?> > <?php esc_html_e( 'No,Admin will approve & publish', 'ivdirectories' );?>
			</label>
		</div>	
	</div>
	<?php
		$eprecaptcha_api=get_option('eprecaptcha_api');	
		if($eprecaptcha_api==""){$eprecaptcha_api='';}	
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Recaptcha  API Key','jobboard');  ?></label>
		<div class="col-md-5">																		
			<input class="col-md-12" type="text" name="eprecaptcha_api" id="eprecaptcha_api" value='<?php echo esc_attr($eprecaptcha_api); ?>' >						
		</div>
		<div class="col-md-4">
			<label>												
				<b> <a href="<?php echo esc_url('https://www.google.com/recaptcha/admin/create');?>"> <?php esc_html_e( 'Get your API key here', 'jobboard' );?>     </a></b>
			</label>	
		</div>

		<div class="alert alert-primary col-md-12" role="alert">
			<?php esc_html_e( 'Recaptcha: Please keep it blank if you are checking the signup/registration page on local server/host. If you active/put the Recaptcha key on local host then registration will not work.', 'jobboard' );?> 
		</div>
	</div>
	
	<?php
		$dir_map_api=get_option('epjbdir_map_api');	
		if($dir_map_api==""){$dir_map_api='';}	
	?>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Google Map API Key','jobboard');  ?></label>
		<div class="col-md-5">																		
			<input class="col-md-12" type="text" name="dir_map_api" id="dir_map_api" value='<?php echo esc_attr($dir_map_api); ?>' >						
		</div>
		<div class="col-md-4">
			<label>												
				<b> <a href="<?php echo esc_url('https://developers.google.com/maps/documentation/geocoding/get-api-key');?>"> <?php esc_html_e( 'Get your API key here', 'jobboard' );?>     </a></b>
			</label>	
		</div>					
	</div>
	
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Map Zoom','jobboard');  ?></label>
		<?php
			$dir_map_zoom=get_option('epjbdir_map_zoom');	
			if($dir_map_zoom==""){$dir_map_zoom='7';}	
		?>
		<div class="col-md-2">
			<label>												
				<input  type="input" name="dir_map_zoom" id="dir_map_zoom" value='<?php echo esc_attr($dir_map_zoom); ?>' >
			</label>	
		</div>
		<div class="col-md-2">
			<label>												
				<?php esc_html_e('20 is more Zoom, 1 is less zoom','jobboard');  ?> 
			</label>	
		</div>
	</div>
	<div class="form-group">
		<label  class="col-md-3 control-label"> <?php esc_html_e('Cron Job URL','jobboard');  ?>						 
		</label>
		<div class="col-md-6">
			<label>												
				<b> <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=jobboard_cron_job"><?php echo admin_url('admin-ajax.php'); ?>?action=jobboard_cron_job </a></b>
			</label>	
		</div>
		<div class="col-md-3">
			<?php esc_html_e( 'Cron JOB Detail : Hide Listing( Package setting),Subscription Remainder email & New job Notifications', 'jobboard' );?>  
		</div>		
	</div>
	<div class="form-group">
		<label  class="col-md-3 control-label"> </label>
		<div class="col-md-8">
			<div id="update_message49"> </div>	
			<button type="button" onclick="return  iv_update_dir_setting();" class="btn btn-success"><?php esc_html_e('Update','jobboard');  ?></button>
		</div>
	</div>
</form>