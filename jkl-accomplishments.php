<?php
/*
 * Plugin Name: JKL Accomplishments
 * Plugin URI: http://www.jekkilekki.com
 * Description: A simple Timeline style plugin using jQuery that shows a list of Accomplishments. Think of it like a reverse Bucket List index page.
 * Version: 0.1
 * Author: Aaron Snowberger
 * Author URI: http://www.aaronsnowberger.com
 * Text Domain: jkl-accomplishments/languages
 * License: GPL2
 */

/*  Copyright 2014  Aaron Snowberger

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * Codex:
 * register_post_type: https://codex.wordpress.org/Function_Reference/register_post_type 
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

// ##1 : Create the Custom Post Type AND flush rewrite rules to make permalinks work with new CPT slug
add_action( 'init', 'jkl_accomplishments_posttype' );
register_activation_hook( __FILE__, 'jkl_rewrite_flush' );

// ##2 : Create Custom Taxonomies
add_action( 'init', 'jkl_accomplishments_taxonomies' );

// ##3 : Create Custom Meta Box to catch the link and anything else we want
add_action( 'add_meta_boxes', 'jkl_accomplishments_meta_box' );

// ##4 : Save metabox data
add_action( 'save_post', 'jkl_save_meta_box' );

/*
 * From here, things will get tricky. There is currently no good way to create custom index pages for Custom Post Types:
 * @link https://tommcfarlin.com/page-template-in-plugin/
 * 
 * Because Custom Page Templates generally reside in Themes
 * @link http://codex.wordpress.org/Theme_Development#Custom_Page_Templates
 * 
 * Here's a plugin that might be useful:
 * @link https://wordpress.org/plugins/custom-post-type-page-template/
 * 
 * It's possible to try something using the single_template filter:
 * @link http://wordpress.stackexchange.com/questions/17385/custom-post-type-templates-from-plugin-folder
 * 
 * Or, the best idea may be just to use the_content filter and change stuff in there:
 * @link http://wordpress.stackexchange.com/questions/96660/custom-post-type-plugin-where-do-i-put-the-template
 * 
 * So, I'm considering creating a SHORTCODE that would be able to be placed in any Post or Page
 * that would go through the LOOP and grab all the relevant data (title, permalink, meta data, thumbnail, excerpt, etc)
 * and then output THAT in the Timeline style feature that I'm lookingn into.
 */

// ##5 : Create Shortcode to display the custom Timeline content for Accomplishments
add_shortcode( 'accomplishments', 'jkl_accomplishments_shortcode' );

// ##6 : Add a button to the WordPress Post editor panel so that users don't have to remember the shortcode
add_action( 'admin_init', 'jkl_admin_init' ); // only do this in the admin panel

