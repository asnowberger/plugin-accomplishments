/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    jQuery('.timeline-body').hide();
    
    jQuery('.timeline-info').hover(function() {
        if( jQuery(this).hasClass('hovered') ) {
            jQuery(this).removeClass('hovered');
        } else {
            jQuery(this).addClass('hovered');
        }
    });
    
    jQuery('.timeline-expand-button').hover(function() {
        if( jQuery(this).prev('.timeline-info').hasClass('hovered') ) {
            jQuery(this).prev('.timeline-info').removeClass('hovered');
        } else {
            jQuery(this).prev('.timeline-info').addClass('hovered');
        }
    });
    
    jQuery('.timeline-expand-button').click(
        function() {
            if( jQuery(this).prev('.timeline-info').hasClass('active') ) {
                jQuery(this).prev('.timeline-info').removeClass('active');
            } else {
                jQuery(this).prev('.timeline-info').addClass('active');
            }
            
            jQuery(this).next('.timeline-body').slideToggle( 'slow' );
        }
    );
    
    jQuery('.timeline-expand-all-button').click(
        function() {
            if( jQuery('.timeline-expand-all-button').hasClass('active') ) {
                jQuery('.timeline-expand-all-button').removeClass('active');
                jQuery('.timeline-body').removeClass('show-all');
            } else {
                jQuery('.timeline-expand-all-button').addClass('active');
                jQuery('.timeline-body').addClass('show-all');
            }
            
            jQuery('.timeline-body').slideToggle( 'slow' );
        });
    
    
    
});