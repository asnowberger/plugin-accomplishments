/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    jQuery('.timeline-body').hide();
    
    jQuery('.timeline-info-expand').hover(function() {
        if( jQuery(this).hasClass('hovered') ) {
            jQuery(this).removeClass('hovered');
        } else {
            jQuery(this).addClass('hovered');
        }
    });
    
    jQuery('.timeline-expand-button').hover(function() {
        if( jQuery(this).prev('.timeline-info-expand').hasClass('hovered') ) {
            jQuery(this).prev('.timeline-info-expand').removeClass('hovered');
        } else {
            jQuery(this).prev('.timeline-info-expand').addClass('hovered');
        }
    });
    
    jQuery('.timeline-expand-button').click(
        function() {
            if( jQuery(this).prev('.timeline-info-expand').hasClass('active') ) {
                jQuery(this).prev('.timeline-info-expand').removeClass('active');
            } else {
                jQuery(this).prev('.timeline-info-expand').addClass('active');
            }
            
            jQuery(this).next('.timeline-body').slideToggle( 'slow' );
        }
    );
    
    jQuery('.timeline-expand-all-button').click(
        function() {
            if( jQuery('.timeline-expand-all-button').hasClass('active') ) {
                jQuery('.timeline-expand-all-button').removeClass('active');
                jQuery('.timeline-info').removeClass('active');
                jQuery('.timeline-info').removeClass('hovered');
                jQuery('.timeline-body').removeClass('show-all');
                jQuery('.timeline-body').slideUp( 'slow' );
            // #B : Is the button NOT ACTIVE? (and we want to OPEN everything)
            } else {
                jQuery('.timeline-expand-all-button').addClass('active');
                jQuery('.timeline-info').removeClass('active');
                jQuery('.timeline-info').removeClass('hovered');
                jQuery('.timeline-body').addClass('show-all');
                jQuery('.timeline-body').slideDown( 'slow' );
            }
        });
    
    
    
});