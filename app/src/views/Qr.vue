<template>
  <section class="uk-section uk-padding-remove uk-section-primary uk-text-center pr-qr" uk-height-viewport="offset-top: true; expand: true">
	<div class="uk-margin-bottom uk-grid-collapse" uk-grid>
		<div class="uk-width-1-1">
			<img class="uk-width-small" src="images/logo/logo-prathletics.png" />
		</div>
		<div class="uk-width-1-1">
			<span class="uk-h4">Your program on</span>
		</div>
		<div class="uk-width-1-1 uk-margin-bottom">
			<span class="uk-h1 uk-text-break">{{qrUrl.replace(/https?:\/\//g, '')}}</span>
		</div>
		<div class="uk-width-1-1 uk-text-break" style="text-align: -webkit-center;">
      <qr-code :text="qrUrl"></qr-code>
		</div>
	</div>
</section>
</template>

<script>
export default {
  name: 'qr',
  components: {},
  data() {
    return {
      drills: [],
      apiUrl: process.env.VUE_APP_CRAFT_API_URL,
      backendUrl: process.env.VUE_APP_CRAFT_BACKEND_URL,
      trainerName: process.env.VUE_APP_TRAINER_NAME,
    };
  },
  computed: {
    // un accesseur (getter) calculé
    qrUrl() {
      if (process.env.VUE_APP_URL) {
        return process.env.VUE_APP_URL;
      }
      return window.location.href.replace(`/${window.location.hash}`, '');
    },
  },
};
</script>
