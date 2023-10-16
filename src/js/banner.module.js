import { Http } from '../../src/js/http.module.js?v=2.6.5';

class Banner extends Http {
    constructor() {
        super();
        this.banners = null
    }
    //ganners
    getSourceBanner(position) {
        return this.getBanner(position).source
    }
    getLinkBanner(position) {
        return this.getBanner(position).link
    }
    getBanner(position) {
        let banner = null
        
        this.banners.map((_banner) => {
            if(_banner.position === position) {
                banner = _banner 
            }
        })

        return banner
    }
    setBanner(banner) {
        this.banners.push(banner)
    }
    setBanners(banners) {
        this.banners = banners
    }
    getBannersTop(data, callback) {
        return this.call('../../app/application/getBannersTop.php', data, callback);
    }
    getBannersLeft(data, callback) {
        return this.call('../../app/application/getBannersLeft.php', data, callback);
    }
}

export { Banner }