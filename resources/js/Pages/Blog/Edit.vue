<script setup lang="ts">
import { ref } from 'vue'
import type { Ref } from 'vue'
import axios from '../../bootstrap'
import { router } from '@inertiajs/vue3'

// Define component props
const props = defineProps<{ post: any }>()

const form = ref({
  title: '',
  content: '',
  category: '',
  published_at: '',
  image: null,
})

const errors = ref<Record<string, string[]>>({})
const isSubmitting = ref(false)

// Initialize form with props
if (props.post) {
  form.value.title = props.post.title
  form.value.content = props.post.content
  form.value.category = props.post.category
  form.value.published_at = props.post.published_at?.split('T')[0] ?? ''
}

const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) form.value.image = file
}

const submitForm = async () => {
  isSubmitting.value = true
  errors.value = {}

  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('content', form.value.content)
  formData.append('category', form.value.category)
  formData.append('published_at', form.value.published_at)
  if (form.value.image) formData.append('image', form.value.image)
  formData.append('_method', 'PUT')

  try {
    await axios.post(`/blog/${props.post.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    router.visit('/blog')
  } catch (err: unknown) {
    if (axios.isAxiosError(err)) {
      if (err.response?.status === 422) {
        errors.value = err.response?.data?.errors ?? {}
        return
      }
    }
    console.error(err)
    alert('Error updating post')
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

      <h1 class="mb-8 text-4xl font-bold">Edit Post</h1>

      <form @submit.prevent="submitForm" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-black mb-2">Title</label>
          <input v-model="form.title" type="text" class="w-full px-4 py-2 border rounded" />
        </div>

        <div>
          <label class="block text-sm font-medium text-black mb-2">Content</label>
          <textarea v-model="form.content" rows="10" class="w-full px-4 py-2 border rounded"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-black mb-2">Image</label>
          <input type="file" accept="image/*" @change="handleImageChange" />
        </div>

        <div class="flex gap-4">
          <button type="submit" :disabled="isSubmitting" class="px-6 py-2 bg-blue-600 text-white rounded">
            Save
          </button>
          <a href="/blog" class="px-6 py-2 border rounded">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</template>
