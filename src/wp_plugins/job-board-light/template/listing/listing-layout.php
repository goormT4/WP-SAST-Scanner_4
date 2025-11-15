<?php
get_header(); 
$opt_style=	get_option('jobboard_archive_template');
if($opt_style==''){$opt_style='grid';}
if($opt_style=='list'){
	echo do_shortcode('[jobboard_all_jobs]');
}elseif($opt_style=='grid'){
	echo do_shortcode('[jobboard_all_jobs_grid]');
}else{
	echo do_shortcode('[jobboard_all_jobs_grid_popup]');
}	
get_footer();
 ?>
