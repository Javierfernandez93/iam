$(document).ready(function(){
	window.toggleToolbar = function()
	{
		toggleToolbar();
	}	

	function toggleToolbar()
	{
		$(".toolbar").toggleClass("hide-toolbar");
		
		if($(".overlay").length > 0)
		{
			$(".overlay").remove();
		} else {
			let overlay = $("<div/>").addClass("overlay").attr("onclick","toggleToolbar()")

			$("body").append(overlay);
		}

	}
});

$(document).keyup(function(e) {
     if (e.key === "Escape") { // escape key maps to keycode `27`
        toggleToolbar();
    }
});