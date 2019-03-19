<template>
  <div class="pr-plans">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Plans</h1>
      <ul>
        <li v-for="a in plans"><router-link :to="'/plans/'+a.id">{{ a.titleLong|capitalize }} ({{ a.title|capitalize }})</router-link></li>
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
      plans: [],
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
        if (localStorage.getItem('plans') !== null) {
          this.plans = JSON.parse(localStorage.getItem('plans'));
          console.log('plans fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.plans = JSON.parse(localStorage.getItem('plans'));
        console.log('plans fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}plans.json`)
        .then((response) => {
          this.plans = response.body.data;
          console.log('plans fetched from API');
          localStorage.setItem('plans', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('plans sended to localStorage');
        }, (response) => {
          console.log('Error while fetching plans from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
