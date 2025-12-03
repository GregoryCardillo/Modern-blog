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