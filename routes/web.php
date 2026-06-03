<?php
// ============================================================
// routes/web.php
// ============================================================

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────

Route::get('/', fn() => view('welcome'));

Route::get('/attachments/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . ltrim($path, '/'));

    abort_if(! file_exists($fullPath) || ! is_file($fullPath), 404);

    return response()->file($fullPath);
})->where('path', '.*')->name('attachments.view');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── User Dashboard ───────────────────────────────────────────────────────
    Route::get('/dashboard', fn() => view('user.dashboard'))->name('user.dashboard');

    // ── User Complaints (CRUD) ────────────────────────────────────────────────
    Route::prefix('complaints')->name('user.complaints.')->group(function () {
        Route::get('/',           [ComplaintController::class, 'index'])->name('index');
        Route::get('/create',     [ComplaintController::class, 'create'])->name('create');
        Route::post('/',          [ComplaintController::class, 'store'])->name('store');
        Route::get('/{complaint}',        [ComplaintController::class, 'show'])->name('show');
        Route::get('/{complaint}/edit',   [ComplaintController::class, 'edit'])->name('edit');
        Route::put('/{complaint}',        [ComplaintController::class, 'update'])->name('update');
        Route::delete('/{complaint}',     [ComplaintController::class, 'destroy'])->name('destroy');
        Route::post('/{complaint}/reply', [ComplaintController::class, 'reply'])->name('reply');
    });

    // ── Admin Area (protected by admin middleware) ────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        Route::get('/dashboard', [Admin\ComplaintController::class, 'dashboard'])->name('dashboard');

        // Complaints
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/',                         [Admin\ComplaintController::class, 'index'])->name('index');
            Route::get('/{complaint}',              [Admin\ComplaintController::class, 'show'])->name('show');
            Route::put('/{complaint}',              [Admin\ComplaintController::class, 'update'])->name('update');
            Route::delete('/{complaint}',           [Admin\ComplaintController::class, 'destroy'])->name('destroy');
            Route::post('/{complaint}/respond',     [Admin\ComplaintController::class, 'respond'])->name('respond');
        });

        // Categories
        Route::resource('categories', Admin\CategoryController::class);

        // Users management
        Route::resource('users', Admin\UserController::class)->only(['index', 'edit', 'update', 'destroy']);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',           [Admin\ReportController::class, 'index'])->name('index');
            Route::get('/pdf',        [Admin\ReportController::class, 'exportPdf'])->name('pdf');
            Route::get('/csv',        [Admin\ReportController::class, 'exportCsv'])->name('csv');
            Route::get('/xlsx',       [Admin\ReportController::class, 'exportXlsx'])->name('xlsx');
            Route::get('/json',       [Admin\ReportController::class, 'exportJson'])->name('json');
        });
    });
});
