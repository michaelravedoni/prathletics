<template>
  <section class="pr-session">

    <section class="uk-container uk-container-small pr-session-meta">
      <span class="pr-no-screen pr-meta-copyright"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i> {{trainerName}}</span>
      <span class="pr-no-screen pr-meta-printed uk-float-right">Imprimé le {{ new Date()|moment().format('D.M.Y') }}</span>
    </section><!-- end pr-session-meta -->

    <section class="uk-container uk-container-small uk-width-xlarge pr-session-header">
      <div class="pr-crumbs" v-if="session.ancestors">
        <span v-for="a in session.ancestors">{{a.typeCadreLabel.titleLong}} {{a.cadreLabel.replace(/-/g, ' ')|capitalize}}</span>
      </div>
      <span class="pr-no-screen pr-header-qr uk-float-right" style="width:50px;"><qr-code :text="currentUrl"error-level="H"></qr-code></span>
      <div class="uk-h4 pr-session-header-heading-group" v-if="session.groups">
        <router-link :to="'/groups/'+session.groups[0].id">{{session.groups[0].title}}</router-link>
      </div>
      <h1 class="uk-h2 pr-session-header-heading" v-if="session.schedule">{{session.schedule.date|moment().locale('fr').format("dddd D")|capitalize}} — {{session.schedule.date|moment().locale('fr').format("HH:mm")}}</h1>
    </section><!-- end pr-session-header -->

    <section v-if="session.type == 'session'" class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge pr-session-blocks">

      <div v-for="(b, index) in session.blocks" class="pr-block">
        <div class="pr-block-informations">
          <span class="pr-block-heading">{{b.heading}} <router-link v-if="!b.text" :to="'/live?section=sessions&pageId='+session.id+'&blockIndex='+index"><i class="far fa-play-circle"></i></router-link ></span>
            <span v-if="b.time" class="pr-block-time">{{b.time}}'</span>&nbsp;<span v-if="b.rest" class="pr-block-rest">[{{b.rest}}']<span v-if="b.note" class="pr-block-note">{{b.note}}</span></span>
        </div>
        <div v-if="b.text"><vue-markdown>{{b.text}}</vue-markdown></div>
        <div v-if="b.exercices" class="pr-block-exercices"></div>
        <div v-for="e in b.exercices" class="pr-block-exercice">

          <span v-if="e.exerciceRelation" class="pr-block-exercice-link"><router-link class="uk-text-meta uk-text-small" :to="'/exercices/'+e.exerciceRelation.id" target="_blank"><i class="far fa-question-circle"></i></router-link></span>
          <span v-if="e.exerciceRelation" class="pr-block-exercice-heading">{{ e.exerciceRelation.title }}</span>

          <span v-if="e.exercice" class="pr-block-exercice-link"></span>
          <span v-if="e.exercice" class="pr-block-exercice-heading">{{ e.exercice }}</span>

          <span class="pr-block-exercice-duration">
            <span v-if="e.series">{{ e.series }}x</span><span v-if="e.repetitions">{{ e.repetitions }}</span>
            <span v-if="e.series == null && e.repetitions">x</span>
            &nbsp;
            <span v-if="e.rest">[{{ e.rest }}]</span>
          </span>
          <span class="pr-block-exercice-note uk-text-right">{{e.note}}</span>
        </div>
      </div>
    </section><!-- end pr-session-blocks (type:session) -->

    <section class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge pr-session-footer">
      <div class="uk-button-group pr-session-footer-buttons">
        <a class="uk-button uk-button-default uk-button-small" :href="session.url" target="_blank"><i class="fas fa-cube"></i></a>
        <a class="uk-button uk-button-default uk-button-small" v-on:click="print(currentUrl)" target="_blank"><i class="fas fa-print"></i></a>
        <button class="uk-button uk-button-default uk-button-small" uk-toggle="target: #qr-modal" type="button"><i class="fas fa-qrcode"></i></button>
      </div>

      <div id="qr-modal" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
          <button class="uk-modal-close-default" type="button" uk-close></button>
          <h2 class="uk-modal-title uk-text-center uk-text-break">Share your programm</h2>
          <p class="uk-text-center uk-text-break">{{currentUrl}}</p>
          <div style="text-align: -webkit-center;"><qr-code :text="currentUrl"error-level="H"></qr-code></div>
        </div>
      </div>
    </section><!-- end pr-session-footer -->

  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';
import VueMarkdown from 'vue-markdown';

