import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Sessions from './views/Sessions.vue';
import Session from './views/Session.vue';
import Groups from './views/Groups.vue';
import Group from './views/Group.vue';
import Exercices from './views/Exercices.vue';
import Exercice from './views/Exercice.vue';
import Drills from './views/Drills.vue';
import Drill from './views/Drill.vue';
import Qr from './views/Qr.vue';

Vue.use(Router);

export default new Router({
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/sessions', name: 'sessions', component: Sessions },
    { path: '/sessions/:id', name: 'session', component: Session },
    { path: '/groups', name: 'groups', component: Groups },
    { path: '/groups/:id', name: 'group', component: Group },
    { path: '/exercices', name: 'exercices', component: Exercices },
    { path: '/exercices/:id', name: 'exercice', component: Exercice },
    { path: '/drills', name: 'drills', component: Drills },
    { path: '/drills/:id', name: 'drill', component: Drill },
    { path: '/qr', name: 'qr', component: Qr },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ './views/About.vue'),
    },
  ],
});
