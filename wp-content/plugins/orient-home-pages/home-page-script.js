jQuery(document).ready(function() {
	generateSelects();

	jQuery('input').change(function() {
		generateSelects();
	});
});

function generateSelects() {
	var inputTemplate = jQuery("#input-template");
	var count = jQuery('input:checked').attr('data-count');
	jQuery("#article-selects").html("");
	for (var i=0; i<count; i++) {
		var selected = articleIds[i];
		inputTemplate.find("option").removeAttr("selected");
		inputTemplate.find("[value=" + selected  + "]").attr("selected", "selected");
		//inputTemplate.querySelector("[value=" + selected + "]").setAttribute('selected');
		jQuery("#article-selects").append(i+1 + ": " + inputTemplate.html() + "<br>");
	}
}

