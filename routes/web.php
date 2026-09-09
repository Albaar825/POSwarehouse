    <?php

    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\Admin\ProductController;
    use App\Http\Controllers\Admin\StockController;
    use App\Http\Controllers\Admin\StockOpnameController;
    use App\Http\Controllers\Kasir\TransactionController;
    use App\Http\Controllers\Auth\ForgotPasswordController;
    use App\Http\Controllers\Auth\ResetPasswordController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', fn () => redirect('/login'));

    /*
    |--------------------------------------------------------------------------
    | Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // ========================================
    // FORGOT PASSWORD
    // ========================================

    Route::get('/forgot-password', [ForgotPasswordController::class,'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ ForgotPasswordController::class,'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class,'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class,'reset'])
        ->name('password.update');
    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile/password', [\App\Http\Controllers\ProfileController::class, 'showChangePasswordForm'])->name('profile.password');
        Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        | Produk, Stok, dan Warehouse
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')->group(function () {
            /*
            | Products
            */
            Route::resource('products', ProductController::class);
            /*
            | Stock
            */
            Route::get('/stock', [StockController::class, 'index'])
                ->name('stock.index');

            Route::get('/stock/create', [StockController::class, 'create'])
                ->name('stock.create');

            Route::post('/stock', [StockController::class, 'store'])
                ->name('stock.store');
            /*
            |--------------------------------------------------------------------------
            | Stock Opname
            |--------------------------------------------------------------------------
            */
            Route::get('/stock-opname', [StockOpnameController::class, 'index'])
                ->name('admin.stock-opname.index');
            Route::get('/stock-opname/create', [StockOpnameController::class, 'create'])
                ->name('admin.stock-opname.create');
            Route::post('/stock-opname', [StockOpnameController::class, 'store'])
                ->name('admin.stock-opname.store');
            Route::get('/stock-opname/{stockOpname}', [StockOpnameController::class, 'show'])
                ->name('admin.stock-opname.show');
            Route::get('/stock-opname/{stockOpname}/edit', [StockOpnameController::class, 'edit'])
            ->name('admin.stock-opname.edit');
            Route::put('/stock-opname/{stockOpname}', [StockOpnameController::class, 'update'])
            ->name('admin.stock-opname.update');
        });

        /*
        |--------------------------------------------------------------------------
        | Kasir Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:kasir')->group(function () {

            Route::get('/pos', [TransactionController::class, 'create'])
                ->name('pos.index');

            Route::post('/pos', [TransactionController::class, 'store'])
                ->name('pos.store');

            Route::get('/pos/receipt/{transaction}', [TransactionController::class, 'receipt'])
                ->name('pos.receipt');


            /*
            |--------------------------------------------------------------------------
            | TRANSACTION HISTORY
            |--------------------------------------------------------------------------
            */

            Route::get('/transactions', [TransactionController::class, 'history'])
                ->name('transactions.index');

            Route::get('/transactions/pdf', [TransactionController::class, 'historyPdf'])
                ->name('transactions.pdf');


            /*
            |--------------------------------------------------------------------------
            | CREDIT
            |--------------------------------------------------------------------------
            */

            Route::get('/pos/credit', [TransactionController::class, 'creditIndex'])
                ->name('pos.credit.index');

            Route::get('/pos/credit/{transaction}', [TransactionController::class, 'creditShow'])
                ->name('pos.credit.show');

            Route::post('/pos/credit/{transaction}/payment', [TransactionController::class, 'creditPayment'])
                ->name('pos.credit.payment');


            /*
            |--------------------------------------------------------------------------
            | OPEN INVOICE
            |--------------------------------------------------------------------------
            */

            Route::post('/pos/open-invoice', [TransactionController::class, 'hold'])
                ->name('pos.open-invoice.hold');

            Route::get('/pos/open-invoices', [TransactionController::class, 'openInvoiceIndex'])
                ->name('pos.open-invoice.index');

            Route::get('/pos/open-invoices/data', [TransactionController::class, 'openInvoices'])
                ->name('pos.open-invoice.data');

            Route::get('/pos/open-invoice/{transaction}', [TransactionController::class, 'openInvoiceShow'])
                ->name('pos.open-invoice.show');

            Route::post('/pos/open-invoice/{transaction}/take', [TransactionController::class, 'openInvoiceTake'])
                ->name('pos.open-invoice.take');

            Route::post('/pos/open-invoice/{transaction}/checkout', [TransactionController::class, 'openInvoiceCheckout'])
                ->name('pos.open-invoice.checkout');

            Route::delete('/pos/open-invoice/{transaction}', [TransactionController::class, 'openInvoiceCancel'])
                ->name('pos.open-invoice.cancel');

        });

    });
