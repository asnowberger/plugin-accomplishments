<?php

/*
 * Plugin Name: Accomplishments
 * Plugin URI: http://www.jekkilekki.com
 * Description: A simple Timeline style plugin using jQuery that shows a list of Accomplishments. Think of it like a reverse Bucket List index page.
 * Version: 0.1
 * Author: Aaron Snowberger
 * Author URI: http://www.aaronsnowberger.com
 * Text Domain: accomplishments/languages
 * License: GPL2
 */

/*
 * Info:
 * Responsive Timeline Portfolio Page: http://webdesign.tutsplus.com/tutorials/building-the-responsive-timeline-portfolio-page--cms-19508
 * Bootstrap Style Tour: http://webdesign.tutsplus.com/articles/walk-users-through-your-website-with-bootstrap-tour--webdesign-17942
 * Excellent multipurpose theme: http://seventhqueen.com/themedemo/?product=kleo
 */

// ##A Create the plugin Activation function
// ##B Create the plugin Deactivation function
// ##C Create the plugin Uninstall function

// ##1 Create the Custom Post Type

// ##2 Create Custome Taxonomies

// ##3 Accomplishments index page
add_action( 'the_content', 'jkl_display_accomplishments' );