
<template>
  <div class="home-container">
    <h1 class="text-2xl font-bold">Welcome to Home Page Lee</h1>
    <p class="text-gray-600">This is the home page of my application.</p>
  </div>
</template>

<script>
export default {
  name: 'HomeIndex',
  data() {
    return {
      message: 'Hello, Vue!'
    };
  },
  mounted() {
    console.log(this.message);
  }
};
</script>

<style scoped>
.home-container {
  padding: 20px;
}
</style>
