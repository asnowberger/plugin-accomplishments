/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var main = function() {
    $('.timeline-info').click(function() {
        $('.timeline-body').slideToggle();
    });
};

$(document).ready(main);