<template>
  <section class="live">
    <div v-if="liveBlock" class="live-block">
      <div class="live-block-informations uk-padding-small uk-text-center">
        <router-link :to="'/'+section+'/'+id" class="uk-float-left"><i class="fas fa-chevron-circle-left"></i></router-link>
        <div v-if="liveBlock.note" class="live-block-note">{{ liveBlock.note }}</div>
        <span v-if="liveBlock.heading" class="live-block-heading uk-h4 uk-margin-remove">{{ liveBlock.heading }}</span>
        <span v-if="liveBlock.time" class="live-block-time" v-on:click="countdownTimeStart()"><i class="far fa-clock"></i> {{ liveBlock.time }}'</span>
        <span v-if="liveBlock.rest" class="live-block-rest" v-on:click="countdownRestStart()"><i class="fas fa-umbrella-beach"></i> {{ liveBlock.rest }}'</span>
      </div>
      <div class="live-countdown-block">
        <countdown ref="countdownBlockTime" :auto-start="false" :time="liveBlock.time * 60 * 1000" tag="span" class="live-countdown-block-time">
          <template slot-scope="props">Time: {{ props.minutes }}:{{ props.seconds }}</template>
        </countdown>
        <countdown ref="countdownBlockRest" :auto-start="false" :time="liveBlock.rest * 60 * 1000" tag="span" class="live-countdown-block-rest">
          <template slot-scope="props">Pause: {{ props.minutes }}:{{ props.seconds }}</template>
        </countdown>
      </div>
      <div class="stopwatch-icon" @click="showStopWatch"><i class="fas fa-stopwatch"></i></div>
      <div class="stopwatch">
        <div class="stopwatch-time uk-width-1-2" @click="stopTimer">{{ minutes | zeroPad }}:{{ seconds | zeroPad }}:{{ milliSeconds | zeroPad(3) }}</div>
        <div class="stopwatch-controls uk-width-1-2">
          <span class="uk-icon uk-margin-large-right stopwatch-button" @click="startTimer" v-show="!isRunning"><i class="fas fa-play"></i></span>
          <span class="uk-icon uk-margin-large-right stopwatch-button" @click="stopTimer" v-show="isRunning"><i class="fas fa-pause"></i></span>
          <span class="uk-icon stopwatch-button" @click="pushTime" v-show="isRunning"><i class="fas fa-list-ol"></i></span>
          <span class="uk-icon stopwatch-button" @click="clearAll" v-show="!isRunning"><i class="far fa-trash-alt"></i></span>
          <span class="uk-icon stopwatch-button uk-margin-large-left" @click="showStopWatch"><i class="far fa-eye-slash"></i></span>
        </div>
        <ul class="uk-width-1-1" v-if="times.length">
          <li class="item" v-for="item in times">
            {{ item.minutes | zeroPad }}:{{ item.seconds | zeroPad }}:{{ item.milliSeconds | zeroPad(3) }}
          </li>
        </ul>
      </div>
      <hooper class="live-block-exercices" uk-height-viewport="expand: true">
        <slide v-for="e in liveBlock.exercices" class="live-block-exercice">
          <div class="uk-card uk-card-default uk-width-large">
            <div v-if="e.exerciceRelation" class="uk-card-media-top uk-text-center">
              <img v-if="e.exerciceRelation.illustration" :src="e.exerciceRelation.illustration.url" :alt="e.exerciceRelation.illustration.title" class="live-block-exercice-image">
            </div>
            <div class="uk-padding">
              <div class="live-block-exercice-duration">{{e.duration}}</div>
              <div v-if="e.note" class="live-block-exercice-note">{{e.note}}</div>
              <h3 class="uk-card-title live-block-exercice-heading">
                <span v-if="e.exercice" class="live-block-exercice-heading">{{ e.exercice }}</span>
                <span v-if="e.exerciceRelation" class="live-block-exercice-heading">{{ e.exerciceRelation.title }}</span>
              </h3>
              <div class="live-block-exercice-description" v-if="(e.exerciceRelation && e.exerciceRelation.exerciceDescription)"><vue-markdown>{{ e.exerciceRelation.exerciceDescription }}</vue-markdown></div>
              <span v-if="e.exerciceRelation" class="live-block-exercice-link"><router-link class="uk-text-meta uk-text-small" :to="'/exercices/'+e.exerciceRelation.id" target="_blank"><i class="far fa-question-circle"></i></router-link></span>
            </div>
          </div>
        </slide>
        <hooper-navigation slot="hooper-addons"></hooper-navigation>
        <hooper-pagination slot="hooper-addons"></hooper-pagination>
        <hooper-progress slot="hooper-addons"></hooper-progress>
      </hooper>
    </div>
  </section>
</template>

<script>
/* Stopwatch from https://github.com/nagasawaaaa/vue-simple-stopwatch */
import VueMarkdown from 'vue-markdown';
import {
  Hooper,
  Slide,
  Progress as HooperProgress,
  Pagination as HooperPagination,
  Navigation as HooperNavigation,
} from 'hooper';