export default {
  name: 'session',
  components: { VueMarkdown },
  data() {
    return {
      session: {},
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
        if (localStorage.getItem(`session-${this.id}`) !== null) {
          this.session = JSON.parse(localStorage.getItem(`session-${this.id}`));
          console.log('session fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.session = JSON.parse(localStorage.getItem(`session-${this.id}`));
        console.log('session fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}sessions/${this.id}.json`)
        .then((response) => {
          this.session = response.body;
          console.log('session fetched from API');
          localStorage.setItem(`session-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('session sended to localStorage');
        }, (response) => {
          console.log('Error while fetching session from api');
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
.pr-block {
  margin-bottom: 1em;
}
.pr-block p,
.pr-block ul {
  margin-bottom: .1rem;
  margin-top: .1rem;
}
.pr-block-informations {
  border-bottom: 1px solid #999;
  display: flex;
  flex-direction: row;
}
.pr-block-heading {
  font-weight: bold;
  flex-grow: 1;
}
.pr-block-time {
  font-weight: normal;
}
.pr-block-rest {
  font-weight: normal;
}
.pr-block-exercices {
  width: 100%;
}
.pr-block-exercice {
  display: flex;
  flex-wrap: wrap;
  border-bottom: 1px solid #eee;
  border-collapse: collapse;
}
.pr-block-exercice-link {
  flex: 0 1 auto;
  width: 20px;
}
.pr-block-exercice-heading {
  flex: 3 1 0;
}
.pr-block-exercice-duration {
  flex: 1 1 0;
  margin-left: 1em;
}
.pr-block-exercice-note {
  font-size: 85%;
  flex: 1 1 0;
}
.pr-crumbs-space {
  display: block;
}
.pr-crumbs span::after {
  content: " - ";
}
.pr-crumbs span:last-child::after {
  display: none;
}
.pr-session-header-heading {
  margin-top: 0;
  margin-bottom: 1em;
}
.pr-session-header-heading-group {
  margin-top: 1em;
  margin-bottom: 0;
}
.pr-session-footer-buttons {
  display: block;
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 0;
}
@media screen and (max-width: 640px) {
  .pr-block-exercices {
    display: block;
  }
  .pr-block-exercice {
    display: block;
  }
  .pr-block-exercice-link {
    display: unset;
  }
  .pr-block-exercice-heading {
    display: unset;
  }
  .pr-block-exercice-duration {
    font-size: 85%;
    font-weight: bold;
    display: flex;
    flex-flow: row-reverse;
  }
  .pr-block-exercice-note {
    display: flex;
    flex-flow: row-reverse;
  }
}
@media print {
  *, ::after, ::before {
    background: unset!important;
    color: unset!important;
    box-shadow: unset!important;
    text-shadow: unset!important;
  }
  .pr-no-print,
  .pr-session-footer,
  header,
  footer {
    display: none;
  }
  a {
    text-decoration: none;
  }
   {
    font-size: 85%;
    padding-top: 1em;
    width: auto;
  }
  .pr-session-header,
  .pr-session-meta,
  .pr-session-blocks {
    padding-top: 1em;
    padding-bottom: .2em;
    font-size: 85%;
    width: auto;
  }
  .pr-session-header {
    padding-top: 0;
  }
  .pr-session-header {
    padding-bottom: 0;
    font-size: 85%;
    width: auto;
  }
  .pr-session-header-heading,
  .pr-session-header-heading-group {
    margin-bottom: 0;
  }
  .pr-session-header-heading {
    margin-top: 0;
    font-size: x-large;
  }
  .pr-session-header-heading-group {
    margin-top:  1em;
    font-size: large;
  }
  .pr-crumbs-space {
    display: inline;
  }
  .pr-crumbs-space:after {
    content: " - ";
  }
  .pr-session-meta {
    border-bottom: 1px solid;
  }
  .pr-meta-title {
    margin-left: 33vw;
  }
  .pr-session-metrics {
    margin-top: 0;
    color: grey;
    font-size: 75%;
  }
  .pr-cadre-metrics {
    margin-top: -15px;
    font-size: xx-small;
    margin-bottom: 0!important;
  }
  .pr-session-metrics div,
  .pr-cadre-metrics div	{
    display: inline;
  }
  .pr-block {
    page-break-inside: avoid;
  }
  .pr-session {
    font-size: 85%;
  }
  .pr-meta {
    display: block;
  }
}

@media screen {
  .pr-meta,
  .pr-no-screen {
    display: none;
  }
}
</style>
