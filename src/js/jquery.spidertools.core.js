/* <<<<<<<<<<<<<<<<< ---------------- JS SCRIPTS ------------------ >>>>>>>>>>>>>>>>>>>>>>>>>>>> */
var storage;
var page_name = __getPageName();
__loadDictionaryInStorage({"reload":true});

try
{
    if (localStorage.getItem)
    {
        storage = localStorage;
    }
} catch(e) {
    storage = {};
}

function set_user_name(user_name)
{
	$("body #landing_user_name").html(user_name);
}

function set_mail(mail)
{
	$("body #landing_user_mail").html(mail);
}

function __showMessage(params)
{
	wow = new WOW(
        {
          boxClass:     'wow',      // default
          animateClass: 'animated', // default
          offset:       0,          // default
          mobile:       true,       // default
          live:         true        // default
        }
    );
	wow.init();
	__closeMessage();
	__closeLoader();
	var defaults = {
    	"message": "",
    	"level": false,
    	"style": false,
    	"actionMessageKey": true,
    	"options":  { "Ok": function(){ __closeMessage(); } },
    };

	if(!__isArray(params))
	{
		var message = params;
			params = {"message": message};
	}

	var params = $.extend(defaults, params);

	__addOverlayMessage();

	var i = 1;
	for(var x in params.options)
	{
		var btn = $("#__overlayAction_" + i);
		var button_text = __Translate(x);
			btn.html(button_text);

		(function(actions){
			btn.unbind("click");
			btn.bind("click", actions);
		})(params.options[x]);

		btn.css("display","inline-block");
		i++;
	}

	$("#__close").unbind("click");
	$("#__close").click(function(){
		__closeMessage();
	});

	$("#__overlayContent").html(params.message);
	$("#__overlayAction").show();

	__actionMessageKey(params.actionMessageKey);

	if(params.style)
		$("#__overlay").attr("style",params.style);

	var topPos = ($(window).height() - $('#__overlay').outerHeight())/2+ 100;

	$('.__overlay').css({
		top: topPos
	});
	$('.__overlay').addClass('wow bounceInDown');
	$('#__overlay').css({
		top: topPos
	});
}

function __showLoader(options)
{
	__closeLoader();
	if (typeof options !== "object" || options === null)
	{
		var message = options;
			options = {"message": message};
	}

	var defaults = {
    	"loader": true,
    	"message": __Translate("Loading..."),
    	"image_path": "../../src/img/status.gif"
    };

   	var options = $.extend(defaults, options);

   	var image_loader    = "<img class='__loader' src='" + options.image_path + "' />";
   	var loader_content  = (options.loader == true)? image_loader : options.message ;
		loader_content += (options.message !== null && options.message !== "null") ? "<br />" + options.message : "";

	__addOverlayMessage();

	$("#__overlayContent").html(loader_content);
	$('.__overlay').addClass('wow bounceInDown');
	return true;
}

/**
 * This function close the message
 * hidde all divs in the message
 * return void
 */
function __closeMessage()
{
	// $("#__overlayShadow").fadeOut(350,function(){
		$('.__overlayShadow').remove();
	// });
	// $("#__overlay").fadeOut(350,function(){
		$('.__overlay').remove();
	// });
	return true;
}

/**
 * This function close the message loader
 * hidde all divs in the message loader
 * return __closeMessage
 */
function __closeLoader()
{
	return __closeMessage();
}

/**
 * To detect which key has been pressed to execute
 * the corresponding action in the message
 * return void
 */
function __actionMessageKey(options)
{
	$("#__overlayAction_1").focus();
	$(document).keydown(function(e) {
	    if(e.keyCode == 27)
	    {
	    	__closeMessage();
	    }

	    if(e.keyCode == 13)
	    {
	    	if(options)
	    	{
	    		$("#__overlayAction_1").click();
	    		return false;
	    	} else {
	    		__closeMessage();
	    		return false;
	    	}
	    }
	});
}

