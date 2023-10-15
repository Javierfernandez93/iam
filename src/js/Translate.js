class Translate {
    constructor() {
        super();
    }
    doLogin(data, callback) {
        return this.call('../../app/application/do_login.php', data, callback);
    }
}

export { Translate }