export default {
  name: 'live',
  components: {
    VueMarkdown,
    Hooper,
    Slide,
    HooperProgress,
    HooperPagination,
    HooperNavigation,
  },
  data() {
    return {
      live: {},
      apiUrl: process.env.VUE_APP_CRAFT_API_URL,
      currentUrl: window.location.href,
      times: [],
      animateFrame: 0,
      nowTime: 0,
      diffTime: 0,
      startTime: 0,
      isRunning: false,
    };
  },
  computed: {
    id() {
      return this.$route.query.pageId;
    },
    section() {
      return this.$route.query.section;
    },
    sectionSingular() {
      return this.$route.query.section.slice(0, -1);
    },
    blockIndex() {
      return this.$route.query.blockIndex;
    },
    liveBlock() {
      return this.live.blocks[this.blockIndex];
    },
    hours: function () {
      return Math.floor(this.diffTime / 1000 / 60 / 60);
    },
    minutes: function () {
      return Math.floor(this.diffTime / 1000 / 60) % 60;
    },
    seconds: function () {
      return Math.floor(this.diffTime / 1000) % 60;
    },
    milliSeconds: function () {
      return Math.floor(this.diffTime % 1000);
    },
  },
  filters: {
    zeroPad: function(value, num){
      var num = typeof num !== 'undefined' ? num : 2;
      return value.toString().padStart(num,"0");
    }
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
        if (localStorage.getItem(`${this.sectionSingular}-${this.id}`) !== null) {
          this.live = JSON.parse(localStorage.getItem(`${this.sectionSingular}-${this.id}`));
          console.log('live fetched from localStorage (online)');
        }
        this.fetchData();
      } else {
        // When offline, fetch data from cache
        this.live = JSON.parse(localStorage.getItem(`${this.sectionSingular}-${this.id}`));
        console.log('live fetched from localStorage (offline)');
        this.$Progress.finish();
      }
    },
    fetchData() {
      this.$http.get(`${this.apiUrl}${this.section}/${this.id}.json`)
      .then((response) => {
        this.live = response.body;
        console.log('live fetched from API');
        localStorage.setItem(`${this.sectionSingular}-${this.id}`, JSON.stringify(response.body));
        this.$Progress.finish();
        console.log('live sended to localStorage');
      }, (response) => {
        console.log('Error while fetching live from api');
        this.$Progress.fail();
      });
    },
    print(url) {
      window.print();
    },
    countdownRestStart(e) {
      this.$refs.countdownBlockRest.start();
      var countdownBlockRestE = document.querySelector('.live-countdown-block-rest');
      if (countdownBlockRestE.style.display !== 'none') {
        countdownBlockRestE.style.display = 'none';
      }
      else {
        countdownBlockRestE.style.display = 'inline';
      }
    },
    countdownTimeStart(e) {
      this.$refs.countdownBlockTime.start();
      var countdownBlockTimeE = document.querySelector('.live-countdown-block-time');
      if (countdownBlockTimeE.style.display !== 'none') {
        countdownBlockTimeE.style.display = 'none';
      }
      else {
        countdownBlockTimeE.style.display = 'inline';
      }
    },
    /* StopWatch */
    showStopWatch: function ()  {
      var stopWatchContainer = document.querySelector('.stopwatch');
      var stopWatchIcon = document.querySelector('.stopwatch-icon');
      if (stopWatchContainer.style.display !== 'none') {
        stopWatchContainer.style.display = 'none';
        stopWatchIcon.style.display = 'block';
      }
      else {
        stopWatchContainer.style.display = 'block';
        stopWatchIcon.style.display = 'none';
      }
    },
    setSubtractStartTime: function (time) {
      var time = typeof time !== 'undefined' ? time : 0;
      this.startTime = Math.floor(performance.now() - time);
    },
    startTimer: function () {
      var vm = this;
      vm.setSubtractStartTime(vm.diffTime);
      (function loop(){
        vm.nowTime = Math.floor(performance.now());
        vm.diffTime = vm.nowTime - vm.startTime;
        vm.animateFrame = requestAnimationFrame(loop);
      }());
      vm.isRunning = true;

    },
    stopTimer: function () {
      this.isRunning = false;
      cancelAnimationFrame(this.animateFrame);
    },
    pushTime: function () {
      this.times.push({
        hours: this.hours,
        minutes: this.minutes,
        seconds: this.seconds,
        milliSeconds: this.milliSeconds
      });
    },
    clearAll: function () {
      this.startTime = 0;
      this.nowTime = 0;
      this.diffTime = 0;
      this.times = [];
      this.stopTimer();
      this.animateFrame = 0;
    },
  },
};
</script>


