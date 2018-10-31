<template>
  <div class="pr-drills">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Programmes et drills</h1>
      <div v-for="category in drills">
        <h2>{{category.title}}</h2>
        <ul>
          <li v-for="a in category.drills"><router-link :to="'/drills/'+a.id">{{ a.title|capitalize }}</router-link></li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'drills',
  components: {},
  data() {
    return {
      drills: [],
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
        this.drills = JSON.parse(localStorage.getItem('drills'));
        console.log('drills fetched from localStorage (online)');
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.drills = JSON.parse(localStorage.getItem('drills'));
        console.log('drills fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}drills.json`)
        .then((response) => {
          const groupedDrills = _.chain(response.body.data).groupBy("category.title").toPairs().map(function(currentData){return _.zipObject(["title", "drills"], currentData);}).value();
          this.drills = groupedDrills;
          console.log('drills fetched from API');
          localStorage.setItem('drills', JSON.stringify(groupedDrills));
          this.$Progress.finish();
          console.log('drills sended to localStorage');
        }, (response) => {
          console.log('Error while fetching drills from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
