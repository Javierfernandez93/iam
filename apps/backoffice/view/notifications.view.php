<div class="container-fluid py-4" id="app">
    <div v-if="notifications.length > 0">
        <div 
            class="card mb-3">
            <div class="card-body">
                <div class="timeline timeline-one-side">
                    <div 
                        v-for="notification in notifications"
                        class="timeline-block mb-3">
                        <div class="align-items-center">
                            <span class="timeline-step">
                                <i v-html="notification.extra"></i>
                            </span>
                            <div class="timeline-content">
                                <div class="fw-semibold text-dark small">{{notification.kind}} <span class="badge bg-light text-secondary small">{{notification.create_date}}</span></div>
                                <div>{{notification.message}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="alert alert-secondary text-center text-white">
            No tienes notificaciones aún. Vuelve más tarde
        </div>
    </div>
</div>