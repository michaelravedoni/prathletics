<template>
  <div class="pr-programs">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Programes</h1>
      <div v-for="c in programs">
        <h2>{{c.title}}</h2>
        <ul>
          <li v-for="a in c.programs"><router-link :to="'/programs/'+a.id">{{ a.title|capitalize }}</router-link></li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'programs',
  components: {},
  data() {
    return {
      programs: [],
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
        this.programs = JSON.parse(localStorage.getItem('programs'));
        console.log('programs fetched from localStorage (online)');
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.programs = JSON.parse(localStorage.getItem('programs'));
        console.log('programs fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}programs.json`)
        .then((response) => {
          const groupedprograms = _.chain(response.body.data).groupBy('categories[0].title').toPairs().map(currentData => _.zipObject(['title', 'programs'], currentData))
            .value();
          this.programs = groupedprograms;
          console.log('programs fetched from API');
          localStorage.setItem('programs', JSON.stringify(groupedprograms));
          this.$Progress.finish();
          console.log('programs sended to localStorage');
        }, (response) => {
          console.log('Error while fetching programs from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
