<!-- The script setup section defines the props that the component accepts -->
<script setup lang="ts">
defineProps<{
  post: any
}>()
</script>

<!-- The template section defines the HTML structure and layout of the blog show page -->
<template>
  <div class="min-h-screen bg-white">
    <div class="mx-auto max-w-2xl px-4 py-12">
      <a href="/blog" class="mb-8 inline-block text-blue-600 hover:underline">
        ← Back to blog
      </a>

      <article>
        <h1 class="mb-4 text-4xl font-bold">{{ post.title }}</h1>
        
        <div class="mb-6 flex items-center justify-between text-gray-600">
          <time>{{ new Date(post.published_at).toLocaleDateString('it-IT') }}</time>
          <span class="rounded-full bg-gray-100 px-3 py-1 text-sm">{{ post.category }}</span>
        </div>

        <img 
          v-if="post.image" 
          :src="post.image" 
          :alt="post.title" 
          class="mb-8 w-full rounded-lg" 
        />

        <div class="prose max-w-none whitespace-pre-wrap text-gray-800">
          {{ post.content }}
        </div>
      </article>
    </div>
  </div>
</template>

export { default as show } from './show'
