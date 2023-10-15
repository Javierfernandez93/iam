class NotificationService {
    hasNotificationService = null
    hasPermissionGranted = false
    constructor() {
    }
    init() {
        return new Promise((resolve, reject) => {
            this.hasNotificationService = window.Notification

            if(this.hasNotificationService)
            {
                resolve()
            } else {
                reject()
            }
        })
    }
    push({text,img})
    {
        var notification = new Notification('Lista de tareas', { body: text, icon: img });
    }
    requestPermission()
    {
        return new Promise((resolve, reject) => {
            if (Notification) {
                Notification.requestPermission().then((result) => {
                    if(result == 'granted')
                    {
                        this.hasPermissionGranted = true
                        resolve()
                    } else {
                        this.hasPermissionGranted = false

                        reject()
                    }
                });
            } else {
                reject()
            }
        })
    }
}

export { NotificationService }