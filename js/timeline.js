/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var main = function() {
    $('.timeline-info').hover(function() {
        $('.timeline-body').slideDown( 'slow', function() {
            
        });
    });
};

$(document).ready(main);