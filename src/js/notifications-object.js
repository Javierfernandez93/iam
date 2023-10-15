class Notifications extends Http {
  constructor()
  {
    super();
  }
  makeContainer(){
  	if(!$(".special-notification-overlay").length)
  	{
	  	let div_main = $("<div/>").addClass("special-notification-overlay d-flex justify-content-end");
	  	let div_row = $("<div/>").addClass("special-notification-content h-100 col-12 py-3 col-xl-3 col-md-6");
	  
	  	div_main.append(div_row);
	  	
	  	$("body").append(div_main);
  	}
  }
  addNotificationTopbar(title,message,link)
  {
    let id = getUniqueId();
    let a = $("<a />").addClass("text-reset notification-item").attr("id",id);
    let media = $("<div />").addClass("media");
    let avatar = $("<div />").addClass("avatar-xs me-3");
    let avatar_span = $("<span />").addClass("avatar-title bg-success rounded-circle font-size-16");
    let avatar_i = $("<i />").addClass("mdi mdi-check text-white");
    let h3 = $("<h3 />").addClass("mt-0 mb-1").text(title);
    let content_container = $("<div />").addClass("font-size-13 text-muted");
    
    let content = $("<p />").addClass("mb-1").text(message);
    
    let time_container = $("<p />").addClass("mb-0");
    let i = $("<i />").addClass("mdi mdi-clock-outline");

    time_container.append(i).append(" Just now");
    content_container.append(content,time_container);

    avatar_span.append(avatar_i);
    avatar.append(avatar_span);
    
    media.append(avatar,content_container);
    a.append(media);

    $(".navbar-header .simplebar-content").append(a);
  }
  addRow(title,content,link)
  {
    let id = getUniqueId();
  	let card = $("<div />").addClass("card card-notification-list").attr("id",id);
    let card_body = $("<div />").addClass("card-body p-2");
  	  	
  	let card_header = $("<div/>").addClass("card-header card-header-flush d-flex justify-content-between align-items-center");
    
    let card_header_flex = $("<div/>").addClass("d-flex justify-content-between align-items-center");
    
    let card_header_left = $("<div/>");
    let card_header_title = $("<div/>").addClass("card-header-title text-xdark");
    let card_header_title_top = $("<div/>").html('<i class="far fa-bell"></i> '+title);
    let card_header_title_bottom = $("<small/>").addClass("text-primary").html('- Justo ahora');
    card_header_title.append(card_header_title_top,card_header_title_bottom);
    
    let card_header_right = $("<div/>");
  	let button = $("<button/>").addClass("btn btn-outline-danger btn-sm").html('<i class="fas fa-times"></i>').click(function(){
  		$("#"+id).remove();
      if($(".card-notification-list").length == 0)
      {
        $(".special-notification-overlay").remove();
      }
    });
    
    let card_content = $("<div/>").addClass("card-body text-muted").html(content);
  	
    let card_footer = $("<div/>").addClass("card-body p-0");

    let button_button = null;
    if(link)
    {
      button_button = $("<button/>").addClass("btn btn-link btn-sm btn-see-more btn-block").html('<i class="fas fa-chevron-down"></i>').click(function(){
          location.href = link;
      });
    }

    if(link)
    {
      card_footer.append(button_button);

    }

    card_header_left.append(card_header_title);
    card_header_right.append(button);

    card_header.append(card_header_left,card_header_right);

    card_header_right.append(button);
    card_body.append(card_header,card_content,card_footer);
  	card.append(card_body);

  	$(".special-notification-content").append(card);
  }
  hideContainer(){
  }
  showContainer(){
  	if($(".box-notification").length > 0)
  	{
  		$(".box-notification").addClass("box-notification-show");
  	}
  }
  push(title,content,link){
  	this.makeContainer();
  	this.addRow(title,content,link);
  	this.showContainer();
    
    this.addNotificationTopbar(title,content,link);
  }
  getNewNotifications(callback,data){
    return this.call("../../app/application/get_new_notifications.php",data,callback);
  }
};