function __addOverlayMessage()
{
	var margin_top = document.body.scrollTop - 120;
	var margin_left = document.body.scrollLeft - 150;

	var overlayShadow 	= $.__createDiv({"id":"__overlayShadow", "className":"__overlayShadow"});
	// var close 			= $.__createImage ({"id":"__close", "className":"__close", "src":"../../spiderframe/css/spider/images/close.png"});
	var message 		= $.__createDiv ({"id":"__overlay", "className":"__overlay"});
	var messageClear 	= $.__createDiv ({"id":"__overlayClear"});
	var messageContent	= $.__createDiv ({"id":"__overlayContent"});
	var messageAction 	= $.__createUl ({"id":"__overlayAction", "className":"__overlayAction"});
	var messageAction_1 = $.__createLi ({"id":"__overlayAction_1","content":"Yes"});
	var messageAction_2 = $.__createLi ({"id":"__overlayAction_2","content":"No"});
	var messageAction_3 = $.__createLi ({"id":"__overlayAction_3","content":"Cancel"});

	messageAction.append(messageAction_1, messageAction_2, messageAction_3);
	message.append(messageClear,messageContent,messageAction);

	$("body").append(overlayShadow,message);/* **/

	$("#__overlay").css("margin-top", margin_top+"px");
	$("#__overlay").css("margin-left", margin_left+"px");
	$("#__overlayShadow").css("width", "100%");
	$("#__overlayShadow").show();
	$("#__overlay").fadeIn();

	var topPos = ($(window).height() - $('#__overlay').outerHeight())/2+ 100;

	// alert(topPos)
	$('#__overlay').css({
		top: topPos
	});

	$('.__overlay').css({
		top: topPos
	});

	$('#__overlay').css({
		top: topPos
	});
	//effects 'blind','bounce','clip','drop','explode','fold','highlight','pulsate','scale','shake','slide','puff'
}

/**
 * This function check to valid e-mail
 * @param $str string to check
 * @return boolean
 *
 */
function __isValidMail(mail)
{
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(mail);
}

/**
 * This function redirect to url
 * @param $url string to address
 * @return boolean
 *
 */
function __goToPage(url)
{
	$("body").fadeOut(10000000000,__go(url));
}

function __go(url)
{
	window.location.href = url;
}

/**
 * This function check is valid array
 * @param obj to eval
 * @return boolean
 *
 */
function __isArray(obj)
{
	if(typeof obj !== "object")
	{
    	return false;
	}

	return true;
}

function __getJSONRequest(options)
{
	var defaults = {
    	"url": null,
    	"token": null,
    	"async": false,
    	"loader": false,
    	"image_path": null,
    	"method": "POST",
    	"message": null,
    	"dataType": "json",
    	"crossDomain": true,
    	"data": {"token": null},
    };

   	var options = $.extend(defaults, options);

	if(options.loader)
	{
		console.log("a")
		__showLoader(options.options);
	}

   	(options.data.token == null)? options.data.token = options.token : options.data.token ;

 	var fileRequest = $.ajax({
		url: options.url,
		data: options.data,
		async: options.async,
		type: options.method,
		dataType : options.dataType,
		crossDomain: options.crossDomain,
	}).responseText;

	var returnData = JSON.parse(fileRequest);

	if(options.loader)
		__closeLoader();

	return returnData;
}

/*  deprectated version */
function __getJSONRequestAsync(options, callback)
{
	var defaults = {
    	"url": null,
    	"token": null,
    	"loader": true,
    	"message": null,
    	"image_path": null,
		"method": "POST",
    	"dataType": "json",
    	"crossDomain": true,
    	"data": {"token": null},
    };

   	var options = $.extend(defaults, options);

   	(options.data.token == null)? options.data.token = options.token : options.data.token ;

   	// Si loader true, async necesario tambien en true
   	// options.async = (options.loader == true)? true : options.async ;

	$.ajax({
		async: true,
		url: options.url,
		data: options.data,
		type: options.method,
		dataType : options.dataType,
		crossDomain: options.crossDomain,
		beforeSend: function(){
			if(options.loader === true)
			{
				__showLoader(options.options);
			}
		},
		complete:function(data){
			if(options.loader === true)
			{
				__closeLoader();
			}
			callback(data.responseJSON);
		}
	});
}

