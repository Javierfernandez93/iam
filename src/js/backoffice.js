$(document).ready(function(){
	let stats = new Stats;
	let notifications = new Notifications;

  // alertMessage(translate("Hola"));

	// setTimeout(()=>{
		notifications.push("Minado",translate("Se encontró un bloque hash") +": 05bnE391929xp0f","bg-dark");	
	// },1000);	
});

// get_stats_earns


class Stats extends Http {
  constructor()
  {
    super();
  }
  getStatsEarns(callback,data){
    return this.call("../../app/application/get_stats_earns.php",data,callback);
  }
};