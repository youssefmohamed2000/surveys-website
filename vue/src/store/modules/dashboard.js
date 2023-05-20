import axiosClient from "../../axios";

export default {
  namespaced: true,

  state: () => ({
    dashboard: {
      loading: false,
      data: {},
    },
  }),

  actions: {
    async getDashboardData(context) {
      try {
        context.commit("setDashboardLoading", true);
        const response = await axiosClient.get("/dashboard");

        context.commit("setDashboardLoading", false);

        context.commit("setDashboardData", response.data);

        return response;
      } catch (error) {
        context.commit("setDashboardLoading", false);
        throw error;
      }
    },
  },

  mutations: {
    setDashboardLoading(state, loading) {
      state.dashboard.loading = loading;
    },

    setDashboardData(state, dashboardData) {
      state.dashboard.data = dashboardData;
    },
  },

  getters: {},
};
