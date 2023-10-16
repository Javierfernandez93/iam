import { UserSupport } from "../../src/js/userSupport.module.js?v=2.6.5";

const AddsessionViewer = {
  name: "addsession-viewer",
  props: ["session"],
  emits: ["saveSession"],
  data() {
    return {
      UserSupport: new UserSupport(),
      offCanvas: null,
      sessionEditor: null,
      session: {},
    };
  },
  watch: {
    'session.courseValue': {
      async handler() {
        if(this.session.courseValue.isValidVimeoUrl())
        {
          this.session.course = await this.session.courseValue.getVimeoFrame()
        } else if(this.session.courseValue.isValidYoutubeUrl()) {
          this.session.course = await this.session.courseValue.getYoutubeVideoFrame()
        }
      },
      deep: true,
    },
    'session.catalog_multimedia_id': {
      handler() {
        this.session.course = null
      },
      deep: true,
    },
    session: {
      handler() {
        this.isSessionComplete = this.session.title != null && this.session.course != null
      },
      deep: true,
    },
  },
  methods: {
    newSession() {
      this.session = {
        unique_id: getUniqueId(),
        title: null,
        course: '',
        courseValue: '',
        catalog_multimedia_id: 1,
      };
    },
    selectSession(session) {
      this.session = session;

      this.offCanvas.show();
    },
    addSession() {
      this.newSession();

      this.offCanvas.show();
    },
    getCatalogMultimedias() {
      return new Promise((resolve) => {
        this.UserSupport.getCatalogMultimedias({}, (response) => {
          if (response.s == 1) {
            this.catalog_multimedias = response.catalog_multimedias;
          }

          resolve();
        });
      });
    },
    saveSession() {
      if (this.session.catalog_multimedia_id == 2) {
        this.session.course = this.session.video.youtubeParser();
      }

      this.$emit("saveSession", this.session);

      this.offCanvas.hide();
    },
    initEditor() {
      this.sessionEditor = new Quill(this.$refs.editor, {
        theme: "snow",
      });

      this.sessionEditor.on("text-change", () => {
        this.session.course = this.sessionEditor.root.innerHTML;
      });
    },
  },
  mounted() {
    this.offCanvas = new bootstrap.Offcanvas(this.$refs.offcanvasRight);

    this.getCatalogMultimedias().then(() => {
      this.initEditor();
    });
  },
  template: `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Lección del curso</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="form-floating mb-3">
                    <input v-model="session.title" :class="session.title ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.course.focus()" type="text" class="form-control" ref="title" placeholder="Título">
                    <label for="title">
                        <t>Título</t>
                    </label>
                </div>

                <div class="mb-3">
                    <div v-show="session.catalog_multimedia_id == 1">
                        <label for="course">
                            <t>Descripción</t>
                        </label>
                        <div ref="editor" style="height:120px;"></div>
                    </div>
                    <div v-if="session.catalog_multimedia_id == 2" class="form-floating">
                        Audio
                    </div>
                    <div v-if="session.catalog_multimedia_id == 3">
                        <div class="form-floating mb-3">
                          <input v-model="session.courseValue" :class="session.courseValue ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.course.focus()" type="text" class="form-control" ref="video" placeholder="Video">
                          <label for="video">
                              <t>Video</t>
                          </label>
                        </div>
                        <span class="frame-video" v-html="session.course"></span>
                    </div>
                    <div v-if="session.catalog_multimedia_id == 4">
                        Html
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <select :class="session.catalog_multimedia_id ? 'is-valid' : ''" class="form-select" ref="catalog_multimedia_id" v-model="session.catalog_multimedia_id" aria-label="Selecciona el tipo de sessión">
                        <option v-for="catalog_multimedia in catalog_multimedias" v-bind:value="catalog_multimedia.catalog_multimedia_id">
                            {{ catalog_multimedia.multimedia }}
                        </option>
                    </select>
                    <label for="catalog_multimedia_id">
                        <t>Tipo de sesión</t>
                    </label>
                </div>

                <button :disabled="!isSessionComplete" class="btn btn-dark" @click="saveSession"><t>Guardar sessión</t></button>
            </div>
        </div>
    `,
};

export { AddsessionViewer };
