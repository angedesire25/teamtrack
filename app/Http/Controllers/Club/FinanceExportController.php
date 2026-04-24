<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\SubscriptionPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FinanceExportController extends Controller
{
    public function csv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tenantId = app('tenant')->id;
        $year     = $request->input('year', now()->year);
        $month    = $request->input('month'); // optional

        $rows = collect();

        // Paiements de cotisations
        $payments = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('subscription.player', 'subscription.plan')
            ->when($month, fn ($q) => $q->whereMonth('payment_date', $month))
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();

        foreach ($payments as $p) {
            $rows->push([
                'date'        => $p->payment_date->format('d/m/Y'),
                'type'        => 'Recette',
                'source'      => 'Cotisation',
                'description' => 'Cotisation '.$p->subscription->season.' — '.$p->subscription->player->fullName(),
                'categorie'   => $p->subscription->plan?->name ?? 'Cotisation',
                'method'      => $p->methodLabel(),
                'reference'   => $p->reference ?? '',
                'amount'      => $p->amount,
            ]);
        }

        // Dons
        $donations = Donation::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->with('donor', 'campaign')
            ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();

        foreach ($donations as $d) {
            $rows->push([
                'date'        => $d->created_at->format('d/m/Y'),
                'type'        => 'Recette',
                'source'      => 'Don',
                'description' => 'Don de '.$d->donorName().($d->campaign ? ' — '.$d->campaign->title : ''),
                'categorie'   => 'Dons',
                'method'      => 'Stripe',
                'reference'   => $d->receipt_number ?? '',
                'amount'      => $d->amount,
            ]);
        }

        // Dépenses
        $expenses = Expense::where('tenant_id', $tenantId)
            ->with('category')
            ->when($month, fn ($q) => $q->whereMonth('date', $month))
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        foreach ($expenses as $e) {
            $rows->push([
                'date'        => $e->date->format('d/m/Y'),
                'type'        => 'Dépense',
                'source'      => 'Dépense',
                'description' => $e->description,
                'categorie'   => $e->category?->name ?? 'Non catégorisée',
                'method'      => '',
                'reference'   => $e->reference ?? '',
                'amount'      => -$e->amount,
            ]);
        }

        $rows = $rows->sortBy('date');

        $period = $month ? date('F', mktime(0,0,0,$month,1)).' '.$year : $year;
        $filename = 'export-comptable-'.$period.'.csv';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF"; // BOM UTF-8
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Type', 'Source', 'Description', 'Catégorie', 'Mode', 'Référence', 'Montant (F)'], ';');
            foreach ($rows as $row) {
                fputcsv($handle, array_values($row), ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(Request $request): Response
    {
        $tenantId = app('tenant')->id;
        $tenant   = app('tenant');
        $year     = $request->input('year', now()->year);
        $month    = $request->input('month');

        // Recettes par mois
        $monthlyIncome   = [];
        $monthlyExpenses = [];
        $months = $month ? [$month] : range(1, 12);

        foreach ($months as $m) {
            $subPay = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenantId))
                ->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount');
            $don = Donation::where('tenant_id', $tenantId)->where('status', 'completed')
                ->whereYear('created_at', $year)->whereMonth('created_at', $m)->sum('amount');
            $exp = Expense::where('tenant_id', $tenantId)
                ->whereYear('date', $year)->whereMonth('date', $m)->sum('amount');
            $monthlyIncome[$m]   = $subPay + $don;
            $monthlyExpenses[$m] = $exp;
        }

        // Par catégorie
        $byCategory = Expense::where('tenant_id', $tenantId)
            ->when($month, fn ($q) => $q->whereMonth('date', $month))
            ->whereYear('date', $year)
            ->with('category')
            ->get()
            ->groupBy(fn ($e) => $e->category?->name ?? 'Non catégorisée')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortByDesc(fn ($v) => $v);

        $totalIncome   = array_sum($monthlyIncome);
        $totalExpenses = array_sum($monthlyExpenses);
        $solde         = $totalIncome - $totalExpenses;

        $periodLabel = $month ? \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') : 'Année '.$year;

        $pdf = Pdf::loadView('pdf.finance-report', compact(
            'tenant', 'periodLabel', 'year', 'month',
            'months', 'monthlyIncome', 'monthlyExpenses',
            'byCategory', 'totalIncome', 'totalExpenses', 'solde'
        ))->setPaper('a4', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rapport-financier-'.$year.'.pdf"',
        ]);
    }
}
