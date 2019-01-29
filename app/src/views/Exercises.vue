<template>
  <div class="pr-exercises">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Exercises</h1>
      <ul>
        <li v-for="a in exercises"><router-link :to="'/exercises/'+a.id">{{ a.title|capitalize }}</router-link></li>
      </ul>
    </section>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'exercises',
  components: {},
  data() {
    return {
      exercises: [],
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
        if (localStorage.getItem('exercises') !== null) {
          this.exercises = JSON.parse(localStorage.getItem('exercises'));
          console.log('exercises fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.exercises = JSON.parse(localStorage.getItem('exercises'));
        console.log('exercises fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}exercises.json`)
        .then((response) => {
          this.exercises = response.body.data;
          console.log('exercises fetched from API');
          localStorage.setItem('exercises', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('exercises sended to localStorage');
        }, (response) => {
          console.log('Error while fetching exercises from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
