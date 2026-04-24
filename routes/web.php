<?php

use App\Http\Controllers\Club\DocumentController;
use App\Http\Controllers\Club\DonationExportController;
use App\Http\Controllers\Club\FinanceExportController;
use App\Http\Controllers\Club\IcsExportController;
use App\Http\Controllers\Club\MedicalExportController;
use App\Http\Controllers\Club\StockExportController;
use App\Http\Controllers\Club\TransferExportController;
use App\Http\Controllers\SuperAdmin\ImpersonateController;
use App\Http\Controllers\SuperAdmin\InvoiceController;
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

// --- Pages publiques de dons (pas d'auth, tenant résolu via middleware global) ---
Route::get('dons',          \App\Livewire\Public\DonationPage::class)   ->name('public.donation');
Route::get('dons/merci',    \App\Livewire\Public\DonationSuccess::class)->name('public.donation.success');
Route::get('dons/{campaign}', \App\Livewire\Public\DonationPage::class) ->name('public.donation.campaign');
Route::post('stripe/webhook', \App\Http\Controllers\Public\StripeWebhookController::class)->name('stripe.webhook');

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

    // Transferts — routes statiques déclarées avant la route dynamique {transfer}
    Route::get('transfers',                \App\Livewire\Club\Transfers\Dashboard::class)       ->name('club.transfers.dashboard');
    Route::get('transfers/outgoing',       \App\Livewire\Club\Transfers\Outgoing\Index::class)  ->name('club.transfers.outgoing');
    Route::get('transfers/incoming',       \App\Livewire\Club\Transfers\Incoming\Index::class)  ->name('club.transfers.incoming');
    Route::get('transfers/windows',        \App\Livewire\Club\Transfers\Windows::class)         ->name('club.transfers.windows');
    Route::get('transfers/export/register.pdf', [TransferExportController::class, 'registerPdf'])->name('club.transfers.register-pdf');
    Route::get('transfers/export/{transfer}/file.pdf', [TransferExportController::class, 'filePdf'])->name('club.transfers.file-pdf');
    Route::get('transfers/{transfer}',     \App\Livewire\Club\Transfers\Show::class)            ->name('club.transfers.show');

    // Finances & Cotisations
    Route::get('finance',             \App\Livewire\Club\Finance\Dashboard::class)              ->name('club.finance.dashboard');
    Route::get('finance/subscriptions', \App\Livewire\Club\Finance\Subscriptions\Index::class) ->name('club.finance.subscriptions');
    Route::get('finance/expenses',    \App\Livewire\Club\Finance\Expenses\Index::class)        ->name('club.finance.expenses');
    Route::get('finance/export.csv',  [FinanceExportController::class, 'csv'])                 ->name('club.finance.export-csv');
    Route::get('finance/export.pdf',  [FinanceExportController::class, 'pdf'])                 ->name('club.finance.export-pdf');

    // Documents & Administratif — routes statiques avant {document}
    Route::get('documents',                      \App\Livewire\Club\Documents\Index::class)         ->name('club.documents.index');
    Route::get('documents/player/{player}/zip',  [DocumentController::class, 'zipDossier'])         ->name('club.documents.player-zip');
    Route::get('documents/player/{player}',      \App\Livewire\Club\Documents\PlayerDossier::class) ->name('club.documents.player');
    Route::get('documents/download/{document}',  [DocumentController::class, 'download'])           ->name('club.documents.download');

    // Médical & Suivi joueurs
    Route::get('medical',                      \App\Livewire\Club\Medical\Overview::class)    ->name('club.medical.overview');
    Route::get('medical/report.pdf',           [MedicalExportController::class, 'weeklyReport'])->name('club.medical.report-pdf');
    Route::get('medical/{player}',             \App\Livewire\Club\Medical\Record::class)      ->name('club.medical.record');

    // Dons — tableau de bord club
    Route::get('donations',           \App\Livewire\Club\Donations\Dashboard::class)         ->name('club.donations.dashboard');
    Route::get('donations/campaigns', \App\Livewire\Club\Donations\Campaigns\Index::class)   ->name('club.donations.campaigns');
    Route::get('donations/donors',    \App\Livewire\Club\Donations\Donors\Index::class)      ->name('club.donations.donors');
    Route::get('donations/export.csv', [DonationExportController::class, 'csv'])             ->name('club.donations.export-csv');
    Route::get('donations/receipt/{donation}', [DonationExportController::class, 'receiptPdf'])->name('club.donations.receipt-pdf');

    // Planning — routes statiques déclarées en premier pour éviter les conflits avec {id}
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

        // Finances
        Route::get('/finance/unpaid',                  \App\Livewire\SuperAdmin\Finance\Unpaid::class)    ->name('finance.unpaid');
        Route::get('/finance/discounts',               \App\Livewire\SuperAdmin\Finance\Discounts::class) ->name('finance.discounts');
        Route::get('/finance/invoices',                \App\Livewire\SuperAdmin\Finance\Invoices::class)  ->name('finance.invoices');
        Route::get('/finance/invoices/{invoice}/pdf',  [InvoiceController::class, 'download'])            ->name('invoices.download');

        // Communication
        Route::get('/communication/messaging',     \App\Livewire\SuperAdmin\Communication\Messaging::class)     ->name('communication.messaging');
        Route::get('/communication/announcements', \App\Livewire\SuperAdmin\Communication\Announcements::class) ->name('communication.announcements');
        Route::get('/communication/templates',     \App\Livewire\SuperAdmin\Communication\Templates::class)     ->name('communication.templates');

        // Administrateurs
        Route::get('/admins',       \App\Livewire\SuperAdmin\Admins\Accounts::class) ->name('admins.index');
        Route::get('/admins/audit', \App\Livewire\SuperAdmin\Admins\AuditLog::class) ->name('admins.audit');

        // Paramètres
        Route::get('/settings/company',     \App\Livewire\SuperAdmin\Settings\Company::class)     ->name('settings.company');
        Route::get('/settings/smtp',        \App\Livewire\SuperAdmin\Settings\Smtp::class)        ->name('settings.smtp');
        Route::get('/settings/stripe',      \App\Livewire\SuperAdmin\Settings\Stripe::class)      ->name('settings.stripe');
        Route::get('/settings/trials',      \App\Livewire\SuperAdmin\Settings\Trials::class)      ->name('settings.trials');
        Route::get('/settings/maintenance', \App\Livewire\SuperAdmin\Settings\Maintenance::class) ->name('settings.maintenance');
        Route::get('/settings/legal',       \App\Livewire\SuperAdmin\Settings\Legal::class)       ->name('settings.legal');
    });

// Arrêt de l'impersonation (accessible hors préfixe superadmin car l'utilisateur est connecté en tant qu'admin club)
Route::post('/impersonate/stop', [ImpersonateController::class, 'stop'])
    ->middleware('auth')
    ->name('impersonate.stop');

require __DIR__.'/auth.php';
