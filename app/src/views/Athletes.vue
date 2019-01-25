<template>
  <div class="pr-athletes">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Athlètes</h1>
      <ul>
        <li v-for="a in athletes"><router-link :to="'/athletes/'+a.id">{{ a.title|capitalize }}</router-link></li>
      </ul>
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
      athletes: [],
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
        if (localStorage.getItem('athletes') !== null) {
          this.athletes = JSON.parse(localStorage.getItem('athletes'));
          console.log('athletes fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.athletes = JSON.parse(localStorage.getItem('athletes'));
        console.log('athletes fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}athletes.json`)
        .then((response) => {
          this.athletes = response.body.data;
          console.log('athletes fetched from API');
          localStorage.setItem('athletes', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('athletes sended to localStorage');
        }, (response) => {
          console.log('Error while fetching athletes from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
