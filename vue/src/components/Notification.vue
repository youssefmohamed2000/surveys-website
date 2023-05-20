<template>
  <div
    v-if="notification.show"
    class="fixed w-[300px] right-4 top-[70px] py-2 px-4 text-white animate-fade-in-down rounded flex justify-between items-center"
    :class="[notification.type === 'success' ? 'bg-emerald-500' : 'bg-red-500']"
  >
    {{ notification.message }}
    <button @click="removeNotification">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6 hover:scale-110"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from "vue";
import store from "../store";

const notification = computed(() => store.state.notification);

function removeNotification() {
  store.state.notification.show = false;
}
</script>
