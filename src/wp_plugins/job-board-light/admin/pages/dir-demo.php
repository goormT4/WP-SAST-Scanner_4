			<?php
	
			global $wpdb;
			global $current_user;
			$ii=1;
			$directory_url=get_option('epjbjobboard_url');					
			if($directory_url==""){$directory_url='job';}			
			?>
	<div class="row">
		<div class="col-md-6 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class=""><?php esc_html_e('Demo Import','jobboard');?></h3>                    
                </div>
                <div class="panel-body">
							<div class="progress">
							  <div id="dynamic" class=" progress-bar progress-bar-success progress-bar-striped active " role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" >
								<span id="current-progress"></span>
							  </div>
							</div>
						<div class="row">
						<div class="col-md-4"></div>
						
							<div class="col-md-4 none " id="cptlink12" > <a  class="btn btn-info " href="<?php echo get_post_type_archive_link( $directory_url) ; ?>" target="_blank"><?php esc_html_e('View All Listing','jobboard');?>  </a>
							</div>
						<div class="col-md-4"></div>	
						</div>	
						<div class="row" id="importbutton">						
							<div class="col-md-12 "> 
							<center>
							<button type="button" onclick="return  iv_import_medo();" class="btn btn-success"><?php esc_html_e('Import Demo Jobs','jobboard');?> </button>
							</center>
							</div>
						</div>					
                </div>
			</div>
        </div>
		<div class="col-md-6 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3><?php esc_html_e('Importing CSV Data ','jobboard');?></h3>                    
                </div>
                <div class="panel-body">
                    <div class="tab-content">
						  <?php
							 include('csv-import.php');
							?>					
                    </div>
                </div>
            </div>
        </div>
		<div class="col-md-6 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3><?php esc_html_e('Home Page Content','jobboard');?> 
						<span id="addhome-succcess"><button type="button" onclick="return  iv_create_home_page();" class="btn btn-success"><?php esc_html_e('Create Home page','jobboard');?>  </button> </span>
						
						</h3>  
						<small><?php esc_html_e('Create a full width page and paste the code','jobboard');?> </small>
						<p><a class="btn btn-info btn-xs" href="http://help.eplug-ins.com/jobboard/slider/jobboard-slider.zip" download ><?php esc_html_e('Download & import the top Revolution Slider','jobboard');?>  </a> </p>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                       <code>
										<p>[rev_slider alias="jobboard-slider"][/rev_slider]</p>
											<h2><br/> </h2>
											<h2><br/> </h2>
											<h2 style="text-align: center;">New Jobs</h2>
											<p style="text-align: center;">  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do 
												quis nostrud  quat.</p>
											<p style="text-align: center;">---------------------------------</p>
											<p style="text-align: center;">[listing_filter post_limit="3"]</p>
											<h2><br/> </h2>
											<h2 style="text-align: center;">Find a Job That Fits Your Education</h2>
											<p style="text-align: center;">  Lorem ipsum dolor sit amet, consectetur adipiscing elit, 
												sed do eiusmod tempor </p>
											<p style="text-align: center;">---------------------------------</p>
											<p style="text-align: center;">[jobboard_categories post_limit="4"]</p>
											<h2><br/> </h2>
											<h2 style="text-align: center;">Featured Jobs</h2>
											<p style="text-align: center;">  Lorem ipsum dolor sit amet, consectetur adipiscing elit, 
												sed do eiusmod  et dolore</p>
											<p style="text-align: center;">---------------------------------</p>
											<h2><br/> </h2>
											<p style="text-align: center;">[jobboard_featured]</p>
											<h2><br/> </h2>
											<h2 style="text-align: center;">Browse Jobs in these Cities</h2>
											<p style="text-align: center;">  Lorem ipsum dolor sit amet, consectetur adipiscing elit, 
												sed do eiusmod tempor  ut labore et dolore</p>
											<p style="text-align: center;">---------------------------------</p>
											<h2><br/> </h2>
											<p style="text-align: center;">[jobboard_cities cities="london,New York,Dubai"]</p>
                       </code>
                    </div>
                </div>
            </div>
        </div>
			<div class="col-md-6 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 ><?php esc_html_e('Some Important shortcode','jobboard');?>
					<a class="btn btn-info btn-xs" href="<?php echo esc_url('//help.eplug-ins.com/jobboard/' ); ?>" target="_blank">
						<?php esc_html_e('All Shortcodes','jobboard');?>  </a>
					</h3> 
                </div>
                <div class="panel-body">
					<div class="tab-content">
							<div class="row">
									<div class="col-md-6">	
									<?php esc_html_e('Listing Filter ( you can use any parameter e.g. [listing_filter employer="testcompany"] )','jobboard');?> 
									 
									</div>
										<div class="col-md-6">	
										[listing_filter ids="20,30,10" employer="testcompany" gender="male" joblevel="Mid Level" job_type="
Full Time"  experiencerange="3 - <5 Years" category="test" city="test" zipcode="10001" post_limit="3"]
									</div>
								</div>	
								<hr/>
								<div class="row">
									<div class="col-md-6">	
									 <?php esc_html_e('Slider Search bar(You can use without slider too)','jobboard');?>
									</div>
										<div class="col-md-6">	
										[slider_search]
									</div>
								</div>
								<hr/>
								<div class="row">
									<div class="col-md-6">	
									<?php esc_html_e('City Shortcode','jobboard');?> 
									</div>
										<div class="col-md-6">	
										[jobboard_cities cities="london,new york,FLORIDA,California"]
									</div>
								</div>
								<hr/>
								<div class="row">
									<div class="col-md-6">	
									<?php esc_html_e('Featured Listing only','jobboard');?> 
									</div>
										<div class="col-md-6">	
										[jobboard_featured]
									</div>
								</div>
								<hr/>																
								<div class="row">
									<div class="col-md-6">	
									<?php esc_html_e('All Listings: List View: All parameter:[jobboard_all_jobs employer="testcompany" category="test" city="test" zipcode="10001" ]','jobboard');?>
									
									</div>
									<div class="col-md-6">	
										[jobboard_all_jobs]
									</div>
									<div class="col-md-6">	
										[jobboard_all_jobs_grid]
									</div>
									<div class="col-md-6">	
									[jobboard_all_jobs_grid_popup]
									</div>
								</div>	
					</div>			
                </div>
            </div>
        </div>
	</div>