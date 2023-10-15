$(document).ready(function(){
	let configurePin = new ConfigurePin;

	window.savePinConfigurated = function(element)
	{
		$(element).text("Espere...").attr("disabled",true);
		$(element).attr("disabled",true);

		configurePin.savePinConfigurated((response)=>{
			if(response.s == 1)
			{
				if(getParam("redirect"))
				{
					let redirect = getParam("redirect")
					
					$(element).text("Redireccionando...");
					
					setTimeout(()=>{
						window.location = redirect;
					},1000);
				}
			}
		},{pin:configurePin.getPin()});
	}

	window.checkPinLenght = function(element)
	{
		let numbers_done = 0;
		
		$("body .pin-form").each((index,input)=>{
			if($(input).val())
			{
				numbers_done++;
			}
		});

		if(numbers_done == ConfigurePin.PIN_LENGTH)
		{
			$("body #save-pin-configuration").removeAttr("disabled");
		} else {
			$("body #save-pin-configuration").attr("disabled",true);
		}

		if($(element).val())
		{
			let size = $("body .pin-form").length - 1;
			if($(element).index() < size)
			{
				$("body .pin-form").eq($(element).index()+1).focus();
				console.log("NEXT")
			} else {
				console.log("DONE")
				$("body #save-pin-configuration").focus();
			}
		}
	}
});

class ConfigurePin extends Http {
	static PIN_LENGTH = 4;
	constructor()
	{
		super();
	}
	getPin(){
		let pin = "";
		$("body .pin-form").each((index,input)=>{
			if($(input).val())
			{
				pin += $(input).val();
			}
		});

		return pin;
	}
	savePinConfigurated(callback,data){
		return this.call("../../app/application/save_pin_configurated.php",data,callback);
	}
}