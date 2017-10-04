jQuery(document).ready(function() {
	generateSelects();
	updateImage();

	jQuery('input[name=file]').change(function() {
		generateSelects();
		updateImage();
	});
});

function generateSelects() {
	var inputTemplate = jQuery("#input-template");
	if(inputTemplate) {
		var count = jQuery('input[name=file]:checked').attr('data-count');
		jQuery("#article-selects").html("");
		for (var i=0; i<count; i++) {
			var selected = articleIds[i];

			inputTemplate.find("option").removeAttr("selected");

			if(selected !== "") {
				inputTemplate.find("[value=" + selected  + "]").attr("selected", "selected");
			}

			jQuery("#article-selects").append(i+1 + ": " + inputTemplate.html() + "<br>");
		}
	}
}

function updateImage() {
	var img = jQuery("#orient_homepage_image")
	var templateName = jQuery("input[name=file]:checked").attr('value');
	if(templateName) {
		templateName2 = templateName.substring(0, templateName.length - 4);
		img.attr('src', '/wp-content/themes/orient-theme/homepages/' + templateName2 + '.png');
	}
}
