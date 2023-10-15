class Loader {
	static element = null;
	static html = "";
	static hideLoader() {
		$(Loader.element).html(Loader.html);

		if(ELEMENT_TYPES.BUTTON == $(Loader.element).prop("tagName"))
		{
			$(Loader.element).removeAttr("disabled");
		}
	}
	static showLoader(element)
	{
		Loader.element = element;
		Loader.html = $(Loader.element).html();

		$(Loader.element).text("Loading");

		if(ELEMENT_TYPES.BUTTON == $(Loader.element).prop("tagName"))
		{
			$(Loader.element).attr("disabled",true);
		}
	}
};

class ELEMENT_TYPES {
	static BUTTON = "BUTTON";
}