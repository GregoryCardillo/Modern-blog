<script setup lang="ts">
import { ref } from 'vue'
import type { Ref } from 'vue'

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

// Function to handle image file selection
const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    form.value.image = file
    
    // Shows image preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result ?? null
    }
    reader.readAsDataURL(file)
  }
}

// Function to submit the form
const submitForm = async () => {
  isSubmitting.value = true
  
  // FormData prepare for file and text upload 
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('content', form.value.content)
  formData.append('category', form.value.category)
  formData.append('published_at', form.value.published_at)
  
  // Append image file if exists
  if (form.value.image) {
    formData.append('image', form.value.image)
  }

  // Send POST request to server
  try {
    const response = await fetch('/blog', {
      method: 'POST',
      body: formData,
      // CSRF token for security
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    })

    // Handle response
    if (response.ok) {
      window.location.href = '/blog'
    } else {
      alert('Errore nella creazione del post')
    }

  // Catch network or other errors
  } catch (error) {
    console.error('Error:', error)
    alert('Errore nella richiesta')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<!-- Form creation -->
<template>
  <div class="min-h-screen bg-white">
    <div class="mx-auto max-w-2xl px-4 py-12">
      <a href="/blog" class="mb-8 inline-block text-blue-600 hover:underline">
        ← Back to blog
      </a>

      <h1 class="mb-8 text-4xl font-bold">Create New Post</h1>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Title
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Post title"
          />
        </div>

        <!-- Category -->
        <div>
          <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
            Category
          </label>
          <select
            id="category"
            v-model="form.category"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option>News</option>
            <option>Technology</option>
            <option>Tutorial</option>
            <option>Other</option>
          </select>
        </div>

        <!-- Image -->
        <div>
          <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
            Image
          </label>
          <input
            id="image"
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          />
          
          <!-- Image Preview -->
          <div v-if="imagePreview" class="mt-4">
            <img :src="imagePreview as string" alt="Preview" class="max-w-full h-auto rounded-lg max-h-64" />
          </div>
        </div>

        <!-- Content -->
        <div>
          <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Content
          </label>
          <textarea
            id="content"
            v-model="form.content"
            required
            rows="12"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
            placeholder="Write your post content here..."
          />
        </div>

        <!-- Publish Date -->
        <div>
          <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
            Publish Date
          </label>
          <input
            id="published_at"
            v-model="form.published_at"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
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
          <a href="/blog" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</template>