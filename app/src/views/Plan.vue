<template>
  <section class="uk-section uk-section-xsmall uk-container uk-container-small">

    <section class="uk-section uk-section-xsmall">
      <h1 class="uk-h2">{{ plan.titleLong }}</h1>
    </section>

    <section class="uk-section uk-section-xsmall">
      <div class="uk-grid">
      <div v-for="(column, index) in plan.plan" class="tg-baqh" :colspan="column.weekRange" :key="column.id">
        <div class="uk-width-auto">
          <div class="uk-text-small">{{column.period}} ({{column.periodLabel}})</div>
          <table class="pr-plan-table">
            <tr>
              <th v-for="col in plan.plan[index].weeks" class="tg-0lax">
                <router-link :to="'/weeks/'+col.weekNumber">{{col.weekNumber}}</router-link>
              </th>
            </tr>
            <tr>
              <td v-for="col in plan.plan[index].weeks" class="tg-0lax">
                <router-link v-for="s in col.sessions" :to="'/sessions/'+s.id"><span class="pr-plan-session">{{s.schedule.date|moment().locale('fr').format('ddd')}}</span></router-link>
              </td>
            </tr>
            </table>
        </div>
      </div>
</div>
    </section>

  </section>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  name: 'plan',
  components: {},
  data() {
    return {
      plan: {},
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
        if (localStorage.getItem(`plan-${this.id}`) !== null) {
          this.plan = JSON.parse(localStorage.getItem(`plan-${this.id}`));
          console.log('plan fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.plan = JSON.parse(localStorage.getItem(`plan-${this.id}`));
        console.log('plan fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$Progress.start();
      this.$http.get(`${this.apiUrl}plans/${this.id}.json`)
        .then((response) => {
          this.plan = response.body;
          console.log('plan fetched from API');
          localStorage.setItem(`plan-${this.id}`, JSON.stringify(response.body));
          this.$Progress.finish();
          console.log('plan sended to localStorage');
        }, (response) => {
          console.log('Error while fetching plan from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>

<style>
	.pr-plan-table {border-collapse:collapse;border-spacing:0;}
	.pr-plan-table td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
	.pr-plan-table th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
	.pr-plan-table .tg-0lax{
		text-align: center;
    vertical-align: top;
    width: 15px;
	}
	.pr-plan-table .tg-baqh{text-align:center;vertical-align:top}
.pr-plan-session {
  display: block;
}
</style>
