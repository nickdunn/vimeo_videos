DOM.onready(function() {
	
	DOM.select("div.field-vimeo_video").forEach(function(e, index) {
		var span = DOM.select("span", e)[0];
		var a = DOM.select("a.change", e)[0];
		var input = DOM.select("input.hidden", e)[0];
		DOM.Event.addListener(a, "click", function() {
			input.style.display = "block";
			input.value = "";
			span.style.display = "none";
		});
	});
	
});