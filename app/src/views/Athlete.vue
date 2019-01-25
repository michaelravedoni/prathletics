<template>
  <section class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge">

    <section class="uk-section uk-section-xsmall">
      <h1 class="uk-h2">{{ athlete.title }}</h1>
      <vue-markdown>{{ athlete.athleteBio }}</vue-markdown>
    </section>

    <section class="uk-section uk-section-xsmall" v-if="athlete.programs">
      <h2 class="uk-h2">Programmes personnalisés</h2>
      <div v-for="p in athlete.programs" class="uk-card uk-card-body uk-card-small uk-card-primary"><router-link :to="'/programs/'+p.id">{{p.category.title}}</span></router-link></div>
    </section>
    <p v-else>Pas de programme personnalisé pour le moment</p>

    <section class="uk-section uk-section-xsmall">
      <h2 class="uk-h2">Prochaine session</h2>
      <div v-if="athlete.nextSession" class="uk-card uk-card-body uk-card-small uk-card-primary"><router-link :to="'/sessions/'+athlete.nextSession.id">{{athlete.nextSession.schedule.date|moment().locale('fr').format("dddd D")|capitalize}} à {{athlete.nextSession.schedule.date|moment().locale('fr').format("HH:mm")}}</router-link></div>
      <p v-else>Pas de sessions programmées pour le moment</p>

      <h2 class="uk-h2">Prochaines sessions</h2>
      <li v-if="athlete.sessions" v-for="s in athlete.sessions"><router-link :to="'/sessions/'+s.id">{{s.schedule.date|moment().locale('fr').format("dddd D")|capitalize}}-{{s.schedule.date|moment().locale('fr').format("HH:mm")}}</router-link></li>
      <p v-else>Pas de sessions programmées pour le moment</p>
    </section>

  </section>
</template>

<script>
import VueMarkdown from 'vue-markdown';

export default {
  name: 'athlete',
  components: {
    VueMarkdown
  },
  data() {
    return {
      athlete: {},
      apiUrl: process.env.VUE_APP_CRAFT_API_URL,
      trainerName: process.env.VUE_APP_TRAINER_NAME,
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
        if (localStorage.getItem(`athlete-${this.id}`) !== null) {
          this.athlete = JSON.parse(localStorage.getItem(`athlete-${this.id}`));
          console.log('athlete fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.athlete = JSON.parse(localStorage.getItem(`athlete-${this.id}`));
        console.log('athlete fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$Progress.start();
      this.$http.get(`${this.apiUrl}athletes/${this.id}.json`)
        .then((response) => {
          this.athlete = response.body;
          console.log('athlete fetched from API');
          localStorage.setItem(`athlete-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('athlete sended to localStorage');
        }, (response) => {
          console.log('Error while fetching athlete from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>

<style>
dl {}
  dt {
    float: left;
    clear: left;
    width: 100px;
    text-align: right;
    font-weight: bold;
  }
  dt::after {
    content: ":";
  }
  dd {
    margin: 0 0 0 110px;
    padding: 0 0 0.5em 0;
  }
  </style>