/*
 * ##### 1 #####
 * Register an Accomplishments post type
 * 
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function jkl_accomplishments_posttype() {
    $labels = array(
        'name'                  => _x( 'Accomplishments', 'post type general name', 'jkl-accomplishments/languages' ),
        'singular_name'         => _x( 'Accomplishment', 'post type singular name', 'jkl-accomplishments/languages' ),
        'menu_name'             => _x( 'Accomplishments', 'admin menu', 'jkl-accomplishments/languages' ),
        'name_admin_bar'        => _x( 'Accomplishment', 'add new on admin bar', 'jkl-accomplishments/languages' ),
        'add_new'               => _x( 'Add New', 'accomplishment', 'jkl-accomplishments/languages' ),
        'add_new_item'          => __( 'Add New Accomplishment', 'jkl-accomplishments/languages' ),
        'new_item'              => __( 'New Accomplishment', 'jkl-accomplishments/languages' ),
        'edit_item'             => __( 'Edit Accomplishment', 'jkl-accomplishments/languages' ),
        'view_item'             => __( 'View Accomplishment', 'jkl-accomplishments/languages' ),
        'all_items'             => __( 'All Accomplishments', 'jkl-accomplishments/languages' ),
        'search_items'          => __( 'Search Accomplishments', 'jkl-accomplishments/languages' ),
        'parent_item_colon'     => __( 'Parent Accomplishments', 'jkl-accomplishments/languages' ),
        'not_found'             => __( 'No accomplishments found.', 'jkl-accomplishments/languages' ),
        'not_found_in_trash'    => __( 'No accomplishments found in Trash.', 'jkl-accomplishments/languages' ),
    );
    
    // Find Dashicons @ http://melchoyce.github.io/dashicons
    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-flag',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'accomplishments' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => null,
        'supports'               => array( 'title', 'author', 'thumbnail', 'excerpt' ) // Also can include 'editor', 'comments'
    );  
    
    register_post_type( 'accomplishments', $args );    
}

function jkl_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    
    // i.e. If in development, you may have to resave your permalinks or deactivate, 
    // then reactivate the CPT plugin to get this function to run on plugin activation
    
    jkl_accomplishments_posttype();
    flush_rewrite_rules();
}


/*
 * ##### 2 #####
 * Register custom taxonomies
 * 
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function jkl_accomplishments_taxonomies() {
    /*
     * Add new Accomplishment Type taxonomy, hierarchical (like categories)
     */
    $labels = array(
        'name'                  => _x( 'Accomplishment Type', 'taxonomy general name', 'jkl-accomplishments/languages' ),
        'singular_name'         => _x( 'Accomplishment Type', 'taxonomy singular name', 'jkl-accomplishments/languages' ),
        'search_items'          => __( 'Search Accomplishment Types', 'jkl-accomplishments/languages' ),
        'popular_items'         => __( 'Most Viewed Accomplishment Types', 'jkl-accomplishments/languages' ),
        'all_items'             => __( 'All Accomplishment Types', 'jkl-accomplishments/languages' ),
        'parent_item'           => __( 'Parent Accomplishment Type' ),
        'parent_item_colon'     => __( 'Parent Accomplishment Type:' ),
        'edit_item'             => __( 'Edit Accomplishment Type', 'jkl-accomplishments/languages' ),
        'update_item'           => __( 'Update Accomplishment Type', 'jkl-accomplishments/languages' ),
        'add_new_item'          => __( 'Add New Accomplishment Type', 'jkl-accomplishments/languages' ),
        'new_item_name'         => __( 'New Accomplishment Type', 'jkl-accomplishments/languages' ),
        'menu_name'             => __( 'Accomplishment Type', 'jkl-accomplishments/languages' ),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'types' ),
    );
    
    register_taxonomy( 'accomplishment-type', array( 'accomplishments' ), $args );
    
    
    /*
     * Add new Satisfaction Level taxonomy, NOT hierarchical (like tags)
     */
    $labels = array(
        'name'                          => _x( 'Satisfaction Level', 'taxonomy general name', 'jkl-accomplishments/languages' ),
        'singular_name'                 => _x( 'Satisfaction Level', 'taxonomy singular name', 'jkl-accomplishments/languages' ),
        'search_items'                  => __( 'Search Satisfaction Levels', 'jkl-accomplishments/languages' ),
        'popular_items'                 => __( 'Most Viewed Satisfaction Levels', 'jkl-accomplishments/languages' ),
        'all_items'                     => __( 'All Satisfaction Levels', 'jkl-accomplishments/languages' ),
        'parent_item'                   => null,
        'parent_item_colon'             => null,
        'edit_item'                     => __( 'Edit Satisfaction Level', 'jkl-accomplishments/languages' ),
        'update_item'                   => __( 'Update Satisfaction Level', 'jkl-accomplishments/languages' ),
        'add_new_item'                  => __( 'Add New Satisfaction Level', 'jkl-accomplishments/languages' ),
        'new_item_name'                 => __( 'New Satisfaction Level', 'jkl-accomplishments/languages' ),
        'separate_items_with_commas'    => null, //__( 'Separate satisfaction levels with commas', 'jkl-accomplishments/languages' ),
        'add_or_remove_items'           => __( 'Add or remove satisfaction levels', 'jkl-accomplishments/languages' ),
        'choose_from_most_used'         => __( 'Choose from the most used levels', 'jkl-accomplishments/languages' ),
        'not_found'                     => __( 'No satisfaction levels found.', 'jkl-accomplishments/languages' ),
        'menu_name'                     => __( 'Satisfaction Level', 'jkl-accomplishments/languages' ),
    );

    $args = array(
        'hierarchical'                  => false,
        'labels'                        => $labels,
        'show_ui'                       => true,
        'show_admin_column'             => true,
        'update_count_callback'         => '_update_post_term_count',
        'query_var'                     => true,
        'rewrite'                       => array( 'slug' => 'satisfaction' ),
    );
    
    register_taxonomy( 'satisfaction-level', array( 'accomplishments' ), $args );
    
    
    /*
     * Add new Difficulty Level taxonomy, NOT hierarchical (like tags)
     */
    $labels = array(
        'name'                          => _x( 'Difficulty Level', 'taxonomy general name', 'jkl-accomplishments/languages' ),
        'singular_name'                 => _x( 'Difficulty Level', 'taxonomy singular name', 'jkl-accomplishments/languages' ),
        'search_items'                  => __( 'Search Difficulty Levels', 'jkl-accomplishments/languages' ),
        'popular_items'                 => __( 'Most Viewed Difficulty Levels', 'jkl-accomplishments/languages' ),
        'all_items'                     => __( 'All Difficulty Levels', 'jkl-accomplishments/languages' ),
        'parent_item'                   => null,
        'parent_item_colon'             => null,
        'edit_item'                     => __( 'Edit Difficulty Level', 'jkl-accomplishments/languages' ),
        'update_item'                   => __( 'Update Difficulty Level', 'jkl-accomplishments/languages' ),
        'add_new_item'                  => __( 'Add New Difficulty Level', 'jkl-accomplishments/languages' ),
        'new_item_name'                 => __( 'New Difficulty Level', 'jkl-accomplishments/languages' ),
        'separate_items_with_commas'    => null, //__( 'Separate difficulty levels with commas', 'jkl-accomplishments/languages' ),
        'add_or_remove_items'           => __( 'Add or remove difficulty levels', 'jkl-accomplishments/languages' ),
        'choose_from_most_used'         => __( 'Choose from the most used levels', 'jkl-accomplishments/languages' ),
        'not_found'                     => __( 'No difficulty levels found.', 'jkl-accomplishments/languages' ),
        'menu_name'                     => __( 'Difficulty Level', 'jkl-accomplishments/languages' ),
    );

    $args = array(
        'hierarchical'                  => false,
        'labels'                        => $labels,
        'show_ui'                       => true,
        'show_admin_column'             => true,
        'update_count_callback'         => '_update_post_term_count',
        'query_var'                     => true,
        'rewrite'                       => array( 'slug' => 'difficulty' ),
    );
    
    register_taxonomy( 'difficulty-level', array( 'accomplishments' ), $args );
}


