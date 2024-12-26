<script setup>
import Navbar from "@/Components/Dashboard/Navbar.vue";
import Sidebar from "@/Components/Dashboard/Sidebar.vue";
import Footer from "@/Components/Dashboard/Footer.vue";
import { defineProps, onMounted } from "vue";
import { initFlowbite } from "flowbite";

localStorage.setItem("darkMode", true);

onMounted(() => {
  initFlowbite();
});
</script>

<template>
  <div>
    <Navbar />
    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
      <Sidebar />
      <div
        class="fixed inset-0 z-10 hidden bg-gray-900/50 dark:bg-gray-900/90"
        id="sidebarBackdrop"
      ></div>

      <div
        id="main-content"
        class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900"
      >
        <slot />
        <Footer />
      </div>
    </div>
  </div>
</template>
