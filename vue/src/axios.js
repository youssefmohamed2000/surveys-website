import axios from "axios";
import store from "./store";

const axiosClient = axios.create({
  baseURL: "http://laravel-vue-survey.test/api",
});

axiosClient.interceptors.request.use((config) => {
  config.headers.Authorization = `Bearer ${store.getters["auth/token"]}`;
  return config;
});

export default axiosClient;
