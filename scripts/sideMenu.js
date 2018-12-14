if($('#opener').css('display')!='none'){
	$('#opener').css('display',"block");
}
$('#opener').on('click', function(event) {	
	event.preventDefault();
	var panel = $('#slide-panel');
	if (!panel.hasClass("invisibleMenu")) {
		panel.animate({'margin-left':'-500px'});
		panel.promise().done(function(){panel.addClass("invisibleMenu");});
		
	} else {
		console.log(panel.hasClass("invisibleMenu"));
		panel.removeClass("invisibleMenu").animate({'margin-left':'0px'});
		
	}
});

