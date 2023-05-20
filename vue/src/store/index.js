import { createStore } from "vuex";
import authModule from "./modules/auth.js";
import surveyModule from "./modules/survey.js";
import dashboardModule from "./modules/dashboard.js";

const store = createStore({
  modules: {
    auth: authModule,
    surveys: surveyModule,
    dashboard: dashboardModule,
  },
  state: () => ({
    questionTypes: ["text", "select", "radio", "checkbox", "textarea"],
    notification: {
      show: false,
      type: null,
      message: null,
    },
  }),

  mutations: {
    notify: (state, { message, type }) => {
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;

      setTimeout(() => {
        state.notification.show = false;
      }, 3000);
    },
  },
  actions: {},
  getters: {},
});

export default store;
