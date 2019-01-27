<template>
  <section class="pr-programs">

    <section class="uk-section uk-section-xsmall uk-container uk-container-small pr-programs-meta">
      <span class="pr-no-screen pr-meta-copyright"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i> {{trainerName}}</span>
      <span class="pr-no-screen pr-meta-printed uk-float-right">Imprimé le {{ new Date()|moment().format('D.M.Y') }}</span>
    </section><!-- end pr-programs-meta -->

    <section class="uk-section uk-section-xsmall uk-container uk-container-small pr-programs-header">
      <span class="pr-no-screen pr-header-qr uk-float-right" style="width:80px;"><qr-code :text="currentUrl"error-level="H"></qr-code></span>
      <h1 class="uk-h2 pr-programs-header-heading">{{ program.title }}</h1>
      <vue-markdown>{{ program.description }}</vue-markdown>
    </section><!-- end pr-programs-header -->

    <section class="uk-section uk-section-xsmall uk-container uk-container-small pr-programs-blocks">
      <section class="">

        <div v-for="(b, index) in program.blocks" class="pr-block">
          <div class="pr-block-informations">
            <span class="pr-block-heading">{{b.heading}} <router-link  :to="'/live?section=programs&pageId='+program.id+'&blockIndex='+index"><i class="far fa-play-circle"></i></router-link ></span>
            <span v-if="b.time" class="pr-block-time">{{b.time}}'</span>
            <span v-if="b.rest" class="pr-block-rest">[{{b.rest}}]<span v-if="b.note" class="pr-block-note">{{b.note}}</span></span>
          </div>
          <div v-if="b.text"><vue-markdown>{{b.text}}</vue-markdown></div>
          <div v-if="b.exercices" class="pr-block-exercices"></div>
          <div v-for="e in b.exercices" class="pr-block-exercice">

            <span v-if="e.exerciceRelation" class="pr-block-exercice-link"><router-link class="uk-text-meta uk-text-small" :to="'/exercices/'+e.exerciceRelation.id" target="_blank"><i class="far fa-question-circle"></i></router-link></span>
            <span v-if="e.exerciceRelation" class="pr-block-exercice-heading">{{ e.exerciceRelation.title }}</span>

            <span v-if="e.exercice" class="pr-block-exercice-link"></span>
            <span v-if="e.exercice" class="pr-block-exercice-heading">{{ e.exercice }}</span>

            <span class="pr-block-exercice-duration" v-if="e.duration">
              {{e.duration}}
            </span>
            <span v-if="e.note" class="pr-block-exercice-note">{{e.note}}</span>
          </div>
        </div>
      </section>
    </section><!-- end pr-programs-blocks -->

    <section class="uk-section uk-section-xsmall uk-container uk-container-small pr-programs-footer">
      <div class="uk-button-group pr-session-footer-buttons">
        <a class="uk-button uk-button-default uk-button-small" :href="program.url" target="_blank"><i class="fas fa-cube"></i></a>
        <a class="uk-button uk-button-default uk-button-small" v-on:click="print(currentUrl)" target="_blank"><i class="fas fa-print"></i></a>
        <button class="uk-button uk-button-default uk-button-small" uk-toggle="target: #qr-modal" type="button"><i class="fas fa-qrcode"></i></button>
      </div>

      <div id="qr-modal" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
          <button class="uk-modal-close-default" type="button" uk-close></button>
          <h2 class="uk-modal-title uk-text-center uk-text-break">Share your program</h2>
          <p class="uk-text-center uk-text-break">{{currentUrl}}</p>
          <div style="text-align: -webkit-center;"><qr-code :text="currentUrl"error-level="H"></qr-code></div>
        </div>
      </div>
    </section><!-- end pr-programs-footer -->
  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';
import VueMarkdown from 'vue-markdown';

export default {
  name: 'program',
  components: { VueMarkdown },
  data() {
    return {
      program: {},
      apiUrl: process.env.VUE_APP_CRAFT_API_URL,
      trainerName: process.env.VUE_APP_TRAINER_NAME,
      currentUrl: window.location.href,
    };
  },
  computed: {
    id() {
      return this.$route.params.id;
    },
  },
  created() {
    // Call method getData when created
    this.$Progress.start();
    this.getData();
  },
  methods: {
    getData() {
      // When online, fetch data from api
      if (navigator.onLine) {
        if (localStorage.getItem(`program-${this.id}`) !== null) {
          this.program = JSON.parse(localStorage.getItem(`program-${this.id}`));
          console.log('program fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.program = JSON.parse(localStorage.getItem(`program-${this.id}`));
        console.log('program fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}programs/${this.id}.json`)
        .then((response) => {
          this.program = response.body;
          console.log('program fetched from API');
          localStorage.setItem(`program-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('program sended to localStorage');
        }, (response) => {
          console.log('Error while fetching program from api');
          this.$Progress.fail();
        });
    },
    print(url) {
      window.print();
    },
  },
};
</script>

<style>
@media print {
  *, ::after, ::before {
    background: unset!important;
    color: unset!important;
    box-shadow: unset!important;
    text-shadow: unset!important;
  }
  .pr-no-print,
  .pr-programs-footer,
  header,
  footer {
    display: none;
  }
  a {
    text-decoration: none;
  }
  .pr-programs-blocks {
    font-size: 80%;
    padding-top: 0;
  }
  .pr-programs-header, .pr-programs-meta {
    padding-top: .4em;
    padding-bottom: .2em;
    font-size: 75%;
    width: auto;
  }
  .pr-programs-header-heading {
    margin-top: 0;
    margin-bottom: 0;
  }
  .pr-meta,
  .pr-programs-meta {
    border-bottom: 1px solid;
  }
  .pr-meta-title {
    margin-left: 33vw;
  }
  .pr-block {
    page-break-inside: avoid;
  }
}
@media screen {
  .pr-meta,
  .pr-no-screen {
    display: none;
  }
}
</style>
