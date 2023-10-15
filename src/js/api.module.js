import { Http } from "../../src/js/http.module.js"

const MAIN_PATH = getMainPathZuum()
const METHOD = 'GET'

class Api extends Http
{
    constructor({api_key, api_secret})
    {
        super()
        this.api_secret = api_secret != null ? api_secret : null
        this.api_key = api_key != null ? api_key : null
    }
    setCredentials({api_secret,api_key}) {
        this.api_secret = api_secret
        this.api_key = api_key
    }
    mergeApiData(data) {
        return {
            ...data, 
            ...{
                api_secret:this.api_secret,
                api_key:this.api_key,
            }
        }
    }
    getProyects(data, callback) {
        return this.call(MAIN_PATH + '/app/services/getProyects.php', this.mergeApiData(data), callback, null, null, METHOD)
    }
    getSheets(data, callback) {
        return this.call(MAIN_PATH + '/app/services/getSheets.php', this.mergeApiData(data), callback, null, null, METHOD)
    }
    getShortLinks(data, callback) {
        return this.call(MAIN_PATH + '/app/services/getShortLinks.php', this.mergeApiData(data), callback, null, null, METHOD)
    }
    importPackage(data, callback) {
        return this.call(MAIN_PATH + '/app/services/importPackage.php', this.mergeApiData(data), callback, null, null, METHOD)
    }
    getPackagesList(data, callback) {
        return this.call(MAIN_PATH + '/app/services/getPackagesList.php', this.mergeApiData(data), callback, null, null, METHOD)
    }
}

export { Api }