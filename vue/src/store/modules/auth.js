import axiosClient from "../../axios.js";

export default {
  namespaced: true,

  state: () => ({
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
  }),

  actions: {
    async register(context, user) {
      const response = await axiosClient.post("/register", user);

      context.commit("setUser", response.data);

      return response;
    },

    async login(context, user) {
      const response = await axiosClient.post("/login", user);

      context.commit("setUser", response.data);

      return response;
    },

    async logout(context) {
      const response = await axiosClient.post("/logout");

      context.commit("logout");

      return response;
    },
  },

  mutations: {
    setUser(state, userData) {
      state.user.data = userData.user;
      state.user.token = userData.token;

      sessionStorage.setItem("TOKEN", userData.token);
    },

    logout(state) {
      state.user.data = {};
      state.user.token = null;
      sessionStorage.removeItem("TOKEN");
    },
  },

  getters: {
    userData(state) {
      return state.user.user;
    },

    token(state) {
      return state.user.token;
    },
  },
};
