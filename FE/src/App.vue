<template>
  <SystemAlert />
  <component :is="layout" />

  <!-- ChatBox chỉ hiện ở Client -->
  <ChatBox v-if="$route.meta.layout === 'client'" />
</template>

<script>
import ChatBox from './components/Client/ChatBox/index.vue';
import SystemAlert from './components/SystemAlert.vue';

const default_layout = "client";

export default {
  components: {
    ChatBox,
    SystemAlert
  },
  computed: {
    layout() {
      return (this.$route.meta.layout || default_layout) + "-layout";
    },
  },
};
</script>

<style>
</style>