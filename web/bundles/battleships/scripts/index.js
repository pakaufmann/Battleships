$(function() {
	$("#cssSelector").change(function() {
		var css = $("#cssSelector option:selected").attr("value");
		$.getJSON("changeCss/" + css, function(data) {
			if(data.success) {
				window.location.reload();
			}
		});
	});
});