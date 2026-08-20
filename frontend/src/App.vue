<template>
  <div id="app">
    <router-view v-slot="{ Component }">
      <transition name="view" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style>
#app {
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
}

.view-enter-active,
.view-leave-active {
  transition: opacity 0.22s var(--ease), transform 0.22s var(--ease);
}

.view-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.view-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
