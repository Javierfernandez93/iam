import { Http } from './http.module.js?v=2.6.6';

class Guest extends Http {
    constructor() {
        super();
    }
    getVcardQR(data, callback) {
        return this.call(getMainPath() + '/app/application/getVcardQR.php', data, callback);
    }
    getVcardShareMethods(data, callback) {
        return this.call(getMainPath() + './app/application/getVcardShareMethods.php', data, callback);
    }
    getVcardRoute(data, callback) {
        return this.call(getMainPath() + './app/application/getVcardRoute.php', data, callback);
    }
    getSocialsFromVcard(data, callback) {
        return this.call(getMainPath() + './app/application/getSocialsFromVcard.php', data, callback);
    }
    getVCardByRoute(data, callback) {
        return this.call(getMainPath() + './app/application/getVCardByRoute.php', data, callback);
    }
    getVCardById(data, callback) {
        return this.call(getMainPath() + './app/application/getVCardById.php', data, callback);
    }
    getVCardById(data, callback) {
        return this.call(getMainPath() + './app/application/getVCardById.php', data, callback);
    }
    getCountries(data, callback) {
        return this.call(getMainPath() + '/app/application/get_countries.php', data, callback);
    }
    doSignup(data, callback) {
        return this.call(getMainPath() + '/app/application/do_signup.php', data, callback);
    }
    getReferralData(data, callback) {
        return this.call(getMainPath() + '/app/application/getReferralData.php', data, callback);
    }
    addVisitPerLanding(data, callback) {
        return this.call(getMainPath() + '/app/application/addVisitPerLanding.php', data, callback);
    }
    getLandingByData(data, callback) {
        return this.call(getMainPath() + '/app/application/getLandingByData.php', data, callback);
    }
}

export { Guest }