import axiosClient from "../../axios";

export default {
  namespaced: true,

  state: () => ({
    currentSurvey: {
      loading: false,
      data: {},
    },
    surveys: [
      {
        loading: false,
        links: [],
        data: [],
      },
    ],
  }),

  actions: {
    async getSurveys(context, { url = null } = {}) {
      url = url || "/surveys";

      context.commit("setSurveysLoading", true);

      let data = await axiosClient.get(url);

      context.commit("setSurveysLoading", false);

      context.commit("setSurveys", data.data);

      return data.data;
    },

    async getSurveyBySlug(context, slug) {
      context.commit("setCurrentSurveyLoading", true);

      try {
        let response = await axiosClient.get(`/survey-by-slug/${slug}`);

        context.commit("setCurrentSurvey", response.data);

        context.commit("setCurrentSurveyLoading", false);

        return response;
      } catch (error) {
        context.commit("setCurrentSurveyLoading", false);
        throw error;
      }
    },

    async saveSurvey(context, survey) {
      delete survey.imageUrl;
      try {
        context.commit("setCurrentSurveyLoading", true);

        let response;
        if (survey.id !== null) {
          response = await axiosClient.put(`/surveys/${survey.id}`, survey);
        } else {
          response = await axiosClient.post("/surveys", survey);
        }

        context.commit("setCurrentSurveyLoading", false);

        context.commit("setCurrentSurvey", response.data);

        return response;
      } catch (err) {
        context.commit("setCurrentSurveyLoading", false);
        throw err;
      }
    },

    getSurvey(context, id) {
      context.commit("setCurrentSurveyLoading", true);

      axiosClient
        .get(`surveys/${id}`)
        .then((result) => {
          context.commit("setCurrentSurvey", result.data);

          context.commit("setCurrentSurveyLoading", false);

          return result;
        })
        .catch((error) => {
          context.commit("setCurrentSurveyLoading", false);

          throw error;
        });
    },

    async deleteSurvey(context, id) {
      return await axiosClient.delete(`/surveys/${id}`);
    },

    async saveSurveyAnswers(context, { surveyId, answers }) {
      let response = await axiosClient.post(`surveys/${surveyId}/answer`, {
        answers,
      });

      return response;
    },
  },

  mutations: {
    setSurveysLoading(state, loading) {
      state.surveys.loading = loading;
    },

    setCurrentSurveyLoading(state, loading) {
      state.currentSurvey.loading = loading;
    },

    setSurveys(state, surveys) {
      state.surveys.links = surveys.meta.links;
      state.surveys.data = surveys.data;
    },

    setCurrentSurvey(state, survey) {
      state.currentSurvey.data = survey.data;
    },
  },
};
