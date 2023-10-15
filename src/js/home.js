$(document).ready(function(){
    let map;

    async function initMap() {
      const { Map } = await google.maps.importLibrary("maps");
    
      const position = { lat: 20.6971379, lng: -103.3933895 }
      console.log()

      map = new Map(document.getElementById("map"), {
        center: position,
        zoom: 14,
        styles: [{"featureType":"all","elementType":"labels.text","stylers":[{"color":"#878787"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f9f5ed"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"color":"#f5f5f5"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#aee0f4"}]}]

      });

      new google.maps.Marker({
        position: position,
        map,
        title: "Oficinas de IAM",
      });
      
    }
    
    initMap();
})