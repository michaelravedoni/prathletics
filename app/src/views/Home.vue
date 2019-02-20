<template>
  <div class="pr-home">
    <section class="uk-section uk-padding-remove uk-section-primary uk-text-center uk-flex uk-flex-center uk-flex-middle" uk-height-viewport="offset-top: true; expand: true">
      <div class="uk-grid-match uk-grid-collapse uk-child-width-1-1 uk-grid-stack" uk-grid>
        <div v-for="g in groups">
          <div class="uk-grid-match uk-grid-collapse uk-child-width-1-3@s uk-flex-center uk-flex-middle uk-text-center uk-margin-small-top uk-margin-small-bottom uk-grid-stack" uk-grid>
            <div>
              <div class="uk-card uk-card-small uk-card-body">
                <div class="uk-h1 uk-margin-remove">
                  <router-link :to="'/groups/'+g.id">{{g.title}}</router-link>
                </div>
              </div>
            </div>
            <div>
              <div v-if="g.nextSession" class="uk-card uk-card-small uk-card-body uk-card-secondary">
                <div>Prochain entraînement</div>
                <div class="uk-h4 uk-margin-remove">
                  <router-link :to="'/sessions/'+g.nextSession.id">
                    <span v-if="g.nextSession.beforeNow == false">{{g.nextSession.fromNow|duration(1).locale('fr').humanize(true)|capitalize}}</span>
                    <span v-if="g.nextSession.beforeNow == true">Commencé il y a {{g.nextSession.fromNow|duration().locale('fr').humanize()}}</span></router-link>
                </div>
                <div class="uk-text-meta">{{g.nextSession.schedule.date|moment().locale('fr').format("dddd D")|capitalize}} à {{g.nextSession.schedule.date|moment().locale('fr').format("HH:mm")}}</div>
              </div>
              <div v-else class="uk-card uk-card-small uk-card-body uk-card-secondary">
                <div>Prochain entraînement</div>
                <div class="uk-h4 uk-margin-remove">-</div>
                <div class="uk-text-meta">Pas d'entraînement programmé</div>
              </div>
            </div>
            <div class="uk-visible@s">
              <div v-if="g.sessions.length" class="uk-card uk-card-small uk-card-body uk-text-left">
                <div class="uk-h4 uk-margin-remove">Prochaines sessions</div>
                <div v-for="s in g.sessions" class="uk-text-meta">
                  <router-link :to="'/sessions/'+s.id">{{s.schedule.date|moment().locale('fr').format("dddd D")|capitalize}} à {{s.schedule.date|moment().locale('fr').format("HH:mm")}}</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="uk-section uk-section-secondary uk-flex uk-flex-center uk-flex-middle uk-text-center uk-height-small">
      <div class="uk-h1"><router-link to="/programs">Programmes</router-link></div>
    </section>
    <section class="uk-section uk-section-default uk-flex uk-flex-center uk-flex-middle uk-text-center uk-height-small">
      <div class="uk-h1"><a :href="backendUrl+'feedback'" target="_blank">Feedback</a></div>
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
      now: this.$time(Date.now()),
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
          console.log('Groups fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.groups = JSON.parse(localStorage.getItem('groups'));
        console.log('Groups fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}groups.json`)
        .then((response) => {
          this.groups = response.body.data;
          console.log('Groups fetched from API');
          localStorage.setItem('groups', JSON.stringify(response.body.data));
          this.$Progress.finish();
          console.log('Groups sended to localStorage');
        }, (response) => {
          console.log('Error while fetching groups from api');
          this.$Progress.fail();
        });
    },
  },
};
</script>
