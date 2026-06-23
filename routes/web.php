<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes — SPA shell only
|--------------------------------------------------------------------------
| The frontend is now a React (JavaScript) single-page application. All data
| flows through the JSON API (routes/api.php, registered in bootstrap/app.php
| under the `/api` prefix). Every other GET request returns the SPA shell
| (resources/views/app.blade.php); React Router then resolves the actual page
| on the client. This catch-all must not swallow `/api/*` or the `/up` health
| check (both are registered separately and are excluded by the regex below).
|
| The shell is served by the `web` middleware group, so loading any page first
| issues the session cookie + XSRF-TOKEN cookie that axios needs for CSRF.
*/
Route::view('/{any?}', 'app')
    ->where('any', '^(?!api|up).*$')
    ->name('spa');