function __getJSONRequestNAsync(options, callback)
{
	var method = "GET"
	if(options.method != undefined) method = options.method;
	var defaults = {
		"async" : false,
    	"url" : null,
    	"loader" : false,
    	"timeOut" : 0,
		"method" : method,
    	"dataType" : "json",
    	"crossDomain" : true,
    	"loadOldObject" : true,
    	"data": {},
    };

   	var options = $.extend(defaults, options);
   		(options.data.token == null)? options.data.token = options.token : options.data.token ;

	if(options.loader && !options.async)
	{
		options.loader.oldText = options.loader.object.html();
		options.loader.object.html(options.loader.message);
	}

	setTimeout(function()
	{
		$.ajax({
			async : options.async,
			url : options.url,
			data : options.data,
			type : options.method,
			dataType : options.dataType,
			crossDomain : options.crossDomain,
			beforeSend : function()
			{
				if(options.loader && options.async)
					options.loader.object.html(options.loader.message);
			},
			complete : function(data)
			{
				if(options.loader && !options.async && options.loadOldObject)
					options.loader.object.html(options.loader.oldText);

				callback(data.responseJSON);
			}
		});

	},options.timeOut);

}

function __saveRow(options)
{
	var defaults = {
				"token": null,
				"path_api": "../../",
    	    	"url": "spiderframe/application/save_row.php",
    	    };

   	var options = $.extend({}, defaults, options);

	return __getJSONRequest({"url": options.path_api + options.url, "data": options.data, "token": options.token});
}

function __sendRequest(options)
{
	return __getJSONRequest(options);
}

function __getUrlGet(getParam)
{
 	var href = $(location).attr("href");
 	var n = href.lastIndexOf("?");
	var hash = (href.lastIndexOf("#") > 1 ) ? href.lastIndexOf("#") : href.length ;
	var sURLVars = href.substring(n+1,hash);
	var sURLGet = sURLVars.split("&");

    for (var i = 0; i < sURLVars.length; i++)
    {
        var sParameterName = sURLGet[i].split("=");
        if (sParameterName[0] == getParam)
        {
            return sParameterName[1];
        }
    }
}

function __getPageName()
{
	var href = $(location).attr("href");
 	var slash = href.lastIndexOf("/");
 	var dot = href.indexOf(".");
 	var page = href.substring(slash+1, dot);
 	return page;
}

function __getFormatNumber(params)
{
	var number = (params.number) ? params.number : 0 ;
	var symbol = (params.symbol) ? params.symbol : "$" ;
	var format = (params.format) ? params.format : false ;

	switch (format){
       	case false:
         	return number;
       	break;
       	case "english":
       		return  (symbol) ? symbol + "" + __numberFormat(number, 2) : __numberFormat(number, 2);
       	break;

       	case "english_without_thousands_separator":
       	 	return  (symbol) ? symbol + " " + __numberFormat(number, 2, '.', '') : __numberFormat(number, 2, '.', '');
       	break;
	   	case "french":
         	return __numberFormat(number, 2, ',', ' ');
		break;

	}
}

