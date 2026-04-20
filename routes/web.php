<?php

use App\Http\Controllers\Club\IcsExportController;
use App\Http\Controllers\Club\StockExportController;
use App\Http\Controllers\SuperAdmin\ImpersonateController;
use App\Http\Controllers\SuperAdmin\PaymentExportController;
use App\Livewire\SuperAdmin\Clubs\Create as ClubCreate;
use App\Livewire\SuperAdmin\Clubs\Edit as ClubEdit;
use App\Livewire\SuperAdmin\Clubs\Index as ClubIndex;
use App\Livewire\SuperAdmin\Clubs\Show as ClubShow;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use App\Livewire\SuperAdmin\Payments\Index as PaymentsIndex;
use App\Livewire\SuperAdmin\Plans\Create as PlanCreate;
use App\Livewire\SuperAdmin\Plans\Edit as PlanEdit;
use App\Livewire\SuperAdmin\Plans\Index as PlanIndex;
use Illuminate\Support\Facades\Route;

// Racine → page de connexion
Route::get('/', fn () => redirect()->route('login'));

// --- Espace club (sous-domaines tenant + domaine central en impersonation) ---
Route::middleware(['auth', 'tenant_access'])->group(function () {

    Route::get('dashboard',             \App\Livewire\Club\Dashboard::class)       ->name('club.dashboard');
    Route::get('players',               \App\Livewire\Club\Players\Index::class)   ->name('club.players.index');
    Route::get('players/create',        \App\Livewire\Club\Players\Form::class)    ->name('club.players.create');
    Route::get('players/{player}/edit', \App\Livewire\Club\Players\Form::class)    ->name('club.players.edit');
    Route::get('categories',            \App\Livewire\Club\Categories\Index::class)->name('club.categories.index');
    Route::get('teams',                 \App\Livewire\Club\Teams\Index::class)     ->name('club.teams.index');
    Route::get('staff',                 \App\Livewire\Club\Staff\Index::class)     ->name('club.staff.index');
    Route::get('users',                 \App\Livewire\Club\Users\Index::class)     ->name('club.users.index');
    Route::view('profile', 'profile')->name('profile');

    // Stock & Matériel
    Route::get('stock',              \App\Livewire\Club\Stock\Overview::class)          ->name('club.stock.overview');
    Route::get('stock/jerseys',      \App\Livewire\Club\Stock\Jerseys\Index::class)     ->name('club.stock.jerseys');
    Route::get('stock/equipment',    \App\Livewire\Club\Stock\Equipment\Index::class)   ->name('club.stock.equipment');
    Route::get('stock/suppliers',    \App\Livewire\Club\Stock\Suppliers::class)         ->name('club.stock.suppliers');
    Route::get('stock/inventory.pdf', [StockExportController::class, 'inventoryPdf'])   ->name('club.stock.inventory-pdf');
    Route::get('stock/inventory.csv', [StockExportController::class, 'inventoryCsv'])   ->name('club.stock.inventory-csv');
    Route::get('stock/purchase-order.pdf', [StockExportController::class, 'purchaseOrderPdf'])->name('club.stock.purchase-order-pdf');

    // Planning — static routes first to avoid {id} collision
    Route::get('planning',              \App\Livewire\Club\Planning\Calendar::class)       ->name('club.planning.calendar');
    Route::get('planning/create',       \App\Livewire\Club\Planning\EventForm::class)      ->name('club.planning.create');
    Route::get('planning/fields',       \App\Livewire\Club\Planning\Fields::class)         ->name('club.planning.fields');
    Route::get('planning/export.ics',   IcsExportController::class)                        ->name('club.planning.export-ics');
    Route::get('planning/{id}/edit',        \App\Livewire\Club\Planning\EventForm::class)      ->name('club.planning.edit');
    Route::get('planning/{id}/match-sheet', \App\Livewire\Club\Planning\MatchSheet::class)     ->name('club.planning.match-sheet');
    Route::get('planning/{id}/attendance',  \App\Livewire\Club\Planning\AttendanceSheet::class)->name('club.planning.attendance');
});

// --- Panel Super Administrateur ---
Route::prefix('superadmin')
    ->middleware(['auth', 'super_admin'])
    ->name('superadmin.')
    ->group(function () {

        // Tableau de bord
        Route::get('/', SuperAdminDashboard::class)->name('dashboard');

        // Gestion des clubs
        Route::get('/clubs', ClubIndex::class)->name('clubs.index');
        Route::get('/clubs/create', ClubCreate::class)->name('clubs.create');
        Route::get('/clubs/{tenant}', ClubShow::class)->name('clubs.show');
        Route::get('/clubs/{tenant}/edit', ClubEdit::class)->name('clubs.edit');
        Route::post('/clubs/{tenant}/impersonate', [ImpersonateController::class, 'start'])
            ->name('clubs.impersonate');

        // Gestion des plans
        Route::get('/plans', PlanIndex::class)->name('plans.index');
        Route::get('/plans/create', PlanCreate::class)->name('plans.create');
        Route::get('/plans/{plan}/edit', PlanEdit::class)->name('plans.edit');

        // Gestion des paiements
        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/payments/export', PaymentExportController::class)->name('payments.export');
    });

// Arrêt de l'impersonation (accessible hors préfixe superadmin car l'utilisateur est connecté en tant qu'admin club)
Route::post('/impersonate/stop', [ImpersonateController::class, 'stop'])
    ->middleware('auth')
    ->name('impersonate.stop');

require __DIR__.'/auth.php';
