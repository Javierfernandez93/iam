class Countdown {
    constructor(date_future) {
        this.interval = null;
        this.date_now = null;
        this.date_future = date_future;
    }
    start() {
        this.interval = setInterval(() => {
            var delta = Math.abs(this.date_future - Date.now()) / 1000;
            var days = Math.floor(delta / 86400);
            delta -= days * 86400;
            var hours = Math.floor(delta / 3600) % 24;
            delta -= hours * 3600;
            var minutes = Math.floor(delta / 60) % 60;
            delta -= minutes * 60;
            var seconds = delta % 60;
            document.getElementById('days').innerHTML = days;
            document.getElementById('hours').innerHTML = hours;
            document.getElementById('minutes').innerHTML = minutes;
            document.getElementById('seconds').innerHTML = parseInt(seconds);
        }, 1000);
    }
};

let countdown = new Countdown(new Date('2022-10-31T19:00:00').valueOf());

countdown.start();