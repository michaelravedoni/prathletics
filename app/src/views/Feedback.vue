<template>
  <div class="uk-section uk-container">
    <!-- If you want to show survey, uncomment the line below -->
    <!-- <survey :survey="survey"></survey> -->
    <survey :survey="survey"></survey>
    <!-- If you want to show survey editor, uncomment the line below -->
    <!-- <survey-editor></survey-editor> -->
  </div>
</template>

<script>
//In your VueJS App.vue or yourComponent.vue file add these lines to import
import * as SurveyVue from 'survey-vue'

var Survey = SurveyVue.Survey
Survey.cssType = "bootstrap";

//If you want to add custom widgets package
//Add these imports
import * as widgets from "surveyjs-widgets";
//And initialize widgets you are want ti use
widgets.icheck(SurveyVue);
widgets.select2(SurveyVue);
widgets.inputmask(SurveyVue);
widgets.jquerybarrating(SurveyVue);
widgets.jqueryuidatepicker(SurveyVue);
widgets.nouislider(SurveyVue);
widgets.select2tagbox(SurveyVue);
widgets.signaturepad(SurveyVue);
widgets.sortablejs(SurveyVue);
widgets.ckeditor(SurveyVue);
widgets.autocomplete(SurveyVue);
widgets.bootstrapslider(SurveyVue);

var json = {
  locale: "fr",
  pages: [
    {
      name: "page1",
      elements: [
        {
          type: "dropdown",
          name: "feedbackAthlete",
          title: {
            default: "Athlète",
            en: "Athlete"
          },
          choices: [
            "item1",
            "item2",
            "item3"
          ],
          choicesByUrl: {
            url: "https://ravedoni.com/test/prathletics/cms/web/athletes.json",
            path: "data",
            valueName: "id",
            titleName: "title"
          }
        },
        {
          type: "rating",
          name: "feelGlobal",
          title: {
            default: "Ressenti général",
            en: "General feeling"
          },
          description: "Entre 1 et 10, ressenti global du jour (fatigue, douleur, humeur…)",
          rateMax: 10
        },
        {
          type: "rating",
          name: "feelSpecificStatic",
          title: {
            default: "Ressenti de la douleur statique",
            en: "Stativ feeling"
          },
          description: "Entre 1 et 10, ressenti de la douleur de la zone blessée au repos (marche, debout, assis, dans le lit)",
          rateMax: 10
        },
        {
          type: "rating",
          name: "feelSpecificDynamic",
          title: {
            default: "Ressenti de la douleur dynamique",
            en: "Dynamic feeling"
          },
          description: "Entre 1 et 10, ressenti de la douleur de la zone blessée en mouvement spécifique (exercices, mouvements spécifiques, marche/course, etc.)",
          rateMax: 10
        },
        {
          type: "comment",
          name: "feedbackComment",
          title: {
            default: "Commentaire",
            en: "Comment"
          }
        }
      ]
    }
  ],
  completeText: {
    fr: "Envoyer"
  },
  isSinglePage: true
};

var survey = new SurveyVue.Model(json)
survey.locale = document.documentElement.lang;
var results = '';

survey.onComplete.add(function (result) {
  console.log(result.data);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "http://home.localhost:8888/prathletics/cms/web/actions/prathletics/feedback/add-feedback");
  xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");
  xhr.send(JSON.stringify(result.data));
});

export default {
  name: 'feedback',
  components: {
    Survey,
  },
  data () {
    return {
      survey: survey,
      results,
    }
  },
  methods: {
    resultsData() {
      console.log('resultsData launched')
    }
  },
}
</script>

<style>
.feedback .sv_bootstrap_css .btn.btn-default.btn-secondary.active {
  background-color: #116cf3;
  color: #ffffff;
  border-color: #116cf3;
}
@media (max-width:900px) {
  .feedback .sv_bootstrap_css .btn {
    padding: 0 1rem;
  }
  .sv_bootstrap_css .sv_qstn {
    padding: .5rem 0;
  }
}
@media (max-width:600px) {
  .feedback .sv_bootstrap_css .btn {
    padding: 0 .8rem;
  }
}

@media (max-width:400px) {
  .feedback .sv_bootstrap_css .btn {
    padding: 0 .5rem;
  }
}
</style>
