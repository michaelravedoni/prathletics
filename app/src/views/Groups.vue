<template>
  <div class="pr-groups">
    <section class="uk-section uk-container uk-container-small uk-width-xlarge">
      <h1>Groupes</h1>
      <ul>
        <li v-for="a in groups"><router-link :to="'/groups/'+a.id">{{ a.title|capitalize }}</router-link></li>
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
      groups: [],
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
        if (localStorage.getItem('groups') !== null) {
          this.groups = JSON.parse(localStorage.getItem('groups'));
          console.log('groups fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.groups = JSON.parse(localStorage.getItem('groups'));
        console.log('groups fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}groups.json`)
        .then((response) => {
          this.groups = response.body.data;
          console.log('groups fetched from API');
          localStorage.setItem('groups', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('groups sended to localStorage');
        }, (response) => {
          console.log('Error while fetching groups from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
