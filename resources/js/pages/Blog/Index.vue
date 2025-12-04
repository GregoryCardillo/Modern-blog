<!-- The script setup section defines the props that the component accepts -->
<script setup lang="ts">
defineProps<{
  posts: any
}>();
</script>

<!-- The template section defines the HTML structure and layout of the blog index page --> 
<template>
  <div class="min-h-screen bg-white">
    <div class="mx-auto max-w-4xl px-4 py-12">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="mb-4 text-4xl font-bold">Blog</h1>
          <p class="text-gray-600">Latest news and articles</p>
        </div>
        <a 
          href="/blog/create" 
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + New Post
        </a>
      </div>

      <!-- ...existing code per i post... -->
      <div class="space-y-8">
        <article 
          v-for="post in posts.data" 
          :key="post.id"
          class="border-b pb-8 last:border-b-0"
        >
          <h2 class="mb-2 text-2xl font-bold">
            <a :href="`/blog/${post.slug}`" class="hover:text-blue-600">
              {{ post.title }}
            </a>
          </h2>
          <div class="mb-4 text-sm text-gray-500">
            {{ new Date(post.published_at).toLocaleDateString('it-IT') }}
            <span class="mx-2">•</span>
            {{ post.category }}
          </div>
          <p class="text-gray-700">{{ post.content.substring(0, 200) }}...</p>
          <a :href="`/blog/${post.slug}`" class="mt-4 inline-block text-blue-600 hover:underline">
            Read more →
          </a>
        </article>
      </div>

        <!-- Pagination --> 
      <div v-if="posts.links" class="mt-12 flex justify-center gap-2">
        <a 
          v-for="link in posts.links"
          :key="link.label"
          :href="link.url || '#'"
          :class="{
            'px-4 py-2 border rounded': true,
            'bg-blue-600 text-white': link.active,
            'text-gray-500 cursor-not-allowed': !link.url
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </div>
</template>