<style media="screen">
.live-block-exercices {

}
.live-block-exercice {
  height: 100%;
  width: 800px;
  max-width: 800px;
}
.live-block-exercice-image {
  max-height: 250px;
  max-width: 100%;
}
.live-block-exercice-heading {
  font-size: 1.5rem;
  margin-top: 0;
  margin-bottom: 0;
}
.live-block-exercice-duration,
.live-block-exercice-description {
  font-size: 1rem;
}
.live-block-exercice-note {
  font-size: .85rem;
}
.live-block-exercice-description p {
  margin-bottom: 0;
}
.live-block-time,
.live-block-rest {
  margin-left: .8rem;
}
.live-block-note {
  margin-right: .8rem;
}
.live-countdown-block {
}
.live-countdown-block-rest,
.live-countdown-block-time {
  display: none;
  position: relative;
  margin-left: 1rem;
}
.stopwatch,
.stopwatch-icon {
  position: fixed;
  padding: .4rem;
  max-width: 400px;
  width: 100%;
  z-index: 10;
  color: white;
  font-size: 1.2rem;
}
.stopwatch {
  display: none;
  background-color: darkcyan;
}
.stopwatch-time {
  display: inline-block;
}
.stopwatch-controls {
  display: inline-block;
}
.stopwatch-button {
  min-width: 1rem;
}
.stopwatch ul {
  list-style: none;
  margin: 0;
  font-size: .8rem;
}
.hooper {
  position: relative;
  overflow: hidden;
  width: 100%;
}
.hooper, .hooper *, .hooper-track {
  box-sizing: border-box;
}
.hooper-track {
  display: flex;
  margin: 0;
  padding: 0;
  width: 100%;
  height: inherit;
}
.hooper-slide {
  flex-shrink: 0;
  height: auto%;
  background-color: #62caaa;
  padding: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  border: 2px solid #fff;
  border-radius: 10px
}
@media (max-width: 600px) {
  .hooper-slide {
    /*align-items: start;*/
  }
  .live-block-exercice-heading {
    font-size: 1rem;
    margin-top: 0;
    margin-bottom: 0;
  }
  .live-block-exercice-duration,
  .live-block-exercice-description {
    font-size: .85rem;
  }
  .live-block-exercice-note {
    font-size: .65rem;
  }
  .live-block-exercice-image {
    max-height: 150px;
    max-width: 100%;
  }
}
.is-active {
  background-color: #47da7f
}
.hooper.is-vertical .hooper-track {
  flex-direction: column;
  height: 200px;
}
.hooper.is-rtl {
  direction: rtl;
}
.hooper-sr-only {
  position: absolute;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  margin: -1px;
  padding: 0;
  width: 1px;
  height: 1px;
  border: 0;
}
.hooper-progress {
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  height: 4px;
  background-color: #efefef;
}
.hooper-progress-inner {
  height: 100%;
  background-color: #4285f4;
  transition: .3s;
}
.hooper-pagination {
  position: absolute;
  right: 50%;
  bottom: 0;
  display: flex;
  padding: 5px 10px;
  -webkit-transform: translateX(50%);
  transform: translateX(50%);
}
.hooper-indicators {
  display: flex;
  margin: 0;
  padding: 0;
  list-style: none;
}
.hooper-indicator.is-active, .hooper-indicator:hover {
  background-color: #4285f4;
}
.hooper-indicator {
  margin: 0 2px;
  padding: 0;
  width: 12px;
  height: 4px;
  border: none;
  border-radius: 4px;
  background-color: #fff;
  cursor: pointer;
}
.hooper-pagination.is-vertical {
  top: 50%;
  right: 0;
  bottom: auto;
  -webkit-transform: translateY(-50%);
  transform: translateY(-50%);
}
.hooper-pagination.is-vertical .hooper-indicators {
  flex-direction: column;
}
.hooper-pagination.is-vertical .hooper-indicator {
  width: 6px;
}
.hooper-next, .hooper-prev {
  position: absolute;
  top: 50%;
  padding: 1em;
  border: none;
  background-color: transparent;
  cursor: pointer;
  -webkit-transform: translateY(-50%);
  transform: translateY(-50%);
}
.hooper-next.is-disabled, .hooper-prev.is-disabled {
  opacity: .3;
  cursor: not-allowed;
}
.hooper-next {
  right: 0;
}
.hooper-prev {
  left: 0;
}
.hooper-navigation.is-vertical .hooper-next {
  top: auto;
  bottom: 0;
  -webkit-transform: initial;
  transform: none;
}
.hooper-navigation.is-vertical .hooper-prev {
  top: 0;
  right: 0;
  bottom: auto;
  left: auto;
  -webkit-transform: initial;
  transform: none;
}
.hooper-navigation.is-rtl .hooper-prev {
  right: 0;
  left: auto;
}
.hooper-navigation.is-rtl .hooper-next {
  right: auto;
  left: 0;
}
body.live header,
body.live footer {
  display:none;
}
</style>
