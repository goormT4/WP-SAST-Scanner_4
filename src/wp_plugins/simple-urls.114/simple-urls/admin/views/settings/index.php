<?php
/**
 * Dashboard
 *
 * @package Dashboard
 */
use LassoLite\Classes\Helper;
echo Helper::include_with_variables( Helper::get_path_views_folder() . 'header-new.php', array(), false );
echo Helper::GET()['page'];