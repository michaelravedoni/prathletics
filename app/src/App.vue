<template>
	<div class="uk-offcanvas-content"><!-- Off-canvas-wrapper -->
		<vue-progress-bar></vue-progress-bar>
		<header>
			<!-- Navigation -->
			<nav class="uk-navbar-container">
				<div class="uk-container">
					<div class="uk-navbar">
						<div class="uk-navbar-left">
							<router-link class="uk-navbar-item uk-logo" to="/">Prathletics</router-link>
						</div>
						<div class="uk-navbar-center">
							<div class="uk-navbar-nav"></div>
						</div>
						<div class="uk-navbar-right">
							<ul class="uk-navbar-nav uk-visible@m">
								<li><router-link to="/groups">Groupes</router-link></li>
								<li><router-link to="/athletes">Athlètes</router-link></li>
								<li><router-link to="/programs">Programmes</router-link></li>
								<li><router-link to="/plans">Plans</router-link></li>
							</ul>
							<a class="uk-navbar-toggle uk-hidden@m" uk-navbar-toggle-icon uk-toggle href="#off-menu"></a>
						</div>
					</div>
				</div>
			</nav>

			<!-- Menu off-canvas -->
			<div id="off-menu" uk-offcanvas="mode: push; overlay: true; bg-close: true;">
				<div class="uk-offcanvas-bar uk-nav-center uk-flex uk-flex-column">
					<button class="uk-offcanvas-close" type="button" uk-close></button>
					<ul class="uk-nav uk-nav-primary uk-nav-center uk-margin-auto-vertical uk-nav-parent-icon" onclick="UIkit.offcanvas('#off-menu').hide();">
						<li><router-link to="/groups">Groupes</router-link></li>
						<li><router-link to="/athletes">Athlètes</router-link></li>
						<li><router-link to="/programs">Programmes</router-link></li>
						<li><router-link to="/plans">Plans</router-link></li>
						<!--<li><router-link to="/sessions">Sessions</router-link></li>
						<li><router-link to="/documents">Documents</router-link></li>-->
					</ul>
					<div>
						<div class="uk-button-group" onclick="UIkit.offcanvas('#off-menu').hide();">
							<router-link class="uk-button uk-button-default uk-button-small" to="/about" >About</router-link>
							<router-link class="uk-button uk-button-default uk-button-small"  to="/qr">Qr code</router-link>
						</div>
					</div>
				</div>
			</div>
		</header>
		<main class="pr-content">
			<router-view/>
		</main>
		<footer class="pr-footer uk-background-muted uk-section uk-section-xsmall">
			<div class="pr-footer-above">
				<div class="uk-container"></div>
			</div>
			<div class="pr-footer-below">
				<div class="uk-container uk-text-center">
					<p><a href="https://creativecommons.org/licenses/by/4.0/" style="color:#000000"><i class="fab fa-creative-commons"></i><i class="fab fa-creative-commons-by"></i></a> {{trainerName}}<br>Made with <span style="color: #e25555;">&hearts;</span> in Switzerland — <router-link to="/about">About</router-link></p>
				</div>
			</div>
		</footer>
	</div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue';

export default {
  data() {
    return {
      trainerName: process.env.VUE_APP_TRAINER_NAME,
      backendUrl: process.env.VUE_APP_CRAFT_BACKEND_URL,
    };
  },
};
</script>

<style lang="scss">

</style>
