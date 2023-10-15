import { User } from '../../src/js/user.module.js?v=1.4.6';

class Player extends User {
    constructor() {
        super();
        this.id = null
        this.playerId = null
        this.userName = null
        this.connected = false
        this.ACTION = null
    }
    isConnected()
    {
        return this.connected
    }
    setConnected(connected)
    {
        this.connected = connected
    }
    setId(id)
    {
        this.id = id
    }
    setUserName(userName)
    {
        this.userName = userName
    }
    setPlayerId(playerId)
    {
        this.playerId = playerId
    }
    getUserName()
    {
        return this.userName
    }
    getId()
    {
        return this.id
    }
    getPlayerId()
    {
        return this.playerId
    }
}

export { Player }