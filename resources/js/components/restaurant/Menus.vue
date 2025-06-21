<template>
  <div class="menus">
    <h2>Restaurant Menus</h2>
    <ul>
      <li v-for="item in menuItems" :key="item.id">
        <img :src="imageUrl(item.menu_image)" alt="Menu Image" />
        <div>
          <strong>{{ item.menu_name }}</strong>
          <p>{{ item.menu_description }}</p>
          <span>{{ item.recipe_type }}</span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const menuItems = ref([])

onMounted(async () => {
  try {
    const response = await axios.get('/api/recipes')
    menuItems.value = response.data
  } catch (error) {
    console.error('Failed to fetch menu:', error)
  }
})

function imageUrl(filename) {
  return `/storage/menus/${filename}` // adjust if your image path is different
}
</script>

<style scoped>
.menus {
  max-width: 600px;
  margin: 0 auto;
  font-family: Arial, sans-serif;
}
.menus h2 {
  text-align: center;
}
.menus ul {
  list-style: none;
  padding: 0;
}
.menus li {
  display: flex;
  gap: 10px;
  padding: 10px 0;
  border-bottom: 1px solid #ddd;
}
.menus img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}
</style>
