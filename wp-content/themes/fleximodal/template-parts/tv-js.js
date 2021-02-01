window.hideAbout = function(){
		$('.aboutFMTV').css('display','none');
		$('.btn.about').removeClass('active');
		
		$(document).scrollTop(0);
}
window.hideTVGuide = function(){
	$('.tv-guide').css('display','none');
	$('.tv-guide-btn').removeClass('active');
	$(document).scrollTop(0);
}
window.hideSchedule = function(){
	$('#schedule').css('display','none');
	$('.schedule-btn').removeClass('active');
	$(document).scrollTop(0);
}
window.hideOnDemand = function(){
	$('#on-demand-schedule').css('display','none');
	$('.on-demand-btn').removeClass('active');
	$(document).scrollTop(0);
}
$('.logo-pause > div:first-child').click(function(){
	$('.logo-pause').css({'visibility':'hidden'});
	player.unMute();
});
var p1 = new TimelineMax({repeat: 250, yoyo: true, repeatDelay: .25, delay:0});
var p2 = new TimelineMax({repeat: 250, yoyo: true, repeatDelay: .25, delay:.15});
var p3 = new TimelineMax({repeat: 250, yoyo: true, repeatDelay: .25, delay:.3});
var p4 = new TimelineMax({repeat: 250, yoyo: true, repeatDelay: .25, delay:.6});
p1.to($('.p1'), 0.8, {opacity:0.025});
p2.to($('.p2'), 0.8, {opacity:0.025});
p3.to($('.p3'), 0.8, {opacity:0.025});
p4.to($('.p4'), 0.8, {opacity:0.025});

window.showLoader = function(){	
	$('.loader').css({'display':'block'});
	p1.play();
	p2.play();
	p3.play();
	p4.play();
}
window.hideLoader = function(){	
	$('.loader').css({'display':'none'});
	p1.pause();
	p2.pause();
	p3.pause();
	p4.pause();

}
hideLoader();

$('.tv-guide-btn').click(function(){

	if ( $('.tv-guide').css('display') == 'none' ){

		$('.tv-guide').css('display','block');		
		$('.tv-guide-btn').addClass('active');		

		hideAbout();
		hideSchedule();
		hideOnDemand();
		showLoader();			 

		var request = $.ajax({
		  url: ajaxurl,
		  type: "POST",
		  data: {action: 'get_tv_guide'},
		  dataType: "html",
		   success: function(html){
			    $( ".tv-guide .inner .channels" ).replaceWith(html);

	    		hideLoader();

			  }
		});			    

	    
		

	 } else{
	    $('.tv-guide').css('display','none');
	    $('.tv-guide-btn').removeClass('active');
	}
	
});		

$('.btn.about').click(function(){

	if ( $('.aboutFMTV').css('display') == 'none' ){
	    $('.aboutFMTV').css('display','block');
	    $('.btn.about').addClass('active');
	    
		hideTVGuide();
		hideSchedule();
		hideOnDemand();
	 } else{
	    $('.aboutFMTV').css('display','none');
	    $('.btn.about').removeClass('active');
	}
	
});	

$('.schedule-btn').click(function(){

	if ( $('#schedule').css('display') == 'none' ){

		$('.schedule-btn').addClass('active');

		hideTVGuide();
		hideAbout();
		hideOnDemand();
		showLoader();

		var request = $.ajax({
		url: ajaxurl,
		type: "POST",
	    data: {action: 'get_schedule', pid:<?php echo get_the_ID();?>, post_type:"<?php echo get_post_type();?>", onDemand:false },
		  dataType: "html",
		   success: function(html){
		   
			    $( "#schedule .inner section" ).replaceWith(html);
	    		$('#schedule').css('display','block');
	    		hideLoader();

			}
		});	

		
	  } else{
	    $('#schedule').css('display','none');
	    $('.schedule-btn').removeClass('active');
	}
	
});	
$('.on-demand-btn').click(function(){

	if ( $('#on-demand-schedule').css('display') == 'none' ){

		$('.on-demand-btn').addClass('active');

		hideTVGuide();
		hideAbout();
		hideSchedule();
		showLoader();

		var request = $.ajax({
		url: ajaxurl,
		type: "POST",
	    data: {action: 'get_schedule', pid:<?php echo get_the_ID();?>, post_type:"<?php echo get_post_type();?>", onDemand:true },
		  dataType: "html",
		   success: function(html){
		   
			    $( "#on-demand-schedule .inner section" ).replaceWith(html);
	    		$('#on-demand-schedule').css('display','block');

	    		hideLoader();

			}
		});	

		

	  } else{
	    $('#on-demand-schedule').css('display','none');
	    $('.on-demand-btn').removeClass('active');
	}
	
});	   

$('.mb-close-btn').click(function(){
	hideTVGuide();
	hideAbout();
	hideSchedule();
	hideOnDemand();
});
