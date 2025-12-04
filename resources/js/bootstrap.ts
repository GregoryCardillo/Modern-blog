import axios from 'axios';

// Send cookies (laravel_session / XSRF-TOKEN) on same-origin requests
axios.defaults.withCredentials = true;

// Read CSRF token from blade meta tag and set header
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

// Common headers for AJAX 
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

export default axios;