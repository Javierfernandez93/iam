import { User } from "../../src/js/user.module.js?v=2.6.6";

String.prototype.isValidMail = function () {
  var pattern = new RegExp(
    /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i
  );
  return pattern.test(this);
};

const LoginwidgetViewer = {
  name: "loginwidget-viewer",
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
                    <div class="fs-2 Space-Grotesk pb-3 fw-bold text-center">Se parte de I AM BEAUTY OIL</div>
                    <div class="row justify-content-center">
                      <div class="col-12 col-xl-8">
                        <div class="row">
                            <div class="col-12 col-xl">
                              <div class="d-grid">
                                <a href="../../apps/login" class="btn btn-lg lead btn-outline-primary">Iniciar sesión</a>
                              </div>
                            </div>
                            <div class="col-12 col-xl">
                              <div class="d-grid">
                                <a href="../../apps/signup" class="btn btn-lg lead btn-primary">Crear cuenta</a>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </section>
    `,
};

export { LoginwidgetViewer };
