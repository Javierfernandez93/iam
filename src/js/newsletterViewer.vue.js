import { User } from "../../src/js/user.module.js?v=2.6.4";

String.prototype.isValidMail = function () {
  var pattern = new RegExp(
    /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i
  );
  return pattern.test(this);
};

const NewsletterViewer = {
  name: "newsletter-viewer",
  props: ["text"],
  data() {
    return {
      User: new User(),
      email: "",
    };
  },
  methods: {
    saveSuscriber() {
      this.User.saveSuscriber({email:this.email}, (response) => {
        if (response.s == 1) {
          alertInfo({
            icon:'<i class="bi bi-ui-checks"></i>',
            message: `
              <div class="py-5">
                <div class="lead fw-bold pb-3">Gracias ahora eres parte de nuestro NewsLetter</div>
                <div class="lead">Regístrate hoy en <a class="text-white text-decoration-underline fw-sembold" href="../../apps/signup">Crea tu cuenta</a></div>
              </div>
            `,
            _class:'bg-gradient-success text-white'
          })
        } else if (response.r == 'SUSCRIBER_EXIST') {
          alertInfo({
            icon:'<i class="bi bi-ui-checks"></i>',
            message: `
              <div class="py-5">
                <div class="lead fw-bold pb-3 text-white">Ya eras parte de I AM</div>
                <div class="lead text-white">Regístrate hoy en <a class="text-white text-decoration-underline fw-sembold" href="../../apps/signup">Crea tu cuenta</a></div>
                <div class="lead text-white">o accede a tu cuenta en <a class="text-white text-decoration-underline fw-sembold" href="../../apps/login">Iniciar sesión</a></div>
              </div>
            `,
            _class:'bg-gradient-success text-white'
          })
        }
      });
    },
  },
  mounted() {
    
  },
  template: `
        <section class="container py-6">
            <div class="row justify-content-center py-6">
                <div class="col-12 col-xl-8">
                    <div class="fs-2 Space-Grotesk pb-3 fw-bold text-center">{{text}}</div>
                    <div class="row">
                        <div class="col-12 col-xl mb-3 mb-xl-0">
                            <input v-model="email" :class="email.isValidMail() ? 'is-valid' : 'is-invalid'" type="text" class="form-control form-control-lg" placeholder="Ingresa tu correo electrónico"/>
                        </div>
                        <div class="col-12 col-xl">
                            <div class="d-grid">
                                <button :disabled="!email.isValidMail()" @click="saveSuscriber" class="btn btn-lg fs1 btn-primary">REGISTRATE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    `,
};

export { NewsletterViewer };
