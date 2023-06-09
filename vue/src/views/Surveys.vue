<template>
  <div>
    <page-component>
      <template v-slot:header>
        <div class="flex items-center justify-between">
          <h1 class="text-3xl font-bold text-gray-900">Surveys</h1>
          <router-link
            :to="{ name: 'SurveyCreate' }"
            class="py-2 px-3 text-white bg-emerald-500 rounded-md hover:bg-emerald-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 -mt-1 inline-block"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 4.5v15m7.5-7.5h-15"
              />
            </svg>

            Add new Survey
          </router-link>
        </div>
      </template>
      <loading v-if="surveys.loading"></loading>
      <div v-else>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
          <survey-list-item
            v-for="(survey, index) in surveys.data"
            :key="survey.id"
            :survey="survey"
            class="opacity-0 animate-fade-in-down"
            :style="{ animationDelay: `${index * 0.1}s` }"
            @delete="deleteSurvey(survey.id)"
          ></survey-list-item>
        </div>

        <div class="flex justify-center mt-5">
          <nav
            class="relative z-0 inline-flex justify-center rounded-md shadow-md"
            aria-label="Pagination"
          >
            <a
              v-for="(link, index) in surveys.links"
              :key="index"
              :disabled="!link.url"
              href="#"
              @click="getForPage($event, link)"
              aria-current="page"
              v-html="link.label"
              class="relative inline-flex items-center px-4 py-2 border text-sm"
              :class="[
                link.active
                  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                index === 0 ? 'rounded-s-md' : '',
                index === surveys.links.length - 1 ? 'rounded-e-md' : '',
              ]"
            >
            </a>
          </nav>
        </div>
      </div>
    </page-component>
  </div>
</template>

<script setup>
import { computed } from "vue";
import PageComponent from "../components/PageComponent.vue";
import Loading from "../components/Loading.vue";
import SurveyListItem from "../components/SurveyListItem.vue";
import store from "../store";

const surveys = computed(() => store.state.surveys.surveys);

store.dispatch("surveys/getSurveys");

function deleteSurvey(surveyId) {
  if (confirm("Are you sure you want to delete this survey?")) {
    store.dispatch("surveys/deleteSurvey", surveyId);
    store.dispatch("surveys/getSurveys");
  }
}

function getForPage(event, link) {
  event.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  store.dispatch("surveys/getSurveys", { url: link.url });
}
</script>