/*
 * ##### 3 #####
 * Create a Custom Meta Box for the Accomplishments post type
 * 
 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
 */
function jkl_accomplishments_meta_box() {
    add_meta_box( 
        'jklac_info',                                                           // Unique ID (for CSS)
        __('Link to Your Accomplishment', 'jkl-accomplishments/languages'),     // Title
        'jkl_display_accomplishments_meta_box',                                 // Callback function
        'accomplishments',                                                      // Post type to display on
        'normal',                                                               // Context
        'high'                                                                  // Priority
                                                                                // Callback_args
    );
}

/*
 * ##### 4 #####
 * DISPLAY the Custom Meta Box fields (i.e. This is the Metabox handler)
 * 
 * @param WP_Post $post The object for the current post/page
 */

function jkl_display_accomplishments_meta_box ( $post ) {
    /*
     * Metabox fields                                           Escaped (output)
     * 1. Link to Accomplishment    => jklac_link               esc_url
     * 2. Major Checkbox            => jklac_major              esc_attr
     */
    
    // Add an nonce field so we can check for it later.
    wp_nonce_field( basename(__FILE__), 'jklac_meta_box_nonce' );
    
    /*
     * Use get_post_meta() to retrieve an existing value 
     * from the database and use the value for the form
     */
    
    // $jklac_meta = get_post_meta( $post->ID );
    $jklac_meta = get_post_meta( $post->ID );
    
    $link = isset( $jklac_meta['jklac_link'] ) ? esc_url( $jklac_meta['jklac_link'][0] ) : '' ;
    $check = isset( $jklac_meta['jklac_major'] ) ? esc_attr( $jklac_meta['jklac_major'][0] ) : '';
    
    ?>
    <!-- Show the URL box for the Link to the Accomplishment -->
    <input type='url' id='jklac_link' name='jklac_link' class='widefat' value='<?php echo $link; ?>' /><br /><br />

    <!-- BELOW: Show the Checkbox to decide whether or not to add this to the main timeline -->
    <input type='checkbox' id='jklac_major' name='jklac_major' value='1' <?php checked( $check, 1 ); ?> />
    <label for='jklac_major'>
        <?php _e( 'Do you want this Accomplishment to appear in your Primary Timeline? (i.e. is this a <a href="http://www.access.gpo.gov/nara/cfr/waisidx_03/16cfr255_03.html">Major Accomplishment</a>?)', 'jkl-reviews/languages'); ?>
    </label>
       
    <?php
}


