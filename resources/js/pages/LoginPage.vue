<template>
  <div class="login-page">
    <AppHeader />
    <main>
      <h1>ログイン</h1>
      <form>
        <label>メールアドレス<input type="email" /></label>
        <label>パスワード<input type="password" /></label>
        <button type="submit">ログイン</button>
      </form>
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from '../components/layout/AppHeader.vue'
import AppFooter from '../components/layout/AppFooter.vue'
</script>

<style scoped>
.login-page { padding: 1rem; }
form { display:flex; flex-direction:column; gap:0.5rem; max-width:320px; }
</style>
