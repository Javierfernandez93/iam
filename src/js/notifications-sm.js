$(document).ready(function(){
	let notificationSm = new NotificationSm;

  notificationSm.__getNotificationCenterSm();
  notificationSm.getNotificationCenterSm();
});

class NotificationSm extends Http {
  static UNREAD = 0;
  static TIME_INTERVAL = 1000*35;
  constructor()
  {
    super();
    this._notificationSm = new Notifications;
  }
  __getNotificationCenterSm(){
    this._getNotificationCenterSm((response)=>{
      if(response.s == 1)
      {
        $(".box-response-notification-center-sm").html(response.html);

        if(response.notification_per_users != undefined)
        {
          response.notification_per_users.forEach((notification_per_user)=>{
            
            if(notification_per_user.see == NotificationSm.UNREAD)
            {
              this._notificationSm.push(notification_per_user.kind,notification_per_user.message,notification_per_user.link);  
            }
          })
        }
      }
    });
  }
  getNotificationCenterSm(){
    setInterval(()=>{
      this.__getNotificationCenterSm();  
    },NotificationSm.TIME_INTERVAL);
  }
  _getNotificationCenterSm(callback,data){
    return this.call("../../app/application/get_notification_center_sm.php",data,callback);
  }
};