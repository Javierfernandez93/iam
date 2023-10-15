$(document).ready(function() {

		 $(document).ready(function(){
		   
		        window.copyToClipBoard = function(element,text) {
		            var $temp = $("<input>");
		            $("body").append($temp);
		            $temp.val(text).select();
		            document.execCommand("copy");
		            $temp.remove();

		            $(element).html("¡Listo!, copiamos tu landing page, ahora puedes compartirla");
		        }
		    });
});