/*
 * ##### 4 #####
 * Fourth, Save the custom metadata
 * 
 * @param int $post_id for the ID of the post being saved
 */
function jkl_save_meta_box( $post_id ) {
    
    /*
     * Ref @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Verify this came from our screen and with proper authorization and that we're ready to save.
     */
    
    // Check if nonce is set
    if ( !isset( $_POST['jklac_meta_box_nonce'] ) ) { return; }
    
    // Verify the nonce is valid
    if ( !wp_verify_nonce( $_POST['jklac_meta_box_nonce'], basename(__FILE__) ) ) { return; }
    
    // Check for autosave (don't save metabox on autosave)
    if ( defined ('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    
    // Check the user's permissions.
    if ( ! current_user_can( 'edit_page', $post_id ) ) { return; }
    /*
     * After all those checks, save. TODO: Sanitize
     */
    
    // Save the Accomplishment Link URL
    if( isset($_POST[ 'jklac_link' ] ) ) {
        update_post_meta( $post_id, 'jklac_link', $_POST['jklac_link'] ); // Unnecessary sanitization/validation?
    }
    
    // Save the Major Accomplishment Checkbox
    if( isset( $_POST['jklac_major'] ) ) {
        update_post_meta( $post_id, 'jklac_major', $_POST['jklac_major'] );
    } else {
        delete_post_meta( $post_id, 'jklac_major' );
    }
}


/*
 * ##### 5 #####
 * Fifth, create a shortcode to display the Accomplishments post type in a Post or Page
 * 
 * @param
 * 
 * @link http://www.sitepoint.com/unleash-the-power-of-the-wordpress-shortcode-api/ 
 */
function jkl_accomplishments_shortcode( $atts, $content ) {
    $atts = shortcode_atts( // override the $atts variable with these $atts
        array(
            'major'     => false,
            'style'     => 'two-column',    // Default to a two-column Timeline layout
            'content'   => !empty($content) ? $content : 'My List of Accomplishments this year (' . date('Y') . ')'
        ), $atts // compare against the $atts that are passed in and overwrite whatever isn't specified
    );

    extract( $atts ); // goes into the array and makes all the values available as variables
    

    /*
     * Instantiate a new WP_Query Loop to get all the posts in this Accomplishments post type
     * 
     * @link http://codex.wordpress.org/Class_Reference/WP_Query
     */
    $args = array(
        'post_type'         => 'accomplishments',
        'posts_per_page'    => -1,
        'year'              => date( 'Y' ),
        //array( // MAYBE we can use this to determine the CSS stylesheet to pass in
        //    'meta_key'      => 'style',
        //   'meta_value'    => 'style',          // Here's where we can determine which style to give it
        //)
    );
    
    $query = new WP_Query( $args );
    $html = ''; // Create the $html variable to store our HTML for output
    
    // If there are Accomplishments (and only if)...
    if( $query->have_posts() ) :
        // call the CSS stylesheet to handle the Timeline
        if( $style == 'two-column' ) {
            wp_register_style( 'jklac_two_column_style', plugin_dir_url( __FILE__ ) . '/css/two-column.css', false, '1.0.0' );
            wp_enqueue_style( 'jklac_two_column_style' );
        } else {
            wp_register_style( 'jklac_single_column_style', plugin_dir_url( __FILE__ ) . '/css/single-column.css', false, '1.0.0' );
            wp_enqueue_style( 'jklac_single_column_style' );
        }
        
        // create the timeline content
        $html .= "<h1>$content</h1>";
        while ( $query->have_posts() ) : $query->the_post();
    
            // Start the Loop here
            $html .= "<article class='timeline-item group'>";       // Add class 'first' to the first article
            $html .= "<header class='timeline-info'>";
            $html .= "<div class='date'>" . get_the_date() . "</div>";                    // Needs the publication date
            $html .= "<div class='description'>" . get_the_excerpt() . "</div>";             // Needs the excerpt
            $html .= "<div class='meta'>" . get_the_author() . "</div>";                    // Needs the custom taxonomies
            $html .= "</header>"; 
            if( has_post_thumbnail() ) {
                $html .= "<figure class='timeline-image'>" . the_post_thumbnail() . "</figure>";    // Needs the thumbnail
            }
            $html .= "</article>";

        endwhile;

        wp_reset_postdata();
        
        // Add the 'Load More' link here (Turn this OFF if all posts are loaded already)
        $html .= "<article class='timeline-item group loading-wrap'>";
        $html .= "<header class='timeline-info'></header>";
        $html .= "<figure class='timeline-image'>";
        $html .= "<div class='loading'><i class='fa fa-spinner'></i> Loading</div>"; // Use a FontAwesome or Dashicon here rather than an image
        $html .= "</figure>";
        $html .= "</article>";
    else :
        $html .= __( "There are no Accomplishments to boast of yet. Why don't you <a href=''>add one?</a>" ); 
    endif;
    
    
    return $html;
}

function jklac_timeline_styles( $atts ) {
    
    // Determine whether this is a single-column or two-column Timeline and call the appropriate stylesheet
    if ( $major == true ) {
        wp_register_style( 'jkl_review_box_display_css', plugin_dir_url( __FILE__ ) . '/css/boxstyle.css', false, '1.0.0' );
        wp_enqueue_style( 'jkl_review_box_display_css' );
    } else {
        
    }
    
    // Also, add Font Awesome to our front-end styles
    wp_enqueue_style( 'fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
}


/*
 * ##### 6 #####
 * Sixth, add a button to the WordPress Post editor panel so users don't have to remember the shortcode
 * 
 * @link http://www.sitepoint.com/adding-a-media-button-to-the-content-editor/
 * @link http://solislab.com/blog/how-to-make-shortcodes-user-friendly/
 */
function jkl_admin_init() {
    // Only hook up these filters if we're in the admin panel, and the current user
    // has permission to edit Posts and Pages
    if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        add_filter( 'mce_buttons', 'jklac_mce_button' );
        add_filter( 'mce_external_plugins', 'jklac_mce_plugin' );
    }
}

function jklac_mce_button( $buttons ) {
    // Add a separation before our button
    array_push( $buttons, '|', 'accomplishments_button' );
    return $buttons;
}

function jklac_mce_plugin( $plugins ) {
    // This plugin file will work the magic of our button
    $plugins['accomplishments'] = plugin_dir_url( __FILE__ ) . 'js/accomplishments_plugin.js';
    return $plugins;
}