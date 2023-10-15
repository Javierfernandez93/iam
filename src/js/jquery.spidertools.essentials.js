	// --------------------   //////   -------------------------//
	// --------------- LANGUAGE FUNCTIONS -------------------------//
	// --------------------   //////   -------------------------//

	function active_language_selector()
	{
		$("#language_selector").bind("click", function(){
			if($("#div_languages").length)
			{
				$("#div_languages").remove();
			} else {
				show_languages();
			}
		});
	}

	function show_languages(languages)
	{
		if(!languages)
		{
			languages = get_catalog_languages();
		}

		var div_languages = $.__createDiv({"id": "div_languages"});
		var ul_language = $.__createUl({"id": "ul_language_selector"});

		$.each(languages, function(key, language){
			if ( language != "system" )
			{
				var li_language = $.__createLi({"id": "language_selector-" + language, "content": language });
					ul_language.append(li_language);
					li_language.bind("click", function(){
						__loadDictionaryInStorage({language:language});
						set_language(language);
						window.location.reload(true);

						//div_languages.remove();
						//location.reload();
						//alert(language.language);
						//return false;
					});
			}

		});

		div_languages.append(ul_language);
		$("#language_selector").append(div_languages);

		/* animation */
		div_languages.animate({ opacity: 1, top: "74px" }, 400);
	}

	function set_language(language)
	{
		var data = { "data": { "language": language }, "url": "../../spiderframe/application/set_language.php" };
		var returnData = __getJSONRequest(data);

		if(returnData.success == 1)
		{
			return true;
		}

		return false;
	}

	function get_catalog_languages(options)
	{
		var defaults = {
				"url": "../../spiderframe/application/get_languages.php",
			};

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url});
	}


	// --------------------   //////   -------------------------//
	// --------------- LOCATION FUNCTIONS ---------------------//
	// --------------------   //////   -------------------------//

	function get_catalog_cities(options)
	{
		var defaults = {
    	    	"active": 1,
    	    	"state_id": false,
    	    	"url": "../../spiderframe/application/get_catalog_cities.php",
    	    };

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url, "data": {"active": options.active, "state_id": options.state_id } });
	}

	function get_catalog_states(options)
	{
		var defaults = {
    	    	"active": 1,
    	    	"country_id": false,
    	    	"url": "../../spiderframe/application/get_catalog_states.php",
    	    };

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url, "data": {"active": options.active, "country_id": options.country_id } });
	}

	function get_catalog_countries(options)
	{
		var defaults = {
    	    	"active": 1,
    	    	"url": "../../spiderframe/application/get_catalog_countries.php",
    	    };

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url, "data": {"active": options.active } });
	}

	function get_catalog_format_date(options)
	{
		var defaults = {
				"url": "../../spiderframe/application/get_catalog_format_date.php",
			};

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url});
	}

	function get_catalog_format_number(options)
	{
		var defaults = {
				"url": "../../spiderframe/application/get_catalog_format_number.php",
			};

   		var options = $.extend(defaults, options);

		return __getJSONRequest({"url": options.url});
	}

	// --------------------   //////   -------------------------//
	// --------------- LOAD ON SELECT BOX FUNCTIONS ---------------------//
	// --------------------   //////   -------------------------//

	function load_catalog_on_select(options)
	{
		var defaults = {
    	    	"items": null,
    	    	"select_id": null,
    	    	"selector": "#",
    	    	"item_selected_id": null,
    	    };

    	var options = $.extend(defaults, options);

    	if(options.items)
    	{
    		$.each(options.items, function(key, value){
				$(options.selector + options.select_id).append("<option value='" + key + "' > " +  value + "</option>");
			});

			if( options.item_selected_id )
			{
				$(options.selector + options.select_id + " option[value='" + options.item_selected_id + "']").prop('selected', 'selected').change();
			}
    	}

		return true;
	}

	function load_catalog_languages_on_select(languages, language_id, select_id, selector)
	{
		if(!languages)
		{
			languages = get_catalog_languages();
		}

		if(!select_id)
		{
			select_id = "language";
		}

		delete languages["system"];

		load_catalog_on_select({"select_id": select_id, "item_selected_id": language_id, "items": languages, "selector": selector });
	}

	// --------------------   //////   -------------------------//
	// --------------- ACTIVE ACTION FUNCTION ---------------------//
	// --------------------   //////   -------------------------//

	function __activeAction (token, class_name)
	{
		token = (token) ? token : false;
		class_name = (class_name) ? class_name : "active_action";

		$("." + class_name).bind("click", function() {

			var defaults = { "field": "active", "inactive_class": "inactive_item"};
			var id = $(this).attr("id");
			var data = $(this).data();
   			var data = $.extend(defaults, data);

   			var new_data = {
   					"value": data.inverse_value,
					"inverse_value": data.value,
					"status": data.inverse_status,
					"inverse_status": data.status,
				};

			var data_row = new DataRow();
				data_row.SetToken(token);
				//data_row.SetLoader(true);
				data_row.SetUserType(data.user_type);

			/** */
			//  ap::row
			var data_active = data_row.NewRow(data.row);
				data_active.SetId(data.id);

			//  app::fields
			var data_field = data_active.SetField(data.field);
				data_field.SetProperty("value", data.inverse_value);

			var parent_id =  "container-" + data.row + "-" + data.id;
			var new_class = (data.value == 0) ? "" : data.inactive_class;
			var old_class = (data.value == 1) ? "" : data.inactive_class;
			//console.log(data_row.GetData());
			new_data = $.extend(data, new_data);

			var options = {"Ok":
							function(){
								__closeMessage();
								var returnData = data_row.Save();
								//console.log(returnData);
								if(returnData.success == 1)
								{
									$("#" + id).html(__Translate(data.inverse_status));
									$("#" + id).data(new_data);
									$("#" + parent_id).removeClass(old_class);
									$("#" + parent_id).addClass(new_class);
								} else {
									__showMessage({"message": __Translate(returnData.reason)});
								}

							},
							"Cancel":
							function(){
								__closeMessage();
							}
						 };

			__showMessage({"message": __Translate("Are you sure to " + data.status + "?"), "options":options});
			/** */
		});
	}

	// --------------------   //////   -------------------------//
	// ------------------ TRANSLATE DATA -----------------------//
	// --------------------   //////   -------------------------//


	/* -------------------------------- */
	/* -------METHODS BY JAVIER------- */
	/* ------------------------------ */

	/* -- HOW TO USE: --

			* METHOD 1 : translate_data_by_find("#abc"); // GIVEN AN ID
			* METHOD 3 : translate_data_by_find(".abc"); // GIVEN AN CLASS
			* METHOD 3 : translate_data_by_find();       // FINDING DATA-TRANSLATE ON "TRUE"

	** NOTE: IF YOU ALREADY USING METHOD 1 OR METHOD 2, DOENST MATTER IF IT HAS A CHILDS OR NOT, IT WILL BE TRANSLATED ***

	-- HOW TO USE: -- */
	/* -------------------------------- */
	/* -------METHODS BY JAVIER------- */
	/* ------------------------------ */
	function translate_data_by_find(element_selector)
	{
		var data = null;

		if(element_selector)
		{
			data = ($(element_selector).children().length > 0) ? $(element_selector).find("*") : $(element_selector);
		} else {
			data = $("[data-translate='true']").find("*");
		}

		$.each(data,function(key){
			if(exclutions(this))
			{
				translate_by_element_type(this);
			}
		});
	}

	/* THIS METHOD GETS AN ELEMENT TYPE AND EVALUATES */
	/* WHICH KIND OF ATTRIBUTE WILL BE TRANSLATED */
	function translate_by_element_type(element)
	{
		var text = "";
		var section = $(this).data("translate_section");

		if( $(element).text() !== undefined && $(element).text() !== "" )
		{
			text = 	$(element).text();
					$(element).text( __Translate(text, section) );
		} else if( $(element).attr("placeholder") !== undefined && $(element).attr("placeholder") != ""){
			text = 	$(element).attr("placeholder");
					$(element).attr("placeholder", __Translate(text, section));
		} else if( $(element).val() !== undefined && $(element).val() != "" ){
			text = 	$(element).val();
					$(element).val( __Translate(text, section) );
		}

		return false;
	}

	/* THIS METHOD GETS AN ELEMENT VALUE AND EVALUATES IF IT WILL */
	/* BE RETURNED FOR MAKE A TRANSLETE OR IF IT'LL BE DEPRECATED */
	function exclutions(element)
	{
		var exclutions = ["IMG", "IFRAME", "UL", "TABLE", "TBODY", "THEAD", "TR"];

		/* for links inside of elements */
		if( $(element).prop("tagName") === "TD"  && $(element).children().prop("tagName") === "A" )
			return false;

		if( $(element).prop("tagName") === "TH"  && $(element).children().prop("tagName") === "A" )
			return false;

		if( $(element).prop("tagName") === "LI"  && $(element).children().prop("tagName") === "A" )
			return false;

		if( $(element).prop("tagName") === "DIV" && $(element).children().length > 0 && $(element).find("ul").find("a") )
			return false;

		if( $(element).prop("tagName") === "DIV" && $(element).children().length > 0 && $(element).find("ul").find("a") )
			return false;

		/* IF DATA_TRANSLATE IS FALSE */
		if( $(element).data("translate") == false )
			return false;

		if( exclutions.indexOf($(element).prop("tagName")) != -1 )
			return false;

		return true;
	}
	/* -------------------------------- */
	/* -------METHODS BY JAVIER------- */
	/* ------------------------------ */

	function translate_data()
	{
		var data = $("[data-translate='true']");

		$.each(data, function(key){
			var text = "";
			var entity = $(this).prop("tagName");
			var section = $(this).data("translate_section");

			/** */
			if( $(this).html() !== undefined && $(this).html() !== "" )
			{
				text = 	$(this).html();
						$(this).html( __Translate(text, section) );
			} else if( $(this).attr("placeholder") !== undefined && $(this).attr("placeholder") != ""){
				text = 	$(this).attr("placeholder");
						$(this).attr("placeholder", __Translate(text, section));
			} else if( $(this).val() !== undefined && $(this).val() != "" ){
				text = 	$(this).val();
						$(this).val(__Translate(text, section));
			}/** */

			if(entity == "UL")
			{
				$(this).find('li').each(function(n, li) {
		            text = 	$(li).html();
					$(li).html( __Translate(text, section) );
		      	});
			}
			//console.log("line " + key + " " + __Translate(html));
		});

		return true;
	}

	// --------------------   //////   -------------------------//
	// -------------------- SHOW CLOCK -------------------------//
	// --------------------   //////   -------------------------//
	function show_clock()
	{
		var digital_date= new Date();
		var hours 		= digital_date.getHours();
		var minutes 	= digital_date.getMinutes();
		var seconds 	= digital_date.getSeconds();
		var dn = "AM";

		if ( hours > 12)
		{
			dn = "PM";
			hours = hours - 12;
		}

		if (hours == 0){ hours = 12; }
		if (minutes<=9){ minutes = "0" + minutes; }
		if (seconds<=9){ seconds = "0" + seconds; }

		$("#info_date").html(hours + ":" + minutes + ":" + seconds + " " + dn);

		setTimeout("show_clock()",1000);
	}

	// --------------------   ////////   -----------------------//
	// -------------------- ACTIVE LINKS -----------------------//
	// --------------------   ////////   -----------------------//
	function active_links()
	{
		var link_name = false;
		var current_link = false;
		var href_link = $(location).attr("href");
		var sections_link = href_link.lastIndexOf("/") + 1;
		var page_name = href_link.substring(sections_link);

		if($("#sidebar li a").length)
		{
			$("#sidebar li a").each(function()
			{
				href_link = $(this).attr("href");
				sections_link = href_link.lastIndexOf("/");
				link_name = href_link.substring(sections_link);

				if(link_name == page_name)
				{
					$(this).parent().addClass("current");
				}
			});

			if(!$("#sidebar li").hasClass("current"))
			{
				$("#sidebar li:first-child").addClass("current");
			}

			$("#sidebar li a").bind("click",function()
			{
				$("#sidebar li").removeClass("current");
				$(this).parent().addClass("current");
				__goToPage($(this).attr("href"));
				return false;
			});
		}

		/** *
		if($("#header").length)
		{
			$("#header").bind("click",function()
			{
				__goToPage("../admin/applications.php");
			});
		}/** */

		if($("#sidebar_logo").length)
		{
			$("#sidebar_logo").bind("click",function()
			{
				__goToPage("../admin/applications.php");
			});
		}
	}

	// --------------------   //////   -------------------------//
	// --------------- SHORTCUTS FUNCTIONS ---------------------//
	// --------------------   //////   -------------------------//
	function active_shortcuts()
	{
		$(window).keydown(function(e)
		{
			var keyboard = (document.all) ? e.keyCode : e.which;
		    if(e.altKey)
		    {
		    	switch (keyboard)
		    	{

		    		case 65: //Alt + m  - Member list
	    			__goToPage("../sections/add_member.php");
	    			break;

		    		case 68: //Alt + d  - Checker tool
		    			__goToPage("../sections/add_assistance.php");
		    			break;

		    		case 70: //Alt + f  - Farebox
		    			__goToPage("../sections/farebox.php");
		    			break;

		    		case 72: //Alt + h  - Admin
		    			__goToPage("../sections/admin.php");
		    			break;

		    		case 73: //Alt + i  - Home
		    			__goToPage("../sections/index.php");
		    			break;

		    		case 77: //Alt + m  - Member list
		    			__goToPage("../sections/member_list.php");
		    			break;



		    		case 81: //Alt + q  - Logout
		    			__goToPage("../sections/logout.php");
		    			break;

		    		case 83: //Alt + s  - Shorcuts
		    			__goToPage("../sections/shortcut_list.php");
		    			break;

		    		case 85: //Alt + u  - User list
		    			__goToPage("../sections/user_list.php");
		    			break;

		    	}
		    }

			/** *
			a = 65 | b = 66 | c = 67 | d = 68
			e = 69 | f = 70 | g = 71 | h = 72
			i = 73 | j = 74 | k = 75 | l = 76
			m = 77 | n = 78 | ñ = 186 | o = 79
			p = 80 | q = 81 | r = 82 | s = 83
			t = 84 | u = 85 | v = 86 | w = 87
			x = 88 | y = 89 | z = 90

			0 = 48 | 1 = 49 | 2 = 50 | 3 = 51
			4 = 52 | 5 = 53 | 6 = 54 | 7 = 55
			8 = 56 | 9 = 57 |
			/** */
		});
		return true;
	}
