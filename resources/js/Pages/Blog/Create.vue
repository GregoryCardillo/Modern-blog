<script setup lang="ts">
import { ref } from 'vue'
import type { Ref } from 'vue'
import axios from '../../bootstrap' // import of the bootstrap.ts file created in resources/js
import { router } from '@inertiajs/vue3'

// Define the structure of the blog form data
type BlogForm = {
  title: string
  content: string
  category: string
  image: File | null
  published_at: string
}

// Reactive form data
const form = ref<BlogForm>({
  title: '',
  content: '',
  category: 'News',
  image: null,
  published_at: new Date().toISOString().split('T')[0],
})

// Reactive variables for image preview and submission state
const imagePreview: Ref<string | ArrayBuffer | null> = ref(null)
const isSubmitting = ref(false)

// Validation errors returned from server
const errors = ref<Record<string, string[]>>({})

// Function to handle image file selection
const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    form.value.image = file
    // Shows image preview
    const reader = new FileReader()
    reader.onload = (ev) => {
      imagePreview.value = ev.target?.result ?? null
    }
    reader.readAsDataURL(file)
  }
}

// Function to submit the form using axios + Inertia
const submitForm = async () => {
  isSubmitting.value = true
  errors.value = {}

  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('content', form.value.content)
  formData.append('category', form.value.category)
  formData.append('published_at', form.value.published_at)
  if (form.value.image) formData.append('image', form.value.image)

  try {
    // axios configured in resources/js/bootstrap.ts (withCredentials + headers)
    const response = await axios.post('/blog', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    // On success, navigate with Inertia (SPA-friendly)
    router.visit('/blog')
  } catch (err: unknown) {
    // Axios error handling
    if (axios.isAxiosError(err)) {
      const status = err.response?.status

      if (status === 419) {
        // CSRF/session expired
        alert('Session expired or missing CSRF token. The page will be reloaded.')
        window.location.reload()
        return
      }

      if (status === 422) {
        // Validation errors
        errors.value = err.response?.data?.errors ?? {}
        // Optionally show first message
        const first = Object.values(errors.value)[0]?.[0]
        if (first) alert(first)
        return
      }

      const message = err.response?.data?.message
      if (message) {
        alert(message)
        return
      }
    }

    console.error('Error submitting form:', err)
    alert('An unexpected error occurred. Check the console for details.')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-white text-black">
    <div class="mx-auto max-w-2xl px-4 py-12">
      <a href="/blog" class="mb-8 inline-block text-blue-600 hover:underline">
        ← Back to blog
      </a>

      <h1 class="mb-8 text-4xl font-bold">Create New Post</h1>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-black mb-2">
            Title
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-black"
            placeholder="Post title"
          />
          <div v-if="errors.title" class="mt-1 text-sm text-red-600">
            <div v-for="(msg, idx) in errors.title" :key="idx">{{ msg }}</div>
          </div>
        </div>

        <!-- Category -->
        <div>
          <label for="category" class="block text-sm font-medium text-black mb-2">
            Category
          </label>
          <select
            id="category"
            v-model="form.category"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-black"
          >
            <option>News</option>
            <option>Technology</option>
            <option>Tutorial</option>
            <option>Other</option>
          </select>
          <div v-if="errors.category" class="mt-1 text-sm text-red-600">
            <div v-for="(msg, idx) in errors.category" :key="idx">{{ msg }}</div>
          </div>
        </div>

        <!-- Image -->
        <div>
          <label for="image" class="block text-sm font-medium text-black mb-2">
            Image
          </label>
          <input
            id="image"
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-black"
          />
          <div v-if="errors.image" class="mt-1 text-sm text-red-600">
            <div v-for="(msg, idx) in errors.image" :key="idx">{{ msg }}</div>
          </div>

          <!-- Image Preview -->
          <div v-if="imagePreview" class="mt-4">
            <img :src="imagePreview as string" alt="Preview" class="max-w-full h-auto rounded-lg max-h-64" />
          </div>
        </div>

        <!-- Content -->
        <div>
          <label for="content" class="block text-sm font-medium text-black mb-2">
            Content
          </label>
          <textarea
            id="content"
            v-model="form.content"
            required
            rows="12"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-black font-mono"
            placeholder="Write your post content here..."
          ></textarea>
          <div v-if="errors.content" class="mt-1 text-sm text-red-600">
            <div v-for="(msg, idx) in errors.content" :key="idx">{{ msg }}</div>
          </div>
        </div>

        <!-- Publish Date -->
        <div>
          <label for="published_at" class="block text-sm font-medium text-black mb-2">
            Publish Date
          </label>
          <input
            id="published_at"
            v-model="form.published_at"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-black"
          />
          <div v-if="errors.published_at" class="mt-1 text-sm text-red-600">
            <div v-for="(msg, idx) in errors.published_at" :key="idx">{{ msg }}</div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            {{ isSubmitting ? 'Creating...' : 'Create Post' }}
          </button>
          <a href="/blog" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-black">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</template>
