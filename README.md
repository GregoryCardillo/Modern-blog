# Modern Blog (Learning Project)

A small Laravel-based blog application with Vue.js in the frontend, created to review and practice core Laravel concepts and modern full-stack patterns .

Summary
- Purpose: built as a hands-on exercise to review routing, authentication, authorization, middleware, controllers, testing, and Inertia-driven frontends.
- Scope: a simple public blog with an admin management UI, user profiles, and full authentication flows.

Features
- Authentication
  - Register, login, logout
  - Password reset (email) and new-password flow
  - Email verification
  - Confirmable password for sensitive actions
- Two-Factor Authentication (optional, via Laravel Fortify)
  - Two-factor challenge page
  - Encrypted two-factor secret and recovery codes
  - Recovery codes and confirmation flow
  - Feature tests for two-factor challenge behavior
- User Profile
  - Edit, update, and delete user profile
- Blog
  - Public list of posts (index) and public show (by slug)
  - Admin-only management: create, edit, update, delete posts
  - Admin dashboard under /admin with post management
- Routing and Middleware
  - Clear route separation for public and protected/admin routes
  - Middleware examples: auth, verified, custom admin middleware
- Frontend
  - Inertia.js used to render pages (single-page experience driven from Laravel)
- Testing
  - PHPUnit feature tests included for authentication-related flows
- Console
  - Artisan commands and examples present

Tech stack
- PHP (Laravel framework)
- Vue for the frontend UI
- Inertia.js for frontend rendering
- Laravel Fortify for authentication features (2FA, password reset, etc.)
- PHPUnit for automated tests

How to run (quick)
1. Clone the repo.
2. Install PHP dependencies: composer install
3. Copy .env.example to .env and configure DB and mail settings.
4. Generate app key: php artisan key:generate
5. Run migrations and seeders: php artisan migrate --seed
6. Serve the app: php artisan serve
7. Run tests: ./vendor/bin/phpunit

What I practiced / learned
- Designing route groups and naming routes for public, auth-protected, and admin-only areas.
- Implementing authentication flows and integrating optional two-factor authentication with encrypted secrets and recovery codes.
- Using Inertia to return frontend pages from controllers.
- Building CRUD controllers and protecting them with middleware and authorization.
- Writing feature tests for auth-related behavior.
- Working with Laravel controllers, form requests, and Eloquent for model operations.

Notes & future improvements
- Add role management beyond a simple admin middleware.
- Improve frontend UI 
- Expand test coverage for CRUD operations and admin flows.
- Add API endpoints and token-based auth if needed.

