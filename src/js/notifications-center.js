$(document).ready(function(){
	let notificationCenter = new NotificationCenter;

  dinamicLoader.show($(".response"));

  notificationCenter.getNotificationCenter((response)=>{
    dinamicLoader.close();

    if(response.s == 1)
    {
      $(".response").html(response.html);
    }
  },{id:getParam("id")});
});

class NotificationCenter extends Http {
  constructor()
  {
    super();
  }
  getNotificationCenter(callback,data){
    return this.call("../../app/application/get_notification_center.php",data,callback);
  }
};