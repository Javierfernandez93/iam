class SpecialAlert extends Http {
    constructor()
    {
        super();
        this.hide = true;
        this.last_element = null;
        this.new_element = null;
    }
    popData()
    {
        $(this.getNewElement()).addClass("sa-pop-element-left").removeClass("sa-push-element");
        $(this.getLastElement()).addClass("sa-push-element-left").removeClass("sa-pop-element");

        setTimeout(()=>{
            $(this.getLastElement()).removeClass("sa-push-element-left sa-pop-element");
            $(this.getNewElement()).removeClass("sa-pop-element-left").remove();

            $(this.getLastElement()).off('click');

        },2000);
    }
    pushData(last_element,new_element)
    {
        this.setLastElement(last_element);
        this.setNewElement(new_element);

        $(this.getLastElement()).addClass("sa-pop-element").click(()=>{
            this.popData();
        });

        $(this.getNewElement()).addClass("sa-push-element").removeClass("d-none");
    }
    getLastElement()
    {
        return this.last_element;
    }
    setLastElement(last_element)
    {
        this.last_element = last_element;
    }
    getNewElement()
    {
        return this.new_element;
    }
    setNewElement(new_element)
    {
        this.new_element = new_element;
    }  
    show()
    {
        this.hide = false;
    }
    hide()
    {
        $(".special-alert-overlay").remove();
        this.hide = true;
    }
    toggle()
    {
        if(this.hide == true)
        {
            this.show();
        } else {
            this.hide();
        }
    }
    getWebService(web_service,callback,data)
    {
        if(web_service == "getQualityInfo")
        {
            this.getQualityInfo(callback,data);
        } else if(web_service == "getPrivateProyectInfo") {
            this.getPrivateProyectInfo(callback,data);
        } else if(web_service == "getPreLoadedIntents") {
            this.getPreLoadedIntents(callback,data);
        } else if(web_service == "getCommentsByTask") {
            this.getCommentsByTask(callback,data);
        } else if(web_service == "getMethodsToShareReferrealLink") {
            this.getMethodsToShareReferrealLink(callback,data);
        } else if(web_service == "getMethodsToShareSheet") {
            this.getMethodsToShareSheet(callback,data);
        } else if(web_service == "getInfoAboutProfit") {
            this.getInfoAboutProfit(callback,data);
        } else if(web_service == "getInfoAboutMargin") {
            this.getInfoAboutMargin(callback,data);
        } else if(web_service == "getMethodsToShareStore") {
            this.getMethodsToShareStore(callback,data);
        } else if(web_service == "addPixel") {
            this.addPixel(callback,data);
        } else if(web_service == "addVar") {
            this.addVar(callback,data);
        }
    }
    getPrivateProyectInfo(callback,data){
        this.call("../../app/application/get_private_proyect_info.php",data,callback);
    }
    getCommentsByTask(callback,data){
        this.call("../../app/application/get_comments_by_task.php",data,callback);
    }
    getMethodsToShareStore(callback,data){
        this.call("../../app/application/get_methods_to_share_store.php",data,callback);
    }
    getInfoAboutMargin(callback,data){
        this.call("../../app/application/get_info_about_margin.php",data,callback);
    }
    getInfoAboutProfit(callback,data){
        this.call("../../app/application/get_info_about_profit.php",data,callback);
    }
    getMethodsToShareSheet(callback,data){
        this.call("../../app/application/get_methods_to_share_sheet.php",data,callback);
    }
    getMethodsToShareReferrealLink(callback,data){
        this.call("../../app/application/get_methods_to_share_referreal_link.php",data,callback);
    }
    getQualityInfo(callback,data){
        this.call("../../app/application/get_quality_info.php",data,callback);
    }
    getPreLoadedIntents(callback,data){
        this.call("../../app/application/get_preloaded_intents.php",data,callback);
    } 
    addVar(callback,data){
        this.call("../../app/application/add_var.php",data,callback);
    }
    addPixel(callback,data){
        this.call("../../app/application/add_pixel.php",data,callback);
    }
}

let specialAlert = new SpecialAlert;

window.popData = function() {
    specialAlert.popData();
}

function _showLeftAlertWS(web_service,callback,data,element,dinamic_loader_class)
{
    if(element != undefined) {
        if(dinamic_loader_class != undefined)
        {
            dinamicLoader.show(element,dinamic_loader_class);
        } else {
            dinamicLoader.show(element);
        }
    }

    specialAlert.getWebService(web_service,(response)=>{
        if(element != undefined) {
            dinamicLoader.closeLoader();
        }

        if(response.s == 1)
        {
            _showLeftAlert(response.html,response.title,response.footer);

            if(callback != undefined) callback();
        }
    },data);
}

window.showLeftAlertWS = function(web_service,callback,data,element,dinamic_loader_class)
{
    _showLeftAlertWS(web_service,callback,data,element,dinamic_loader_class);
}

window.showLeftAlert = function(message,title,footer)
{
    _showLeftAlert(message,title,footer);
}

function deleteAllSpecialAlerts()
{
    if($(".special-alert-overlay").lenght)
    {
        $(".special-alert-overlay").remove();
    }
}

function _showLeftAlert(message,title,footer)
{
    deleteAllSpecialAlerts();

    let overlay = $("<div/>").addClass("special-alert-overlay d-flex justify-content-end");
    let card = $("<div/>").addClass("special-alert-content h-100 col-12 col-xl-3 col-md-6 card");
    let card_header = $("<div/>").addClass("card-header card-header-flush");
    let card_header_button = $("<div/>").addClass("d-flex justify-content-end");
    let button = $("<button/>").addClass("btn btn-link").html('<i class="fas fa-times"></i>').click(()=>{
        $(".special-alert-overlay").remove();
    });
    let card_header_title = $("<div/>").addClass("card-header border-none");
    let card_header_title_content = $("<div/>").addClass("header-title display-4 mt-3 text-primary").html(title);
    let card_body = $("<div/>").addClass("card-body card-body-special-alert lead mt-3").html(message);
    let card_footer;

    if (footer != undefined) {
        card_footer = $("<div/>").addClass("card-footer").html(footer);
    }

    card_header_title.append(card_header_title_content);
    card_header_button.append(button);
    card_header.append(card_header_button);
    card.append(card_header,card_header_title,card_body,card_footer);
    overlay.append(card);
    $("body").append(overlay);
}