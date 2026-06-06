<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    POSController,
    TransactionController,
    CustomerController,
    ServiceController,
    ReportController,
    UserController,
    ExpenseController,
    SettingController,
    QueueController,
    Auth\LoginController
};

/*
|--------------------------------------------------------------------------
| Guest Routes (Tidak Perlu Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Perlu Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // ==============================================================
    // DASHBOARD & AUTH - SEMUA ROLE BISA AKSES
    // ==============================================================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ==============================================================
    // USER MANAGEMENT - HANYA ADMIN SAJA
    // ==============================================================
    Route::resource('users', UserController::class);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    
    // Ganti Password - SEMUA ROLE BISA AKSES
    Route::post('/password/change', [UserController::class, 'changePassword'])->name('password.change');

    // ==============================================================
    // POS (Point of Sale) - ADMIN & CASHIER
    // ==============================================================
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/from-queue/{queue}', [POSController::class, 'fromQueue'])->name('from-queue');
        Route::post('/process', [POSController::class, 'processTransaction'])->name('process');
        Route::get('/search-customer', [POSController::class, 'searchCustomer'])->name('search-customer');
        Route::get('/customer/{id}', [POSController::class, 'getCustomer'])->name('get-customer');
        Route::post('/clear-queue-session', [POSController::class, 'clearQueueSession'])->name('clear-queue-session');
        Route::get('/check-queue-session', [POSController::class, 'checkQueueSession'])->name('check-queue-session');
        Route::get('/get-services', [POSController::class, 'getServices'])->name('get-services');
    });

    // ==============================================================
    // QUEUE ROUTES - ADMIN, CASHIER, BARBER
    // ==============================================================
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [QueueController::class, 'index'])->name('index');
        Route::post('/', [QueueController::class, 'store'])->name('store');
        Route::put('/{id}/call', [QueueController::class, 'call'])->name('call');
        Route::put('/{id}/start', [QueueController::class, 'start'])->name('start');
        Route::put('/{id}/complete', [QueueController::class, 'complete'])->name('complete');
        Route::put('/{id}/cancel', [QueueController::class, 'cancel'])->name('cancel');
        Route::put('/{id}/assign-barber', [QueueController::class, 'assignBarber'])->name('assign-barber');
        Route::get('/display', [QueueController::class, 'getDisplay'])->name('display');
        Route::post('/reset', [QueueController::class, 'resetDaily'])->name('reset');
        Route::get('/export', [QueueController::class, 'export'])->name('export');
        Route::get('/barber-status', [QueueController::class, 'getBarberStatus'])->name('barber-status');
        Route::get('/check-barber/{id}', [QueueController::class, 'checkBarberAvailability'])->name('check-barber');
    });
    
    // ==============================================================
    // TRANSACTION ROUTES - ADMIN & CASHIER
    // ==============================================================
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::post('/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('cancel');
    });
    
    // Receipt Print
    Route::get('/receipt/{transaction}', [TransactionController::class, 'printReceipt'])->name('receipt.print');

    // ==============================================================
    // CUSTOMER ROUTES - ADMIN & CASHIER
    // ==============================================================
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/add-points', [CustomerController::class, 'addPoints'])->name('customers.add-points');
    Route::get('/customers/by-phone/{phone}', [CustomerController::class, 'getByPhone'])->name('customers.by-phone');

    // ==============================================================
    // SERVICE ROUTES - ADMIN & BARBER
    // ==============================================================
    Route::resource('services', ServiceController::class);
    Route::post('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // ==============================================================
    // EXPENSE ROUTES - ADMIN SAJA
    // ==============================================================
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/category/{category}', [ExpenseController::class, 'byCategory'])->name('by-category');
        Route::get('/report/pdf', [ExpenseController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/report/excel', [ExpenseController::class, 'exportExcel'])->name('export-excel');
    });

    // ==============================================================
    // REPORT ROUTES - ADMIN SAJA
    // ==============================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
    });

    // ==============================================================
    // SETTINGS ROUTES - ADMIN SAJA
    // ==============================================================
    Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/general', [SettingController::class, 'updateGeneral'])->name('update-general');
    Route::post('/business', [SettingController::class, 'updateBusiness'])->name('update-business');
    Route::post('/membership', [SettingController::class, 'updateMembership'])->name('update-membership');
    Route::post('/print', [SettingController::class, 'updatePrint'])->name('update-print');
    Route::post('/notification', [SettingController::class, 'updateNotification'])->name('update-notification');
    Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
    Route::get('/backup', [SettingController::class, 'backupDatabase'])->name('backup');
    Route::get('/reset', [SettingController::class, 'resetSettings'])->name('reset');
    Route::get('/system-info', [SettingController::class, 'systemInfo'])->name('system-info');
});
});

/*
|--------------------------------------------------------------------------
| Additional Routes for Development
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/debug-routes', function () {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'methods' => implode('|', $route->methods()),
            ];
        });
        return response()->json($routes);
    });
}