<template>
  <section class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge">

    <section class="uk-section uk-section-small">
      <h1 class="uk-h2">{{ exercice.title }}</h1>

      <img v-if="exercice.illustration" :src="exercice.illustration.url" :alt="exercice.illustration.title">
      <p v-if="exercice.exerciceDescription"><vue-markdown>{{ exercice.exerciceDescription }}</vue-markdown></p>

      <dl class="uk-tile uk-tile-small uk-tile-muted">
        <dt>Note</dt>
        <dd><vue-markdown>{{ exercice.note }}</vue-markdown></dd>
        <dt>Difficulté</dt>
        <dd>{{ exercice.difficulty.value }}</dd>
        <dt>Intensité</dt>
        <dd>{{ exercice.exerciceIntensite.value }}</dd>
        <dt>Répétitions minimum</dt>
        <dd>{{ exercice.repetitionsMinimum }}</dd>
        <dt>Répétitions maximum</dt>
        <dd>{{ exercice.repetitionsMaximum }}</dd>
      </dl>
    </section>

  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';
import VueMarkdown from 'vue-markdown';

export default {
  name: 'exercice',
  components: { VueMarkdown },
  data() {
    return {
      exercice: {},
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
        if (localStorage.getItem(`exercice-${this.id}`) !== null) {
          this.exercice = JSON.parse(localStorage.getItem(`exercice-${this.id}`));
          console.log('exercice fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.exercice = JSON.parse(localStorage.getItem(`exercice-${this.id}`));
        console.log('exercice fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}exercices/${this.id}.json`)
        .then((response) => {
          this.exercice = response.body;
          console.log('exercice fetched from API');
          localStorage.setItem(`exercice-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('exercice sended to localStorage');
        }, (response) => {
          console.log('Error while fetching exercice from api');
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
