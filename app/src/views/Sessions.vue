<template>
  <div class="pr-sessions">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Sessions</h1>
      <div class="pr-crumbs">
        <router-link v-for="a in sessions" :to="'/sessions/'+a.id">{{ a.title|capitalize }}</router-link>
      </div>
    </section>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'home',
  components: {},
  data() {
    return {
      sessions: [],
      apiUrl: process.env.VUE_APP_CRAFT_API_URL,
      backendUrl: process.env.VUE_APP_CRAFT_BACKEND_URL,
      trainerName: process.env.VUE_APP_TRAINER_NAME,
    };
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
        if (localStorage.getItem('sessions') !== null) {
          this.sessions = JSON.parse(localStorage.getItem('sessions'));
          console.log('sessions fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.sessions = JSON.parse(localStorage.getItem('sessions'));
        console.log('sessions fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}sessions.json`)
        .then((response) => {
          this.sessions = response.body.data;
          console.log('sessions fetched from API');
          localStorage.setItem('sessions', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('sessions sended to localStorage');
        }, (response) => {
          console.log('Error while fetching sessions from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
