<template>
  <section class="uk-section uk-section-xsmall uk-container uk-container-small uk-width-xlarge">

    <section class="uk-section uk-section-small">
      <h1 class="uk-h2">{{ exercise.title }}</h1>

      <img v-if="exercise.illustration" :src="exercise.illustration.url" :alt="exercise.illustration.title">
      <p v-if="exercise.exerciseDescription"><vue-markdown>{{ exercise.exerciseDescription }}</vue-markdown></p>

      <dl class="uk-tile uk-tile-small uk-tile-muted dl-horizontal">
        <dt v-if="exercise.note">Note</dt>
        <dd v-if="exercise.note"><vue-markdown>{{ exercise.note }}</vue-markdown></dd>
        <dt v-if="exercise.easier">Easier</dt>
        <dd v-if="exercise.easier"><li v-for="e in exercise.easier"><router-link class="" :to="'/exercises/'+e.id">{{ e.title }}</router-link></li></dd>
        <dt v-if="exercise.harder">Harder</dt>
        <dd v-if="exercise.harder"><li v-for="e in exercise.harder"><router-link class="" :to="'/exercises/'+e.id">{{ e.title }}</router-link></li></dd>
        <dt v-if="exercise.variation">Variation(s)</dt>
        <dd v-if="exercise.variation"><li v-for="e in exercise.variation"><router-link class="" :to="'/exercises/'+e.id">{{ e.title }}</router-link></li></dd>
        <dt v-if="exercise.difficulty">Difficulté</dt>
        <dd v-if="exercise.difficulty">{{ exercise.difficulty.label }}</dd>
        <dt v-if="exercise.intensity">Intensité</dt>
        <dd v-if="exercise.intensity">{{ exercise.intensity.label }}</dd>
        <dt v-if="exercise.muscleTarget">Muscle principal</dt>
        <dd v-if="exercise.muscleTarget"><span v-for="m in exercise.muscleTarget"> - {{ m.title }}</span></dd>
        <dt v-if="exercise.minimumAge">Âge minimum</dt>
        <dd v-if="exercise.minimumAge">{{ exercise.minimumAge.title }}</dd>
        <dt v-if="exercise.reference">Référence</dt>
        <dd v-if="exercise.reference">{{ exercise.reference }}</dd>
      </dl>
    </section>

  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';
import VueMarkdown from 'vue-markdown';

export default {
  name: 'exercise',
  components: { VueMarkdown },
  data() {
    return {
      exercise: {},
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
        if (localStorage.getItem(`exercise-${this.id}`) !== null) {
          this.exercise = JSON.parse(localStorage.getItem(`exercise-${this.id}`));
          console.log('exercise fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.exercise = JSON.parse(localStorage.getItem(`exercise-${this.id}`));
        console.log('exercise fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}exercises/${this.id}.json`)
        .then((response) => {
          this.exercise = response.body;
          console.log('exercise fetched from API');
          localStorage.setItem(`exercise-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('exercise sended to localStorage');
        }, (response) => {
          console.log('Error while fetching exercise from api');
          this.$Progress.fail();
        });
    },
  },
  watch: {
    '$route' (to, from) {
      this.getData();
    }
  }
};
</script>

<style>

</style>
