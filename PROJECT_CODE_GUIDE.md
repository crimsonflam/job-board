# Job Board — Code Guide (Important Files & Block-by-Block Explanation)

> A study/reference guide for this job-board application.
> It explains **what every file is for**, **which files are the important ones (and why)**, and for the
> most important files it walks through the code **block by block, line by line, word by word**.
>
> Nothing in the codebase was changed to produce this document — it is read-only documentation.
> **This guide was regenerated after a full re-scan** and reflects the CURRENT architecture.

---

## Table of Contents

- [⚠️ Important: the architecture changed](#arch-changed)
- [1. What this application is](#1-what-this-application-is)
- [2. How the app works (the two request flows)](#2-how-the-app-works-the-two-request-flows)
- [3. The IMPORTANT files (and why)](#3-the-important-files-and-why)
- [4. Every file's purpose (full inventory)](#4-every-files-purpose-full-inventory)
- [5. Block-by-block code walkthrough of the important files](#5-block-by-block-code-walkthrough-of-the-important-files)
  - 5.1 [`public/index.php` — the entry point](#51-publicindexphp--the-entry-point-unchanged)
  - 5.2 [`bootstrap/app.php` — application wiring](#52-bootstrapappphp--application-wiring-changed)
  - 5.3 [`routes/web.php` — the SPA shell catch-all](#53-routeswebphp--the-spa-shell-catch-all-changed)
  - 5.4 [`routes/api.php` — the API map](#54-routesapiphp--the-api-map-new--the-most-important-routing-file)
  - 5.5 [`app/Http/Middleware/RoleMiddleware.php` — the security gate](#55-apphttpmiddlewarerolemiddlewarephp--the-security-gate-unchanged)
  - 5.6 [`app/Models/User.php` — the central account model](#56-appmodelsuserphp--the-central-account-model-unchanged)
  - 5.7 [`app/Models/JobListing.php` — the job domain object](#57-appmodelsjoblistingphp--the-job-domain-object-unchanged)
  - 5.8 [The API Resources — `app/Http/Resources/*`](#58-the-api-resources--apphttpresources-new-the-json-contract)
  - 5.9 [`Api/AuthController.php` — auth backbone](#59-apphttpcontrollersapiauthcontrollerphp--auth-backbone-new)
  - 5.10 [`Api/JobController.php` — public browsing](#510-apphttpcontrollersapijobcontrollerphp--public-browsing-new)
  - 5.11 [`Api/Employer/JobListingController.php` — the biggest controller](#511-apphttpcontrollersapiemployerjoblistingcontrollerphp--the-biggest-controller-new-11-port)
  - 5.12 [`Api/Seeker/ApplicationController.php` — the apply flow](#512-apphttpcontrollersapiseekerapplicationcontrollerphp--the-apply-flow-new)
  - 5.13 [Admin controllers](#513-admin-controllers-new)
  - 5.14 [`resources/views/app.blade.php` — the SPA shell](#514-resourcesviewsappbladephp--the-spa-shell-new)
  - 5.15 [`main.jsx` + `App.jsx` — the React entry & router](#515-resourcesjsmainjsx--appjsx--the-react-entry--router-new)
  - 5.16 [`api/client.js` + `api/index.js` — the service layer](#516-resourcesjsapiclientjs--apiindexjs--the-service-layer-new)
  - 5.17 [`AuthContext.jsx` + `guards.jsx`](#517-resourcesjscontextsauthcontextjsx--componentsguardsjsx-new)
  - 5.18 [`JobsIndex.jsx` — a representative page](#518-resourcesjspagesjobsjobsindexjsx--a-representative-page-new)
  - 5.19 [Database migrations — the schema](#519-database-migrations--the-schema-unchanged)
  - 5.20 [`config/morocco.php` — the city source of truth](#520-configmoroccophp--the-city-source-of-truth-unchanged)
  - 5.21 [`database/seeders/DatabaseSeeder.php` — demo data](#521-databaseseedersdatabaseseederphp--demo-data-unchanged)
- [6. Quick reference — where to look for X](#6-quick-reference--where-to-look-for-x)

---

<a id="arch-changed"></a>

## ⚠️ Important: the architecture changed

This project used to be a classic **server-rendered Laravel app** (Blade `.blade.php` views, one controller per page). It has since been **rebuilt as a React Single-Page Application (SPA) with a Laravel JSON API backend.**

| Layer | Before (old) | Now (current) |
|-------|--------------|---------------|
| Frontend | Blade templates (`resources/views/**`) | **React** components (`resources/js/**`) |
| Routing (pages) | `routes/web.php` → controllers → views | **React Router** in the browser (`resources/js/App.jsx`) |
| Data | Controllers returned rendered HTML | **JSON API** (`routes/api.php` → `app/Http/Controllers/Api/**`) |
| Output shaping | Blade + model helper methods | **API Resources** (`app/Http/Resources/**`) turn models into JSON |
| Styling | Tailwind via CDN (configured inline in the layout) | **Tailwind v4 compiled by Vite** (`resources/css/app.css`) |
| Auth | Session + Blade forms | **Session/cookie auth over the same-origin API** (axios) |

What did **not** change (the business core is identical): the **Eloquent models**, the **`RoleMiddleware`**, the **database migrations**, the **`config/morocco.php`** city list, and the **seeder**. The new API controllers are described in their own comments as "1:1 ports" of the old ones — same validation rules, same authorization, same behaviour — they just return JSON instead of HTML.

---

## 1. What this application is

A **job board** for the Moroccan market with three kinds of users:

| Role | What they do |
|------|--------------|
| **Seeker** | Browses/saves jobs, uploads a CV, applies, tracks responses. |
| **Employer** | Sets up a company profile, posts jobs, manages applicants, accepts/rejects with a message. |
| **Admin / Super Admin** | Moderates the platform — stats, deletes jobs, deactivates/reactivates users. |

Key design decisions (unchanged from the original):
- **One `users` table for everyone** — seeker/employer/admin/super_admin are rows distinguished by a `role` column. Employer "company" fields and seeker "CV/profile" fields are nullable columns on that same row (no separate `companies` table).
- **Morocco-focused** — locations come from a fixed city list; salaries are in **MAD**.
- **Role-based access control (RBAC)** enforced by `RoleMiddleware` on the API **plus** per-action ownership/permission checks in controllers. The React route guards are *convenience only* — the server is the source of truth.

The "MOD N" comments scattered in the code are a historical changelog of features added/removed during development (e.g. *MOD 1: featured jobs removed*, *MOD 19: messaging removed*).

---

## 2. How the app works (the two request flows)

Because it is now an SPA, there are **two distinct flows**:

### Flow A — First page load (gets the HTML shell)
```
Browser → public/index.php → bootstrap/app.php → routes/web.php
        → returns resources/views/app.blade.php  (an almost-empty HTML shell)
        → the shell loads the compiled React bundle (Vite) → React boots
```
The Blade catch-all route returns the **same shell** for every non-API URL. React Router then decides which "page" to show on the client.

### Flow B — Data (JSON, after React is running)
```
React component → axios (resources/js/api) → /api/... 
   → routes/api.php → an API Controller → Eloquent Model → Database
   → API Resource turns the model into JSON → back to React → rendered
```
Auth is **session + cookie** based and same-origin, so the API lives under Laravel's `web` middleware group (cookies + CSRF), not a stateless token group.

---

## 3. The IMPORTANT files (and why)

Read these first; everything else is detail.

| # | File | Why it's important |
|---|------|--------------------|
| 1 | `routes/api.php` | The **API map** — every data endpoint the React app can call, with the middleware that guards it. The real "what can this app do" list. |
| 2 | `resources/js/App.jsx` | The **client-side router** — the React equivalent of a routes file. Maps browser URLs to page components and applies role guards. |
| 3 | `bootstrap/app.php` | Wires routing, **registers the API under the `web` group**, and aliases the `role` middleware. |
| 4 | `app/Http/Middleware/RoleMiddleware.php` | The **security gate** — restricts API route groups to specific roles. |
| 5 | `app/Models/User.php` | The **central model** — roles, the active/deactivated gate, the `canManage()` admin hierarchy, and relationships. |
| 6 | `app/Models/JobListing.php` | The **core domain object** — allowed values, the search/filter query logic, display helpers. |
| 7 | `app/Http/Resources/*` | The **JSON contract** — how models are shaped for the frontend (and what is deliberately hidden). |
| 8 | `app/Http/Controllers/Api/AuthController.php` | Login/register/logout/me — the auth backbone the whole SPA depends on. |
| 9 | `app/Http/Controllers/Api/Employer/JobListingController.php` | The **biggest controller** — job CRUD, applicant management, accept/reject, validation. |
| 10 | `resources/js/api/client.js` + `api/index.js` | The **service layer** — the axios setup (CSRF, error/flash handling) and one function per endpoint. |
| 11 | `resources/js/contexts/AuthContext.jsx` | Holds the logged-in user for the whole SPA; powers the guards and navbar. |
| 12 | `resources/views/app.blade.php` | The **SPA shell** — the only HTML Laravel renders; bootstraps React, CSRF, and the base path. |
| 13 | `database/migrations/*` | The **database schema** — the source of truth for columns and constraints (unchanged). |
| 14 | `config/morocco.php` | Single source of truth for the **city list** (unchanged). |
| 15 | `database/seeders/DatabaseSeeder.php` | The **demo data** + login accounts (unchanged). |

---

## 4. Every file's purpose (full inventory)

### Backend — `app/`

**API Controllers — `app/Http/Controllers/Api/`** (return JSON, consumed by React)

| File | Purpose |
|------|---------|
| `AuthController.php` | Register, login, logout, and `me` (who-am-I). |
| `JobController.php` | **Public** job browsing — list + single job by slug. |
| `MetaController.php` | Shared option lists (categories, cities, type/experience/education labels) for filters & forms. |
| `Seeker/DashboardController.php` | Seeker dashboard data. |
| `Seeker/ProfileController.php` | Seeker profile + CV upload/download/delete. |
| `Seeker/ApplicationController.php` | Apply to a job + "My Applications" list/detail. |
| `Seeker/SavedJobController.php` | Save/unsave toggle + saved-jobs list. |
| `Employer/DashboardController.php` | Employer dashboard stats. |
| `Employer/CompanyProfileController.php` | Employer company profile show/update. |
| `Employer/JobListingController.php` | Job CRUD + applicants + accept/reject + CV download. |
| `Admin/DashboardController.php` | Platform-wide stats. |
| `Admin/JobController.php` | Admin job list + delete. |
| `Admin/UserController.php` | Admin user list + deactivate/activate. |

**API Resources — `app/Http/Resources/`** (transform Eloquent models → JSON)

| File | Purpose |
|------|---------|
| `UserResource.php` | Shapes a user (role booleans, profile/company/CV fields; never password). |
| `JobListingResource.php` | Shapes a job (pre-computed labels, public company info, seeker flags). |
| `ApplicationResource.php` | Shapes an application (CV snapshot, status label/colour, response). |
| `CategoryResource.php` | Shapes a category (+ optional published-job count). |

**Models — `app/Models/`** (one class per table — unchanged)

| File | Purpose |
|------|---------|
| `User.php` | Every account. |
| `JobListing.php` | A job posting. |
| `Application.php` | A seeker's application to one job. |
| `SavedJob.php` | A bookmark (seeker ↔ job). |
| `Category.php` | A job category. |

**Other backend**

| File | Purpose |
|------|---------|
| `app/Http/Middleware/RoleMiddleware.php` | Role-gating middleware (`role` alias). |
| `app/Http/Controllers/Controller.php` | Empty base controller. |
| `app/Providers/AppServiceProvider.php` | Default provider (empty). |

> Note: the old `app/Http/Controllers/Auth/*`, `DashboardController.php`, `HomeController.php`, `JobController.php`, and the `Seeker/Employer/Admin` web controllers were **deleted** — replaced by the `Api/**` controllers above.

### Routing & bootstrap

| File | Purpose |
|------|---------|
| `routes/api.php` | **All JSON API endpoints** (registered under `/api`). |
| `routes/web.php` | A single catch-all returning the SPA shell (excludes `/api` and `/up`). |
| `routes/console.php` | Custom artisan commands. |
| `bootstrap/app.php` | App wiring; registers the API + the `role` alias. |
| `bootstrap/providers.php` | Service-provider list. |
| `public/index.php` | The single HTTP entry point. |
| `public/.htaccess` | Apache rewrites → `index.php`. |

### Frontend — `resources/`

**App shell & build**

| File | Purpose |
|------|---------|
| `resources/views/app.blade.php` | The HTML shell that boots React (CSRF, base path, Vite assets). |
| `resources/css/app.css` | Tailwind v4 import + the crimson/Poppins theme. |
| `vite.config.js` | Vite build config (React + Tailwind + Laravel plugins). |
| `package.json` | JS dependencies (React, React Router, axios) + scripts. |

**React core — `resources/js/`**

| File | Purpose |
|------|---------|
| `main.jsx` | Mounts the React app into `#root`. |
| `App.jsx` | Providers + React Router route table + guards. |
| `config.js` | Reads the base path (for subfolder hosting). |
| `utils.js` | Small helpers (e.g. `cleanParams`). |
| `api/client.js` | The axios instance + response interceptor (CSRF, flash, 401). |
| `api/index.js` | The service layer — one function per endpoint, grouped by domain. |
| `contexts/AuthContext.jsx` | Global logged-in-user state. |
| `contexts/FlashContext.jsx` | Global toast/flash-message state. |
| `hooks/useMeta.js` | Loads & caches the `/api/meta` option lists. |
| `components/guards.jsx` | Route guards: `RequireRole`, `RequireGuest`, `FullPageLoader`. |
| `components/Layout.jsx` | App chrome — navbar, flash banners, footer. |
| `components/JobCard.jsx` | A job "card" (with the save heart). |
| `components/JobFilters.jsx` | The Browse filter sidebar. |
| `components/Modal.jsx`, `Pagination.jsx`, `Badge.jsx`, `StatCard.jsx` | Reusable UI pieces. |
| `components/employer/JobForm.jsx` | The shared create/edit job form. |
| `pages/Welcome.jsx`, `Login.jsx`, `Register.jsx` | Public / auth pages. |
| `pages/jobs/JobsIndex.jsx`, `JobShow.jsx` | Browse + job detail pages. |
| `pages/seeker/*` | Seeker dashboard, profile, applications, saved jobs. |
| `pages/employer/*` | Employer dashboard, company, jobs, applicants. |
| `pages/admin/*` | Admin dashboard, jobs, users. |

### Database & config

| File | Purpose |
|------|---------|
| `database/migrations/*` | Table schemas (users, job_listings, applications, saved_jobs, categories, + framework tables). |
| `database/seeders/DatabaseSeeder.php` | Demo categories, users, jobs, applications, saved jobs. |
| `database/factories/UserFactory.php` | Fake users for tests. |
| `config/morocco.php` | The Moroccan city list (custom). |
| `config/*.php` | Standard Laravel config (auth, database, filesystems, etc.). |

---

# 5. Block-by-block code walkthrough of the important files

Each file is broken into blocks; within a block the key lines and keywords are explained.

---

## 5.1 `public/index.php` — the entry point (unchanged)

```php
define('LARAVEL_START', microtime(true));
```
- Creates a global constant holding the start time (`microtime(true)` = current time as a float). Used to measure request duration.

```php
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
```
- `__DIR__` is this file's folder. The assignment `$maintenance = ...` happens inside `file_exists()`. If a maintenance file exists (`php artisan down`), load it and stop here.

```php
require __DIR__.'/../vendor/autoload.php';
```
- Loads Composer's autoloader (auto-includes any class on first use).

```php
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
```
- Builds the app object from `bootstrap/app.php`, then `Request::capture()` reads the incoming HTTP request and `handleRequest(...)` runs it through routing/middleware and returns the response.

---

## 5.2 `bootstrap/app.php` — application wiring (CHANGED)

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
```
- Starts building the app (`basePath` = project root). Registers the web routes (the SPA shell), console commands, and a `/up` health-check endpoint.

```php
        then: function () {
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
    )
```
- **The key new block.** `then:` runs extra route registration: it loads `routes/api.php` under the **`web` middleware group** and the **`/api` prefix**.
- Why the `web` group (not the usual stateless `api` group)? The comment explains: the SPA is same-origin, so reusing Laravel's **session + cookie auth + CSRF** is simpler than tokens (no Sanctum needed). The `/api` prefix keeps the API separate from the SPA catch-all route.

```php
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
```
- Gives `RoleMiddleware` the short alias `'role'`, so routes can write `middleware('role:admin')`. This single line makes the RBAC usable from the route files.

```php
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```
- Exception customization (empty). `->create()` returns the finished app object.

---

## 5.3 `routes/web.php` — the SPA shell catch-all (CHANGED)

```php
Route::view('/{any?}', 'app')
    ->where('any', '^(?!api|up).*$')
    ->name('spa');
```
- `Route::view('/{any?}', 'app')` → for (almost) **any** GET URL, return the `app` Blade view (the SPA shell) without a controller. `{any?}` is an optional catch-all segment.
- `->where('any', '^(?!api|up).*$')` constrains the catch-all with a regex using a **negative lookahead** `(?!api|up)`: match anything that does **not** start with `api` or `up`. This stops the catch-all from swallowing the JSON API (`/api/*`) and the health check (`/up`).
- Result: visiting `/jobs`, `/login`, `/employer/dashboard`, etc. all return the same shell, and React Router resolves the actual page in the browser.

---

## 5.4 `routes/api.php` — the API map (NEW — the most important routing file)

```php
Route::get('/meta', [MetaController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{slug}', [JobController::class, 'show']);
```
- **Public** endpoints (no auth). `/meta` returns filter option lists; `/jobs` lists jobs; `/jobs/{slug}` returns one job. (All paths are under the `/api` prefix added in `bootstrap/app.php`.)

```php
Route::get('/me', [AuthController::class, 'me']);

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
```
- `/me` returns the current user (or null) — React calls it on startup to know if you're logged in.
- `register`/`login` are wrapped in `guest` (only for logged-out users). `logout` requires `auth`.

```php
Route::middleware('auth')->group(function () {

    Route::middleware('role:seeker')->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/dashboard', [SeekerDashboardController::class, 'index']);
        Route::get('/profile', [SeekerProfileController::class, 'show']);
        Route::put('/profile', [SeekerProfileController::class, 'update']);
        Route::post('/profile', [SeekerProfileController::class, 'update']);
        Route::get('/cv/download', [SeekerProfileController::class, 'downloadCv']);
        Route::delete('/cv', [SeekerProfileController::class, 'deleteCv']);
        Route::get('/applications', [SeekerApplicationController::class, 'index']);
        Route::get('/applications/{application}', [SeekerApplicationController::class, 'show']);
        Route::post('/apply/{jobListing}', [SeekerApplicationController::class, 'store']);
        Route::get('/saved-jobs', [SavedJobController::class, 'index']);
        Route::post('/saved-jobs/{jobListing}', [SavedJobController::class, 'toggle']);
    });
```
- Everything here requires login (`auth`). The nested group adds:
  - `role:seeker` → only seekers (passed to `RoleMiddleware`).
  - `prefix('seeker')` → URLs become `/api/seeker/...`.
  - `name('seeker.')` → route-name prefix.
- Note the **`PUT` and `POST` both map to `update`** for the profile: the comment explains POST is exposed directly so multipart **file uploads** (the CV) work cleanly from axios FormData.
- `{application}` / `{jobListing}` are route-model-bound parameters (Laravel auto-loads the model by id).

```php
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index']);
        Route::get('/company', [CompanyProfileController::class, 'show']);
        Route::put('/company', [CompanyProfileController::class, 'update']);
        Route::get('/jobs', [JobListingController::class, 'index']);
        Route::post('/jobs', [JobListingController::class, 'store']);
        Route::get('/jobs/{jobListing}', [JobListingController::class, 'show']);
        Route::put('/jobs/{jobListing}', [JobListingController::class, 'update']);
        Route::delete('/jobs/{jobListing}', [JobListingController::class, 'destroy']);
        Route::put('/jobs/{jobListing}/toggle-status', [JobListingController::class, 'toggleStatus']);
        Route::get('/applicants', [JobListingController::class, 'applicants']);
        Route::get('/applications/{application}/cv', [JobListingController::class, 'downloadCv']);
        Route::put('/applications/{application}/status', [JobListingController::class, 'updateApplicationStatus']);
    });
```
- Employer-only group. The job endpoints are written out explicitly (instead of `Route::resource`) because it's an API; each HTTP verb maps to a CRUD action. Note there's a dedicated `toggle-status` endpoint and applicant-management endpoints.

```php
    Route::middleware('role:admin,super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/jobs', [AdminJobController::class, 'index']);
        Route::delete('/jobs/{jobListing}', [AdminJobController::class, 'destroy']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}/deactivate', [AdminUserController::class, 'deactivate']);
        Route::put('/users/{user}/activate', [AdminUserController::class, 'activate']);
    });
});
```
- `role:admin,super_admin` → **both** admin tiers allowed. Admins can delete jobs and deactivate/activate users, but cannot change job status (only the owning employer can) — the routes that would allow it simply don't exist.

---

## 5.5 `app/Http/Middleware/RoleMiddleware.php` — the security gate (unchanged)

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized.');
    }
    return $next($request);
}
```
- `string ...$roles` — **variadic**: the comma-separated roles on the route (e.g. `admin,super_admin`) arrive as an array.
- The `if` denies the request when **nobody is logged in** (`!auth()->check()`) **OR** the user's role **isn't in the allowed list** (`!in_array(...)`), aborting with **403**.
- Otherwise `$next($request)` passes it on. Because API requests send `Accept: application/json` (set by axios), Laravel returns a JSON 403/401 here instead of an HTML redirect.

---

## 5.6 `app/Models/User.php` — the central account model (unchanged)

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;
```
- `extends Authenticatable` gives login/identity behaviour. The two **traits** add test factories and notifications.

```php
    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'phone', 'bio',
        'location', 'website', 'resume_path', 'skills', 'expected_salary', 'availability',
        'company_name', 'company_description',
        'company_location', 'company_website', 'industry',
        'resume_file_name', 'resume_uploaded_at',
    ];
```
- `$fillable` = the **mass-assignment allow-list** (only these may be set via `create()`/`update()`). Stops an attacker from injecting an unexpected field like `role => 'admin'`. Note seeker AND employer fields all live on the one row.

```php
    protected $hidden = ['password', 'remember_token'];
```
- Never serialized to JSON — so the password hash can't leak (the API Resources also avoid it).

```php
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',
            'resume_uploaded_at' => 'datetime',
        ];
    }
```
- Type conversions: dates → Carbon objects; `'password' => 'hashed'` auto-hashes on assignment; `'skills' => 'array'` stores JSON but reads as a PHP array.

```php
    public function isAdmin(): bool { return in_array($this->role, ['admin', 'super_admin']); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isEmployer(): bool { return $this->role === 'employer'; }
    public function isSeeker(): bool { return $this->role === 'seeker'; }
    public function isActive(): bool { return $this->status === 'active'; }
```
- Role/status helpers used everywhere (controllers, Resources, and — via the JSON booleans — the React UI). `isAdmin()` is `true` for super admins too.

```php
    public function canManage(User $target): bool
    {
        if ($this->id === $target->id) return false;          // never yourself
        if ($this->isSuperAdmin()) return !$target->isSuperAdmin();   // super: anyone but another super
        if ($this->role === 'admin') return in_array($target->role, ['seeker', 'employer']); // admin: regular users only
        return false;
    }
```
- The **anti-privilege-escalation rule**. `$this` is the acting admin, `$target` the user being acted on. Rules: never yourself; a super admin may manage anyone except another super admin; a normal admin may manage only seekers/employers; everyone else, no. **Re-checked on the server in `Admin/UserController`.**

```php
    public function jobListings(): HasMany { return $this->hasMany(JobListing::class); }
    public function applications(): HasMany { return $this->hasMany(Application::class); }
    public function savedJobs(): HasMany { return $this->hasMany(SavedJob::class); }
```
- "One user has many …" relationships (employer's jobs; seeker's applications/bookmarks).

```php
    public function hasCompanyProfile(): bool { return filled($this->company_name); }
    public function hasSavedJob(int $id): bool { return $this->savedJobs()->where('job_listing_id', $id)->exists(); }
    public function hasApplied(int $id): bool { return $this->applications()->where('job_listing_id', $id)->exists(); }
    public function hasDefaultResume(): bool { return filled($this->resume_path); }
    public function companyDisplayName(): string { return $this->company_name ?: $this->name; }
```
- Small helpers. `filled(...)` = "not empty". These gate behaviour (e.g. an employer needs a company profile to post; a seeker needs a default CV to apply) and feed the JSON flags the React app reads.

---

## 5.7 `app/Models/JobListing.php` — the job domain object (unchanged)

```php
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'requirements', 'benefits', 'type', 'experience_level', 'education_level',
        'location', 'salary_min', 'salary_max', 'skills', 'status', 'published_at',
    ];
```
- Mass-assignable job columns. `user_id` = the employer; `slug` = the URL id; the three enums are constrained.

```php
    public const EDUCATION_LABELS = ['none' => 'No Requirements', 'bac' => 'Bac', 'bac+2' => 'Bac+2', 'bac+3' => 'Bac+3', 'bac+5' => 'Bac+5'];
    public const TYPE_LABELS = ['full-time' => 'Full-time', 'part-time' => 'Part-time', 'remote' => 'Remote', 'internship' => 'Internship'];
    public const EXPERIENCE_LABELS = ['entry_level' => 'Entry Level', 'mid_level' => 'Mid Level', 'senior' => 'Senior', 'lead' => 'Lead'];
```
- Class **constants** mapping stored value → human label. One source of truth: `MetaController` ships these to the React forms/filters, and the Resources/helpers use them for display text.

```php
    public function isRemote(): bool { return $this->type === 'remote'; }
```
- "Remote" is a job **type**, not a separate flag.

```php
    public function scopePublished(Builder $query): Builder { return $query->where('status', 'active'); }
```
- `JobListing::published()` adds `WHERE status = 'active'` (public/active jobs only).

```php
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($inner) use ($s) {
                    $inner->where('title', 'like', "%{$s}%")
                        ->orWhere('location', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($u) => $u->where('company_name', 'like', "%{$s}%"));
                });
            })
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            ->when($filters['type'] ?? null, fn ($q, $types) => $q->whereIn('type', (array) $types))
            ->when($filters['location'] ?? null, fn ($q, $l) => $q->where('location', $l))
            ->when($filters['experience'] ?? null, fn ($q, $levels) => $q->whereIn('experience_level', (array) $levels))
            ->when($filters['education'] ?? null, fn ($q, $e) => $q->where('education_level', $e))
            ->when($filters['salary_min'] ?? null, fn ($q, $s) => $q->where('salary_max', '>=', $s))
            ->when($filters['salary_max'] ?? null, fn ($q, $s) => $q->where('salary_min', '<=', $s));
    }
```
- All Browse-Jobs filtering happens **in the DB**. `->when($value, $cb)` runs each clause **only if** that filter was provided (`?? null` makes a missing key skip). Search matches title/location OR the employer's `company_name` (`orWhereHas`). `whereIn('type', (array) $types)` supports multi-select. The two salary lines are a **range overlap** test.

```php
    public function typeLabel(): string { return self::TYPE_LABELS[$this->type] ?? ucfirst($this->type); }
    public function educationLabel(): string { return self::EDUCATION_LABELS[$this->education_level] ?? 'No Requirements'; }
    public function experienceLabel(): string { return self::EXPERIENCE_LABELS[$this->experience_level] ?? ucfirst($this->experience_level); }
    public function salaryRange(): string { /* "8,000 - 15,000 MAD" | "X MAD" | "Not specified" */ }
```
- Display helpers. **These are now called inside `JobListingResource`** so the frontend gets ready-made text and never re-implements the label/salary logic.

---

## 5.8 The API Resources — `app/Http/Resources/*` (NEW; the JSON contract)

A "Resource" is a class with a `toArray()` that turns a model into the exact JSON the frontend receives. This is where the old Blade helper calls now live.

### `UserResource.php`
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id, 'name' => $this->name, 'email' => $this->email,
        'role' => $this->role, 'status' => $this->status,
        'is_seeker' => $this->isSeeker(), 'is_employer' => $this->isEmployer(),
        'is_admin' => $this->isAdmin(), 'is_super_admin' => $this->isSuperAdmin(),
        'is_active' => $this->isActive(),
        // ...profile + CV + company fields...
        'has_default_resume' => $this->hasDefaultResume(),
        'has_company_profile' => $this->hasCompanyProfile(),
        'company_display_name' => $this->companyDisplayName(),
        'can_manage' => $this->when(
            $request->user() && $request->user()->isAdmin(),
            fn () => $request->user()->canManage($this->resource)
        ),
        'created_at' => $this->created_at,
    ];
}
```
- `$this->...` reads the underlying model. The **role booleans** are pre-computed so React can do `user.is_employer` instead of re-deriving roles.
- `password`/`remember_token` are simply never listed → never sent.
- `can_manage` uses `$this->when($condition, fn () => ...)`: the field is **only included** when the viewer is an admin, and its value is `viewer->canManage(thisUser)` — mirroring the per-row button gate. The server still re-checks on every action.

### `JobListingResource.php`
```php
$user = $request->user();
$isSeeker = $user && $user->isSeeker();

return [
    // ...raw columns...
    'type_label' => $this->typeLabel(),
    'education_label' => $this->educationLabel(),
    'experience_label' => $this->experienceLabel(),
    'salary_range' => $this->salaryRange(),
    'is_remote' => $this->isRemote(),
    'company' => $this->whenLoaded('user', fn () => [
        'name' => $this->user->companyDisplayName(),
        'company_name' => $this->user->company_name,
        'industry' => $this->user->industry,
        // ...public-safe fields only (no email/phone)...
    ]),
    'category' => new CategoryResource($this->whenLoaded('category')),
    'applications_count' => $this->whenCounted('applications'),
    'is_saved' => $this->when($isSeeker, fn () => $user->hasSavedJob($this->id)),
    'has_applied' => $this->when($isSeeker, fn () => $user->hasApplied($this->id)),
];
```
- Pre-computed **labels** + `is_remote` mean React renders identical text with zero business logic.
- `whenLoaded('user', ...)` only includes `company` if the controller eager-loaded the employer — and exposes **only public fields** (never the employer's private email/phone).
- `whenCounted('applications')` includes the count only when `withCount()` was used (employer "My Jobs").
- `is_saved` / `has_applied` are included **only for a logged-in seeker** (drives the heart + "Applied" state), exactly like the old per-card Blade checks.

### `ApplicationResource.php`
```php
'resume_file_name' => $this->resume_file_name,
'cv_is_default' => $this->cv_is_default,
'has_resume' => filled($this->resume_path),
'resume_url' => $this->when(filled($this->resume_path), fn () => Storage::disk('public')->url($this->resume_path)),
'status_label' => $this->statusLabel(),
'status_badge_color' => $this->statusBadgeColor(),
'has_response' => $this->hasResponse(),
'job_listing' => new JobListingResource($this->whenLoaded('jobListing')),
'user' => new UserResource($this->whenLoaded('user')),
```
- Carries the **frozen CV snapshot** + the employer's response + pre-computed status label/colour so seeker and employer screens render identical badges. The raw `resume_path` is **not** exposed — CVs are fetched via authed download endpoints.

### `CategoryResource.php`
```php
'job_listings_count' => $this->whenCounted('jobListings'),
```
- Plain category fields; the count appears only when the controller asked for it (Browse sidebar).

---

## 5.9 `app/Http/Controllers/Api/AuthController.php` — auth backbone (NEW)

```php
public function me(Request $request)
{
    if (!$request->user()) {
        return response()->json(['data' => null]);
    }
    return new UserResource($request->user());
}
```
- React calls `/api/me` on startup. Returns `{ data: null }` for a guest, or the user wrapped in `UserResource` (which the SPA stores in `AuthContext`).

```php
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Password::min(8)],
        'role' => ['required', 'in:seeker,employer'],
        'company_name' => ['required_if:role,employer', 'nullable', 'string', 'max:255'],
    ]);
```
- Server-side validation. `unique:users` blocks duplicate emails; `confirmed` requires a matching `password_confirmation`; **`in:seeker,employer`** is the security gate that prevents self-registering an admin; `required_if` makes `company_name` mandatory only for employers.

```php
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'company_name' => $validated['role'] === 'employer' ? ($validated['company_name'] ?? null) : null,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return (new UserResource($user))->response()->setStatusCode(201);
}
```
- Creates the user (`Hash::make` one-way-hashes the password). Logs them in and **regenerates the session id** (security best-practice). Returns the user JSON with HTTP **201 Created**.

```php
public function login(Request $request)
{
    $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required']]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        if (!Auth::user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }
        $request->session()->regenerate();
        return new UserResource(Auth::user());
    }

    throw ValidationException::withMessages([
        'email' => 'The provided credentials do not match our records.',
    ]);
}
```
- `Auth::attempt(...)` verifies the password and logs in on success. **The deactivation gate** runs *after* a correct password (so the message isn't leaked to email-guessers): if not active, log back out, wipe the session, and throw a validation error.
- `throw ValidationException::withMessages([...])` returns an HTTP **422** with `{ message, errors }` — the React login form renders these inline (the SPA equivalent of the old `withErrors()`).

```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out.']);
}
```
- Secure logout: end auth, invalidate session, refresh the CSRF token; return a JSON message.

---

## 5.10 `app/Http/Controllers/Api/JobController.php` — public browsing (NEW)

```php
public function index(Request $request)
{
    $jobs = JobListing::published()
        ->filter($request->only(['search','category','type','location','experience','education','salary_min','salary_max']))
        ->with(['user', 'category'])
        ->latest('published_at')
        ->paginate(15)
        ->withQueryString();

    $categories = Category::withCount(['jobListings' => fn ($q) => $q->published()])->orderBy('name')->get();

    return JobListingResource::collection($jobs)->additional([
        'categories' => CategoryResource::collection($categories),
    ]);
}
```
- Same query as the old Blade version: published only, DB filtering via the `filter` scope, eager-load employer+category (avoids N+1), newest first, 15/page. `$request->only([...])` whitelists the filter keys.
- `JobListingResource::collection($jobs)` returns a **paginated JSON collection** (Laravel adds `data`, `links`, `meta`). `->additional([...])` attaches the sidebar category counts to the same response.

```php
public function show(Request $request, string $slug)
{
    $job = JobListing::where('slug', $slug)->with(['user', 'category'])->firstOrFail();

    $myApplication = null;
    $user = $request->user();
    if ($user && $user->isSeeker()) {
        $myApplication = $user->applications()->where('job_listing_id', $job->id)->first();
    }

    return (new JobListingResource($job))->additional([
        'meta' => ['my_application' => $myApplication ? new ApplicationResource($myApplication) : null],
    ]);
}
```
- Finds the job by slug (`firstOrFail()` → 404 if missing). For a logged-in seeker it also fetches **their** application for this job (if any) and attaches it under `meta.my_application` so the detail page can show the applied/response state.

---

## 5.11 `app/Http/Controllers/Api/Employer/JobListingController.php` — the biggest controller (NEW, 1:1 port)

```php
public function index()
{
    $jobListings = auth()->user()->jobListings()->withCount('applications')->latest()->paginate(15);
    return JobListingResource::collection($jobListings);
}
```
- The employer's own jobs (`auth()->user()->jobListings()` scopes by `user_id`), with an `applications_count`, newest first.

```php
public function store(Request $request)
{
    $validated = $this->validateJob($request);

    $job = auth()->user()->jobListings()->create([
        ...$validated,
        'slug' => Str::slug($validated['title']) . '-' . uniqid(),
        'skills' => !empty($validated['skills']) ? array_map('trim', explode(',', $validated['skills'])) : null,
        'status' => 'active',
        'published_at' => now(),
    ]);

    return (new JobListingResource($job))->additional(['message' => 'Job published successfully!'])
        ->response()->setStatusCode(201);
}
```
- `validateJob()` (shared, below) returns clean data. `jobListings()->create([...])` auto-sets `user_id`. `...$validated` **spreads** the validated fields in. `slug` = slugified title + `uniqid()` for uniqueness. `skills`: split a comma string into a trimmed array, or null. Jobs publish immediately (`status='active'`). Returns **201** with a success `message` (the axios interceptor turns that into a success toast).

```php
public function update(Request $request, JobListing $jobListing)
{
    $this->authorizeJob($jobListing);
    $validated = $this->validateJob($request);
    $jobListing->update([
        ...$validated,
        'skills' => !empty($validated['skills']) ? array_map('trim', explode(',', $validated['skills'])) : null,
    ]);
    return (new JobListingResource($jobListing->fresh()))->additional(['message' => 'Job listing updated successfully!']);
}
```
- Ownership check first, then same validation/skills handling. **Does not touch `status`** (that's only the toggle). `->fresh()` reloads the updated row for the response.

```php
public function toggleStatus(JobListing $jobListing)
{
    $this->authorizeJob($jobListing);
    if ($jobListing->status === 'active') {
        $jobListing->update(['status' => 'inactive']);
        $message = 'Job set to inactive — it is no longer visible to seekers.';
    } else {
        $jobListing->update(['status' => 'active', 'published_at' => $jobListing->published_at ?? now()]);
        $message = 'Job set to active — it is now visible to seekers.';
    }
    return (new JobListingResource($jobListing->fresh()))->additional(['message' => $message]);
}
```
- Flips active ⇄ inactive. When re-activating, sets `published_at` to its existing value **or** `now()` if never published (`?? now()`).

```php
public function applicants(Request $request)
{
    $user = auth()->user();
    // ...read job/status/search/sort params...
    $applications = Application::query()
        ->whereHas('jobListing', fn ($q) => $q->where('user_id', $user->id))
        ->with(['user', 'jobListing'])
        ->when($jobFilter, fn ($q, $jobId) => $q->where('job_listing_id', $jobId))
        ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
        ->when($search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")))
        // ...sorting...
        ->paginate(20)->withQueryString();

    $jobs = $user->jobListings()->orderBy('title')->get(['id', 'title']);
    return ApplicationResource::collection($applications)->additional(['jobs' => $jobs]);
}
```
- The security line is `whereHas('jobListing', fn ($q) => $q->where('user_id', $user->id))` — only applications to **this employer's** jobs. Then optional job/status/name filters and sorting, all in the DB. `additional(['jobs' => ...])` ships the job list for the filter dropdown.

```php
public function updateApplicationStatus(Request $request, Application $application)
{
    $this->authorizeJob($application->jobListing);
    $validated = $request->validate([
        'status' => ['required', 'in:accepted,rejected'],
        'response_message' => ['required', 'string', 'min:10', 'max:500'],
    ], [ /* custom messages */ ]);
    $application->update([
        'status' => $validated['status'],
        'response_message' => $validated['response_message'],
        'responded_at' => now(),
    ]);
    $name = $application->user->name;
    return (new ApplicationResource($application->fresh()->load(['user','jobListing'])))
        ->additional(['message' => "Response sent to {$name}."]);
}
```
- Accept/reject. Ownership check; `status` must be accepted/rejected; a **10–500 char message is mandatory**. Stamps `responded_at`. Returns the refreshed application + a personalized success message.

```php
private function validateJob(Request $request): array
{
    $cities = config('morocco.cities');
    $validated = $request->validate([
        'title' => ['required','string','max:255'],
        'category_id' => ['nullable','exists:categories,id'],
        'description' => ['required','string'],
        // ...
        'type' => ['required','in:full-time,part-time,remote,internship'],
        'experience_level' => ['required','in:entry_level,mid_level,senior,lead'],
        'education_level' => ['required','in:none,bac,bac+2,bac+3,bac+5'],
        'location' => [
            'nullable', 'required_unless:type,remote', Rule::in($cities),
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('type') === 'remote' && filled($value)) {
                    $fail('Remote jobs cannot have a location.');
                }
            },
        ],
        'salary_min' => ['nullable','numeric','min:0'],
        'salary_max' => ['nullable','numeric','min:0','gte:salary_min'],
        'skills' => ['nullable','string'],
    ], [ /* custom messages */ ]);

    if ($validated['type'] === 'remote') {
        $validated['location'] = null;
    }
    return $validated;
}
```
- The enums are constrained with `in:...`. The **location** rules are the clever part: `required_unless:type,remote` (required for every non-remote type), `Rule::in($cities)` (must be a real Moroccan city), and a **custom closure** that fails if a remote job tries to set a location — enforcing both directions. Finally, remote jobs always store `null` location. `salary_max` has `gte:salary_min`.

```php
private function authorizeJob(JobListing $job): void
{
    if ($job->user_id !== auth()->id()) abort(403);
}
```
- The reusable ownership guard used by every employer action: 403 unless the job belongs to the current employer.

---

## 5.12 `app/Http/Controllers/Api/Seeker/ApplicationController.php` — the apply flow (NEW)

```php
public function store(Request $request, JobListing $jobListing)
{
    $user = auth()->user();

    if ($user->hasApplied($jobListing->id)) {
        return response()->json(['message' => 'You have already applied to this job.'], 409);
    }

    if (!$user->hasDefaultResume()) {
        return response()->json([
            'message' => 'Please upload your resume in your profile before applying.',
            'redirect' => '/seeker/profile',
        ], 422);
    }
```
- Guard 1: no duplicate applications → HTTP **409 Conflict**. Guard 2: must have a default CV → HTTP **422** with a `redirect` hint the React app uses to send the seeker to their profile.

```php
    $request->validate([
        'cv_choice' => ['required', 'in:default,upload'],
        'resume' => ['required_if:cv_choice,upload', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
    ], [ 'resume.required_if' => 'Please choose a PDF file to upload, or use your default CV.' ]);

    if ($request->input('cv_choice') === 'upload' && $request->hasFile('resume')) {
        $resumePath = $request->file('resume')->store('resumes', 'public');
        $resumeFileName = $request->file('resume')->getClientOriginalName();
        $cvIsDefault = false;
    } else {
        $resumePath = $user->resume_path;
        $resumeFileName = $user->resume_file_name;
        $cvIsDefault = true;
    }
```
- `cv_choice` = reuse `default` profile CV or `upload` a new PDF (≤5 MB, `mimes:pdf`). If uploading, store the file on the `public` disk; otherwise **copy** the profile CV's path/name onto this application — a **frozen snapshot** so later profile changes don't alter past applications.

```php
    $application = Application::create([
        'user_id' => $user->id,
        'job_listing_id' => $jobListing->id,
        'resume_path' => $resumePath,
        'resume_file_name' => $resumeFileName,
        'cv_is_default' => $cvIsDefault,
    ]);

    return (new ApplicationResource($application))
        ->additional(['message' => 'Application submitted successfully!'])
        ->response()->setStatusCode(201);
}
```
- Creates the application (status defaults to `pending`) and returns **201** + success message.

```php
private function authorizeOwner(Application $application): void
{
    if ($application->user_id !== auth()->id()) abort(403);
}
```
- Used by `show()` so a seeker can't read someone else's application by changing the id.

---

## 5.13 Admin controllers (NEW)

`Admin/UserController` — deactivate/activate (never delete):
```php
public function deactivate(User $user)
{
    abort_unless(auth()->user()->canManage($user), 403);
    $user->update(['status' => 'deactivated']);
    return (new UserResource($user->fresh()))->additional(['message' => 'User deactivated successfully.']);
}
```
- `abort_unless(canManage(...), 403)` is the **server-side enforcement** of the permission hierarchy — independent of whether the React UI showed the button. `activate()` is the mirror. There is no delete.

`Admin/JobController::destroy()` deletes a job and returns a message; the admin cannot change a job's status (no such endpoint).

`Admin/DashboardController::index()` builds a `$stats` array of `COUNT` aggregates (users by status/role, jobs, applications) plus recent jobs/users, returned as JSON.

---

## 5.14 `resources/views/app.blade.php` — the SPA shell (NEW)

This is the **only** HTML page Laravel renders for the app.

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="base-path" content="{{ rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/') }}">
```
- The **CSRF token** is exposed for completeness. The **base-path** meta computes the subfolder the app is served from (e.g. `/job_board/public` on XAMPP) by parsing `APP_URL`; React Router and axios read it so URLs work under a subfolder.

```blade
@viteReactRefresh
@vite(['resources/css/app.css', 'resources/js/main.jsx'])
```
- `@vite([...])` injects the compiled CSS + the React entry script (`main.jsx`). `@viteReactRefresh` enables hot-reload during development.

```blade
<body class="...">
    <div id="root"></div>
</body>
```
- `<div id="root">` is the empty mount point — React renders the entire app inside it.

---

## 5.15 `resources/js/main.jsx` + `App.jsx` — the React entry & router (NEW)

`main.jsx`:
```jsx
createRoot(document.getElementById('root')).render(
    <React.StrictMode><App /></React.StrictMode>
);
```
- Finds `#root` and renders `<App />`. `StrictMode` is a dev-only helper that surfaces bugs.

`App.jsx` — providers + router:
```jsx
export default function App() {
    return (
        <BrowserRouter basename={BASE_PATH || undefined}>
            <FlashProvider>
                <AuthProvider>
                    <ApiBridge />
                    <Layout>
                        <AppRoutes />
                    </Layout>
                </AuthProvider>
            </FlashProvider>
        </BrowserRouter>
    );
}
```
- `<BrowserRouter basename=...>` enables client-side routing under the subfolder base path.
- `FlashProvider` / `AuthProvider` wrap the app in global state (toasts + current user).
- `ApiBridge` connects the axios interceptor to those contexts (so API errors flash and 401s log out).
- `Layout` is the chrome (navbar/footer); `AppRoutes` is the route table.

```jsx
function DashboardRedirect() {
    const { user, loading } = useAuth();
    if (loading) return <FullPageLoader />;
    if (!user) return <Navigate to="/login" replace />;
    if (user.is_admin) return <Navigate to="/admin/dashboard" replace />;
    if (user.is_employer) return <Navigate to="/employer/dashboard" replace />;
    return <Navigate to="/seeker/dashboard" replace />;
}
```
- The client-side equivalent of the old `DashboardController` fan-out: `/dashboard` redirects to the role's dashboard (admin checked first, since super admins are also admins).

```jsx
<Route path="/jobs" element={<JobsIndex />} />
<Route path="/login" element={<RequireGuest><Login /></RequireGuest>} />
<Route path="/seeker/dashboard" element={<RequireRole roles={SEEKER}><SeekerDashboard /></RequireRole>} />
<Route path="/employer/jobs" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerJobsIndex /></RequireRole>} />
<Route path="/admin/users" element={<RequireRole roles={ADMIN}><AdminUsersIndex /></RequireRole>} />
<Route path="*" element={<Navigate to="/" replace />} />
```
- Each `<Route>` maps a URL to a page, wrapped in a guard. `RequireGuest` blocks logged-in users; `RequireRole` checks the role (and optionally `requireCompanyProfile` for employers). The `*` route redirects unknown URLs home. **These guards are convenience only — the API re-checks every request.**

---

## 5.16 `resources/js/api/client.js` + `api/index.js` — the service layer (NEW)

`client.js` — the axios instance:
```js
const client = axios.create({
    baseURL: `${BASE_PATH}/api`,
    withCredentials: true,
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
});
```
- All requests go to `/api` under the base path. `withCredentials: true` sends the Laravel **session cookie**. `Accept: application/json` + `X-Requested-With` make `auth`/`role` middleware return JSON 401/403 instead of redirecting.
- The comment explains CSRF: axios auto-reads Laravel's `XSRF-TOKEN` cookie and returns it as the `X-XSRF-TOKEN` header on writes (no stale `<meta>` token).

```js
client.interceptors.response.use(
    (response) => {
        const message = response?.data?.message;
        if (message && response.config.method !== 'get') handlers.flash('success', message);
        return response;
    },
    (error) => {
        const status = error.response?.status;
        const data = error.response?.data;
        if (status === 401) handlers.onUnauthorized();
        else if (status === 422 && data?.errors) { /* form renders inline */ }
        else if (data?.message) handlers.flash('error', data.message);
        else if (status >= 500) handlers.flash('error', 'Something went wrong. Please try again.');
        return Promise.reject(error);
    }
);
```
- A single **response interceptor** centralizes cross-cutting behaviour: any non-GET response with a `message` becomes a **success toast** (mirrors Laravel session flashes); **401** logs the user out; **422** validation errors are left for the form to render inline; other errors / **5xx** flash an error.

`index.js` — one function per endpoint (clean service layer):
```js
export const jobsApi = {
    list: (params) => client.get('/jobs', { params }),
    show: (slug) => client.get(`/jobs/${slug}`),
};
export const seekerApi = {
    apply: (jobId, formData) => client.post(`/seeker/apply/${jobId}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    }),
    toggleSaved: (jobId) => client.post(`/seeker/saved-jobs/${jobId}`),
    // ...
};
```
- Pages call `jobsApi.list(...)` etc. instead of hand-writing URLs — keeping the API surface in one place. CV uploads/applies send `multipart/form-data`.

---

## 5.17 `resources/js/contexts/AuthContext.jsx` + `components/guards.jsx` (NEW)

`AuthContext.jsx`:
```jsx
export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const refresh = useCallback(async () => {
        try { const { data } = await authApi.me(); setUser(data.data ?? null); }
        catch { setUser(null); }
    }, []);

    useEffect(() => { (async () => { await refresh(); setLoading(false); })(); }, [refresh]);

    const login = useCallback(async (payload) => { const { data } = await authApi.login(payload); setUser(data.data); return data.data; }, []);
    // register / logout similar
    return <AuthContext.Provider value={{ user, setUser, loading, refresh, login, register, logout }}>{children}</AuthContext.Provider>;
}
```
- Holds the current `user` for the whole app. On mount it calls `/api/me` to **hydrate** auth state (`loading` covers that window). `login`/`register`/`logout` update `user`. `useAuth()` (below) reads this anywhere.

`guards.jsx`:
```jsx
export function RequireRole({ roles, requireCompanyProfile = false, children }) {
    const { user, loading } = useAuth();
    const needsProfile = requireCompanyProfile && user?.is_employer && !user?.has_company_profile;
    // ...flash if needsProfile...
    if (loading) return <FullPageLoader />;
    if (!user) return <Navigate to="/login" state={{ from: location }} replace />;
    if (!roles.includes(user.role)) return <Navigate to="/dashboard" replace />;
    if (needsProfile) return <Navigate to="/employer/company" replace />;
    return children;
}
```
- While auth resolves, show a spinner. Then: not logged in → `/login` (remembering the intended path, like `redirect()->intended()`); wrong role → `/dashboard`; employer without a company profile → `/employer/company` (mirrors the old server gate). Otherwise render the page. The comment stresses the server still enforces all of this.

---

## 5.18 `resources/js/pages/jobs/JobsIndex.jsx` — a representative page (NEW)

```jsx
export default function JobsIndex() {
    const meta = useMeta();
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const filters = readFilters(searchParams);
    const page = searchParams.get('page') || '1';

    const fetchJobs = useCallback(async () => {
        const params = { ...readFilters(searchParams), page };
        const { data: res } = await jobsApi.list(params);
        setData(res);
    }, [searchParams]);

    useEffect(() => { fetchJobs(); }, [fetchJobs]);
```
- The Browse page keeps filters **in the URL** (`useSearchParams`) — so back/forward and reload preserve them, exactly like the old `withQueryString()`. `useMeta()` loads the filter option lists from `/api/meta`. When the URL changes, it re-fetches `/api/jobs` with those params.

```jsx
    const toggleSaved = async (job) => {
        const { data: res } = await seekerApi.toggleSaved(job.id);
        setData((d) => ({ ...d, data: d.data.map((j) => j.id === job.id ? { ...j, is_saved: res.saved } : j) }));
    };
```
- Saving/unsaving calls the toggle endpoint and **optimistically updates** just that job's `is_saved` flag in local state (the API returns `{ saved: true|false }`).

```jsx
    return (
        <div className="...">
            <JobFilters meta={meta} filters={filters} onApply={applyFilters} onClear={clearFilters} />
            {jobs.map((job) => <JobCard key={job.id} job={job} onToggleSaved={toggleSaved} />)}
            <Pagination meta={data.meta} onPage={goToPage} />
        </div>
    );
}
```
- Renders the filter sidebar, a `JobCard` per result, and pagination — driven entirely by the JSON the API returned.

---

## 5.19 Database migrations — the schema (unchanged)

A migration describes a table; `up()` creates, `down()` drops.

### `create_users_table` (key columns)
```php
$table->string('email')->unique();
$table->enum('role', ['seeker', 'employer', 'admin', 'super_admin'])->default('seeker');
$table->enum('status', ['active', 'deactivated'])->default('active');
$table->string('resume_path')->nullable();
$table->string('company_name')->nullable();
// ...other CV + company fields, all nullable...
```
- Unique email; the `role` enum (defaults to seeker); the `status` enum (deactivation gate). All seeker-CV and employer-company fields are **nullable** because one table serves every role. (Also creates `password_reset_tokens` + `sessions`.)

### `create_job_listings_table` (key columns)
```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
$table->string('slug')->unique();
$table->enum('type', ['full-time','part-time','remote','internship'])->default('full-time');
$table->enum('experience_level', ['entry_level','mid_level','senior','lead'])->default('entry_level');
$table->enum('education_level', ['none','bac','bac+2','bac+3','bac+5'])->default('none');
$table->decimal('salary_min', 10, 2)->nullable();
$table->json('skills')->nullable();
$table->enum('status', ['active','inactive'])->default('active');
$table->index(['status','published_at']); $table->index('type');
```
- `cascadeOnDelete()` → deleting an employer deletes their jobs. `nullOnDelete()` → deleting a category nulls the job's category (job survives). The enums mirror the model constants. Indexes speed up filtering/sorting.

### `create_applications_table`
```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
$table->string('resume_path')->nullable();
$table->boolean('cv_is_default')->default(true);
$table->enum('status', ['pending','accepted','rejected'])->default('pending');
$table->text('response_message')->nullable();
$table->timestamp('responded_at')->nullable();
$table->unique(['user_id', 'job_listing_id']);
```
- Stores the **CV snapshot** + the employer response. `unique(['user_id','job_listing_id'])` → a seeker can apply to a job **only once** (DB-enforced).

### `create_saved_jobs_table` & `create_categories_table`
```php
// saved_jobs: a unique (user_id, job_listing_id) join row → no duplicate bookmarks
$table->unique(['user_id', 'job_listing_id']);
// categories: name, unique slug, optional icon
$table->string('slug')->unique();
```

---

## 5.20 `config/morocco.php` — the city source of truth (unchanged)

```php
return [
    'cities' => ['Agadir', 'Al Hoceima', /* ...alphabetical... */ 'Zagora'],
];
```
- A config file returns an array; `config('morocco.cities')` reads it. One list feeds the job validator (`Rule::in($cities)`), the company-profile validator, and `MetaController` (which ships it to the React filters/forms).

---

## 5.21 `database/seeders/DatabaseSeeder.php` — demo data (unchanged)

`run()` seeds in dependency order:
```php
foreach ($categories as $cat) Category::create($cat);          // 10 categories first
User::create([... 'role' => 'super_admin' ...]);               // the single super admin
// + a normal admin, 5 employers (last one deactivated to demo lockout),
// 5 seekers (last has NO CV to demo the apply gate),
// 14 jobs, random applications (pending/accepted/rejected), random saved jobs.
```
- `Hash::make('password')` sets the shared demo password. The deliberately-deactivated employer and the CV-less seeker exist to demonstrate the lockout and the "must upload a CV" gate.

---

## 6. Quick reference — where to look for X

| I want to understand… | Look at… |
|------------------------|----------|
| What data endpoints exist | `routes/api.php` |
| What pages/URLs exist (client) | `resources/js/App.jsx` |
| Who can access what | `RoleMiddleware.php` + `role:` groups in `routes/api.php` + `User::canManage()` + `guards.jsx` |
| The JSON shape the frontend gets | `app/Http/Resources/*` |
| The data model / columns | `database/migrations/*` + `$fillable`/`casts()` in models |
| Job search & filtering | `JobListing::scopeFilter()` + `Api/JobController::index()` + `pages/jobs/JobsIndex.jsx` |
| How applying works | `Api/Seeker/ApplicationController::store()` |
| Job posting & validation | `Api/Employer/JobListingController` (`store`, `update`, `validateJob`) |
| Login / deactivation lockout | `Api/AuthController::login()` |
| Registration / role restriction | `Api/AuthController::register()` |
| Auth state in the UI | `contexts/AuthContext.jsx` |
| API calls / CSRF / error toasts | `api/client.js` + `api/index.js` |
| The look & feel / theme | `resources/css/app.css` + `components/Layout.jsx` |
| Demo accounts & data | `database/seeders/DatabaseSeeder.php` |

**Default demo logins** (all use password `password`):
- Super admin: `superadmin@jobboard.com`
- Admin: `admin@jobboard.com`
- Employer: `employer@jobboard.com`
- Seeker: `seeker@jobboard.com`

**Run it locally:** `composer install` → `npm install` → configure `.env` + `php artisan migrate --seed` → run `npm run dev` (Vite) and serve Laravel. The SPA needs the Vite bundle built/running.

---

*End of documentation. Regenerated after a full re-scan of the current React-SPA + Laravel-API codebase.*
