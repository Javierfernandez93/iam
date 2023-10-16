import { UserSupport } from '../../src/js/userSupport.module.js?t=1.1.4'   

const HomeViewer = {
    name : 'home-viewer',
    data() {
        return {
            UserSupport: new UserSupport
        }
    },
    methods : {
        getSystemVar(name)
        {
            return new Promise((resolve)=>{
                this.UserSupport.getSystemVar({name:name},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.var)
                    }
                })
            })
        },
        async initMap(position)
        {
            let map;

            const { Map } = await google.maps.importLibrary("maps");
    
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
    },
    async  mounted() 
    {      
        this.getSystemVar('company_address_latitude').then(async (val)=>{
            await this.initMap(val) 
        })
    },
    template : `
    <div id="map"></div>
    `
}

export { HomeViewer }