function __numberFormat(number, decimal, decimal_separator, thousand_separator)
{
    number=parseFloat(number);

    if(isNaN(number))
    {
        return "";
    }

	decimal = (decimal) ? decimal : 2 ;
	number = number.toFixed(decimal) + '';
    x = number.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';

    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1))
    {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function mktime(hour, minute, second, month, day, year)
{
    return new Date(year, month - 1, day, hour, minute, second, 0).getTime() / 1000;
}



/****** -------------  Tranlate functions ------------- **********/
/**
 * __loadDictionaryInStorage function
 * @param page string is the page section to load for tranlate
 * @returns storage.dictionary load dictionary section and common in localStorage
 */
function __loadDictionaryInStorage(options)
{

	if (typeof options !== "object" || options === null)
	{
		var options = (options != undefined)? options : "spanish";
			options = {"language": options};
	}

	var defaults = {
    	    	"loader": true,
    	    	"language": "spanish",
    	    	"reload": false,
    	    	"message": "Cargando diccionarios...",
    	    	"image_path": "../../src/img/status.gif"
    };

   	var options = $.extend(defaults, options);

	if(!localStorage.dictionary || options.reload)
	{
		var dictionary = __getJSONRequest({"url": "../../subcore/languages/" + options.language + ".json"});
			localStorage.dictionary = JSON.stringify(dictionary);

		__setCookie("dictionary_loaded", true);
		return true;
	}


	return true;
}

function __T(word, section)
{
	return __translate(word, section);
}

function __Translate(word, section)
{
	return __translate(word, section);
}

function __t(word, section)
{
	return __translate(word, section);
}

/** */
// NEW VERSION
function __translate(word, section)
{
	if(!localStorage.dictionary)
	{
		__loadDictionaryInStorage();
	}

	if(word)
	{
		var first_word = null;
		var second_word = null;

		var local_dictionary = JSON.parse(storage.dictionary);


		if(__isArray(local_dictionary))
		{
			$.each(local_dictionary[0], function(dictionary_section, section_content) {

				$.each(section_content, function(system_value, translate_value) {

					if(word === system_value)
					{
					 	second_word = (section == dictionary_section) ? translate_value : null;
						first_word  = (first_word == null) ? translate_value : first_word;
					}

				});
			});
		}

		if(second_word != null || second_word != undefined)
		{
			word = second_word;
		} else if(first_word != null || first_word != undefined) {
			word = first_word;
		}
	}


	return word;
}


/** */
/** *
// old Version
function __translate(word, section)
{
	if( !__getCookie("dictionary_loaded") )
	{
		__loadDictionaryInStorage();
	}

	if(word)
	{
		var first_word = null;
		var second_word = null;

		var local_dictionary = JSON.parse(storage.dictionary);

		if(__isArray(local_dictionary))
		{
			$.each(local_dictionary, function(dictionary_section, line) {

				$.each(line, function(key, values) {
					 if(values["system_value"] == word)
					 {
					 	second_word = (section == dictionary_section) ? values["translate_value"] : null;
					 	first_word  = (first_word == null) ? values["translate_value"] : first_word;
					 }
				});
			});
		}

		if(second_word != null || second_word != undefined)
		{
			word = second_word;
		} else if(first_word != null || first_word != undefined) {
			word = first_word;
		}
	}

	return word;
}
/** */
function __focus(id)
{
	if(id == "first")
	{
		$("input:text:visible:first").focus();
	} else {
		$("#" + id).focus();
	}

	return true;
}

function upload_file_by_ajax(options)
{
	var defaults = {
	    	"files": null,
	    	"token": null,
	    	"cache": false,
	    	"loader": true,
	    	"method": "POST",
	    	"thumbnail": false,
	    	"contentType": false,
	    	"processData": false,
	    	"upload_path": null,
	    	"thumbnail_path": null,
	    	"url": "../../subcore/application/upload_file.php",
	    };

   	var data = new FormData();
	var options = $.extend(defaults, options);

	$.each(options, function(key, value)
	{
		if( (key != "files") || (key != "cache") || (key != "loader") || (key != "method") || (key != "contentType") || (key != "processData") || (key != "url"))
		{
			data.append(key, value);
		}
	});

	for(i=0; i<options.files.length; i++)
	{
		data.append("upload_file_" + i, options.files[i]);

	}

	var fileContent = $.ajax({
		data: data,
		url: options.url,
		async:false,
		type: options.method,
		cache: options.cache,
		contentType: options.contentType,
		processData: options.processData
	}).responseText;


	var returnData = JSON.parse(fileContent);


	return returnData;
}
function upload_file_campaign_by_ajax(options,callback)
{
	var defaults = {
    	"files": null,
    	"token": null,
    	"cache": false,
    	"loader": true,
    	"method": "POST",
    	"thumbnail": false,
    	"contentType": false,
    	"processData": false,
    	"upload_path": null,
    	"thumbnail_path": null,
    	"url": "../../apps/backoffice/subcore/application/upload_campaing_file.php",
    };

   	var data = new FormData();

   	if(options != undefined) var options = $.extend(defaults, options);

   	console.log(options)

	$.each(options, function(key, value)
	{
		if( (key != "files") || (key != "cache") || (key != "loader") || (key != "method") || (key != "contentType") || (key != "processData") || (key != "url"))
		{
			data.append(key, value);
		}
	});

	for(i=0; i<options.files.length; i++)
		data.append("upload_file_" + i, options.files[i]);

	var fileContent = $.ajax({
		data: data,
		url: options.url,
		async:false,
		type: options.method,
		cache: options.cache,
		contentType: options.contentType,
		processData: options.processData
	}).responseText;


	var returnData = JSON.parse(fileContent);

	callback(returnData);
}

function upload_file_by_XMLHttpRequest(options)
{
	var defaults = {
	    	"files": null,
	    	"token": null,
	    	"cache": false,
	    	"loader": true,
	    	"method": "POST",
	    	"thumbnail": false,
	    	"contentType": false,
	    	"processData": false,
	    	"upload_path": null,
	    	"thumbnail_path": null,
	    	"url": "../../spiderframe/application/upload_file.php",
	    };

   	var data = new FormData();
	var options = $.extend(defaults, options);

	$.each(options, function(key, value)
	{
		if( (key != "files") || (key != "cache") || (key != "loader") || (key != "method") || (key != "contentType") || (key != "processData") || (key != "url"))
		{
			data.append(key, value);
		}
	});

	for(i=0; i<options.files.length; i++)
	{
		data.append("upload_file_" + i, options.files[i]);
	}

	if(window.XMLHttpRequest)
	 {
	 	var returnData = new XMLHttpRequest();
	 } else if(window.ActiveXObject) {
	 	var returnData = new ActiveXObject("Microsoft.XMLHTTP");
	 }

	returnData.open(options.method, options.url, true);

	returnData.onload = function(event) {
		if (returnData.status == 200) {
		  //var msg = returnData.responseText;
		  //$("#succes_upload_file").html(msg);
		  return JSON.parse(returnData.responseText);
		} else {
		  return returnData.status;
		}
	};

	returnData.send(data);

	return false;
}


function __setCookie(cookie, value, expire_days)
{
    var days = new Date();
    var expires = "";

    if(expire_days)
    {
    	days.setTime(days.getTime() + (expire_days * 24 * 60 * 60 * 1000));
    	var expire_date = "expires=" + days.toGMTString();
    }

    document.cookie = cookie + "=" + value + "; " + expire_date;
}

function __getCookie(cookie)
{
    var name = cookie + "=";
    var cookies = document.cookie.split(';');

    for(var i=0; i<cookies.length; i++)
    {
        var current_cookie = cookies[i];
        while (current_cookie.charAt(0)==' ') current_cookie = current_cookie.substring(1);
        if (current_cookie.indexOf(name) != -1)
        {
        	return current_cookie.substring(name.length, current_cookie.length);
        }
    }

    return null;
}

function __addOverLayShadow()
{
	var overlayShadow 	= $.__createDiv({"id":"__overlayShadow", "className":"__overlayShadow"});
	$('body').append(overlayShadow);
	$("#__overlayShadow").css("width", "100%");
	$("#__overlayShadow").show();
}

function __removeOverLayShadow()
{
	$("#__overlayShadow").hide();
}

function __deleteCookie(cookie)
{
	document.cookie = cookie + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
}

function __timeConverter(options)
{
	var defaults = {
		"with_time" : true,
		"date" : null
	};

	var options = $.extend(defaults, options);

	if(options.date != "")
	{
		var date = new Date(options["date"]*1000);
		var year = date.getFullYear();
		var month= date.getMonth() + 1;
		var day  = date.getDate();

		var	time = day + "-" + month + "-" + year;

		if(options["with_time"])
		{
			var hour = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours());
			var min  = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes());
			var sec  = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
				time += " " + hour + ":" + min + ":" + sec;
		}

		return time;
	}

	return __Translate("Not date");
}