<template>
  <div class="uk-section uk-container uk-container-small pr-about">
    <h1>About</h1>
    <p>This application is build with <a href="https://github.com/michaelravedoni/prathletics">Prathletics</a> a project under the <a href="https://choosealicense.com/licenses/mit/">MIT</a>. The main contributor is <a href="https://github.com/michaelravedoni">Michael Ravedoni</a>. All content is licensed under <a href="https://creativecommons.org/licenses/by/4.0/">CC BY 4.0</a>.</p>
    <p>We’re really happy to accept contributions from the community, that’s the main reason why we open-sourced it! There are many ways to contribute, even if you’re not a technical person. <a href="https://github.com/michaelravedoni/prathletics#contributing">→ Contribute</a></p>
    <button class="uk-button uk-button-default uk-button-small" type="button" onclick="localStorage.clear();sessionStorage.clear();UIkit.notification({message: '&#10004; Storage cleared', pos: 'top-center', timeout: '2000', status:'success'});">Clear storage</button>
    <button class="uk-button uk-button-default uk-button-small" type="button" onclick="location.reload(true);">Refresh</button>
  </div>
</template>
