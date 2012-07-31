// JavaScript Document
jQuery(document).ready(function($){
	
	if( $('#pan-pan-pan-slide').size()>0) {
		$( '#pan-pan-pan-slide' ).tabs( {
			fx:{
				opacity: 'toggle', 
				duration: 'normal'
			}
		} ).tabs( 'rotate', 7000, true ).tabs( 'option', 'event', 'click' );
	}
	$('#pan-pan-pan-slide li a').click( function() {
		return false;
	});

});
