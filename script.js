jQuery(document).ready(function() {
	jQuery("#settings").show();
	jQuery("#help").hide();	
	jQuery("#settingsB").removeClass("settingsB").addClass("settingsBH"); 

	jQuery('#upload_image_button').click(function() {
		 formfield = jQuery('#upload_image').attr('name');
		 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		 return false;
	});
 
	window.send_to_editor = function(html) {
		 imgurl = jQuery('img',html).attr('src');
		 jQuery('#upload_image').val(imgurl);
		 tb_remove();
	}

	jQuery("#settingsB").click( function() {
		jQuery("#settings").show();	
		jQuery("#help").hide();	 
		jQuery("#helpB").removeClass("helpBH").addClass("helpB"); 
		jQuery("#settingsB").removeClass("settingsB").addClass("settingsBH"); 
	});   
 

	jQuery("#helpB").click( function() {
		jQuery("#settings").hide();	
		jQuery("#help").show();	
		jQuery("#helpB").removeClass("helpB").addClass("helpBH"); 
		jQuery("#settingsB").removeClass("settingsBH").addClass("settingsB");
	});
								
});



