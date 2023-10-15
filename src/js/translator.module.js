import { Cookie } from '../../src/js/cookie.module.js?v=1.3.0'

class Translator extends Cookie {
    constructor() {
        super()

        this.ready = null
        this.language = null
        this.loadFromStorage = false
        this.words = []
    }
    async init() {
        this.getLanguage()
        await this.getWords()
    }
    async changeLanguage(language) {
        this.setLanguage(language)
        
        await this.init()
    }
    sanitizeLanguage(language) {
        return language.split('-')[0]
    }
    async getWords() {
        this.words = this.getWordsCookie(this.language)

        if(this.words == null || this.loadFromStorage == false) {
            let response = await fetch(`../../src/languages/${this.language}.json`,{
                method: "GET", 
            })
            
            this.words = await response.json()

            this.setWordsCookie(this.language, this.words)
        } 
    }
    getBrowserLanguage() {
        return navigator.language || navigator.userLanguage 
    }
    getLanguage() {
        this.language = this.getLanguageCookie()

        if(this.language == null)
        {
            this.language = this.sanitizeLanguage(this.getBrowserLanguage())
            this.setLanguage(this.language)
        }

        return this.language
    }
    getWordsCookie(language) {
        let words = this.getCookie(`w_${language}`)

        if(words != null)
        {
            return JSON.parse(words)
        }

        return null
    }
    getLanguageCookie() {
        return this.getCookie('language')
    }
    setWordsCookie(language,words) {
        this.setCookie(`w_${language}`, JSON.stringify(words))
    }
    setLanguageCookie(language) {
        this.setCookie('language', language)
    }
    setLanguage(language) {
        this.language = language
        this.setLanguageCookie(this.language)
    }
}

export { Translator }