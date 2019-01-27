<template>
  <section class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge">

    <section class="uk-section uk-section-xsmall">
      <h1 class="uk-h2">{{ group.title }}</h1>
      <p>{{ group.groupDescription }}</p>
      <p><span v-for="a in group.athletes"> - <router-link :to="'/athletes/'+a.id">{{a.title}}</router-link></span></p>
    </section>

    <section class="uk-section uk-section-xsmall">
      <h2 class="uk-h2">Prochaine session</h2>
      <div v-if="group.nextSession" class="uk-card uk-card-body uk-card-small uk-card-primary"><router-link :to="'/sessions/'+group.nextSession.id">{{group.nextSession.schedule.date|moment().locale('fr').format("dddd D")|capitalize}} à {{group.nextSession.schedule.date|moment().locale('fr').format("HH:mm")}}</router-link></div>
      <p v-else>Pas de sessions programmées pour le moment</p>

      <h2 class="uk-h2">Prochaines sessions</h2>
      <ul v-if="group.sessions.length">
        <li v-for="s in group.sessions"><router-link v-if="s.schedule" :to="'/sessions/'+s.id">{{s.schedule.date|moment().locale('fr').format("dddd D")|capitalize}}-{{s.schedule.date|moment().locale('fr').format("HH:mm")}}</router-link></li>
      </ul>
      <p v-else>Pas de sessions programmées pour le moment</p>
    </section>

  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'group',
  components: {},
  data() {
    return {
      group: {},
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
        if (localStorage.getItem(`group-${this.id}`) !== null) {
          this.group = JSON.parse(localStorage.getItem(`group-${this.id}`));
          console.log('group fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.group = JSON.parse(localStorage.getItem(`group-${this.id}`));
        console.log('group fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$Progress.start();
      this.$http.get(`${this.apiUrl}groups/${this.id}.json`)
        .then((response) => {
          this.group = response.body;
          console.log('group fetched from API');
          localStorage.setItem(`group-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('group sended to localStorage');
        }, (response) => {
          console.log('Error while fetching group from api');
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
