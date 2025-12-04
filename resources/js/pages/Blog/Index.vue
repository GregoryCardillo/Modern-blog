<!-- The script setup section defines the props that the component accepts -->
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
// optional: keep axios import if you want to use it elsewhere
import axios from '../../bootstrap'

defineProps<{ posts: any }>()

const page = usePage()

// Optional chaining (?.) allows safely accessing nested properties without
// throwing errors if something is undefined. 
// Double bang (!!) converts any truthy/falsy value into a strict boolean.

// Function to check if the current user can edit a post
const canEdit = (post: any) => {
  const user = (page.props as any).auth?.user
  if (post?.can?.update !== undefined) return !!post.can.update
  if (!user) return false
  return post.user_id === user.id
}

const canDelete = (post: any) => {
  const user = (page.props as any).auth?.user
  if (post?.can?.delete !== undefined) return !!post.can.delete
  if (!user) return false
  return post.user_id === user.id
}

interface PostCan {
  update?: boolean
  delete?: boolean
}

interface Post {
  id: number
  slug?: string
  user_id?: number
  can?: PostCan
  [key: string]: any
}

const deletePost = (post: Post): void => {
  if (!confirm('Sei sicuro di voler cancellare questo post?')) return

  // use slug if your public routes use {post:slug}
  const identifier: string | number = post.slug ?? post.id

  // use Inertia.delete: keeps SPA behavior and works with Inertia middleware
  Inertia.delete(`/blog/${identifier}`, {
    onSuccess: () => {
      // optional: Inertia refreshes automatically, but force a visit to the list
      Inertia.visit('/blog')
    },
    onError: (errors: Record<string, any>) => {
      console.error('Delete error', errors)
      alert('Errore nella cancellazione del post.')
    }
  })
}
</script>

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
          <div class="mt-4 flex items-center gap-4">
            <a :href="`/blog/${post.slug}`" class="inline-block text-blue-600 hover:underline">
              Read more →
            </a>

            <!-- Edit / Delete only if authorized -->
            <div class="ml-auto flex gap-2" v-if="canEdit(post) || canDelete(post)">
              <a
                v-if="canEdit(post)"
                :href="`/blog/${post.id}/edit`"
                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
              >
                Edit
              </a>

              <button
                v-if="canDelete(post)"
                @click.prevent="deletePost(post)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Delete
              </button>
            </div>
          </div>
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