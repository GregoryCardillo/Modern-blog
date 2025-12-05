<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps<{ posts: any }>()

const page = usePage()
const user = (page.props as any).auth?.user
const posts = (page.props as any).posts || { data: [] }

const deletePost = (post: any) => {
  if (!confirm('Delete post?')) return
  Inertia.delete(`/blog/${post.id}`, { onSuccess: () => Inertia.visit(route('admin.dashboard')) })
}
</script>

<template>
  <div class="min-h-screen bg-white">
    <div class="mx-auto max-w-4xl px-4 py-12">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>
        <div>
          <span class="text-sm text-gray-600">Signed in as {{ user?.name }}</span>
        </div>
      </div>

      <a v-if="user?.role === 'admin'" href="/blog/create" class="mb-6 inline-block px-3 py-2 bg-blue-600 text-white rounded">New Post</a>

      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="text-left">
            <th class="px-2 py-2">Title</th>
            <th class="px-2 py-2">Author</th>
            <th class="px-2 py-2">Published</th>
            <th class="px-2 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in posts.data" :key="post.id" class="border-t">
            <td class="px-2 py-3"><a :href="`/blog/${post.slug}`" class="text-blue-600 hover:underline">{{ post.title }}</a></td>
            <td class="px-2 py-3">{{ post.user?.name ?? '—' }}</td>
            <td class="px-2 py-3">{{ post.published_at }}</td>
            <td class="px-2 py-3">
              <a v-if="post.can.update" :href="`/blog/${post.id}/edit`" class="mr-2 text-yellow-600">Edit</a>
              <button v-if="post.can.delete" @click.prevent="deletePost(post)" class="text-red-600">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="mt-6">
        <nav v-if="posts.links" class="flex gap-2">
          <a v-for="link in posts.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="px-3 py-1 border rounded"/>
        </nav>
      </div>
    </div>
  </div>
</template>
