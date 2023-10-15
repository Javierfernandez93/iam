var ChangeText = {
	timer : false,
	init : function(callback){
		var parent = this;
		this.timer = setInterval(function(){
			parent.change_text();
		}, $("[data-text]").data('delay'));
	},
	change_text : function(){
		this.text = $("[data-text]").data('text');
		this.replace_text = $("[data-text]").text();

		if(this.replace_text.length >= this.text.length)
	    	clearInterval(this.timer);
		else
			$("[data-text]").text(this.text.substring(0, this.replace_text.length + 1));
	},
};

