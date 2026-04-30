<?php

namespace App\Http\Controllers;

use App\Models\BpjsRateVersion;
use App\Models\FormulaAuditLog;
use App\Models\TaxPtkpRateVersion;
use App\Models\TaxTerRateVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRateManagementController extends Controller
{
    // ─── Dashboard ───────────────────────────────────────────────
    public function index()
    {
        $today = now()->toDateString();

        $bpjsActive = BpjsRateVersion::where('effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $today))
            ->count();

        $bpjsLastUpdated = BpjsRateVersion::max('updated_at');

        $terActive = TaxTerRateVersion::where('effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $today))
            ->count();

        $terLastUpdated = TaxTerRateVersion::max('updated_at');

        $ptkpActive = TaxPtkpRateVersion::where('effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $today))
            ->count();

        $ptkpLastUpdated = TaxPtkpRateVersion::max('updated_at');

        return view('admin.rate-management.index', compact(
            'bpjsActive',
            'bpjsLastUpdated',
            'terActive',
            'terLastUpdated',
            'ptkpActive',
            'ptkpLastUpdated',
        ));
    }

    // ─── BPJS Rates ──────────────────────────────────────────────
    public function bpjsRates()
    {
        $rates = BpjsRateVersion::orderBy('program')
            ->orderByDesc('effective_from')
            ->get()
            ->groupBy('program');

        return view('admin.rate-management.bpjs', compact('rates'));
    }

    public function createBpjsRate()
    {
        $programs = ['jht', 'jkk', 'jkm', 'jp', 'bpjs_kesehatan'];

        return view('admin.rate-management.bpjs-create', compact('programs'));
    }

    public function storeBpjsRate(Request $request)
    {
        $validated = $request->validate([
            'program'          => 'required|in:jht,jkk,jkm,jp,bpjs_kesehatan',
            'employer_rate'    => 'required|numeric|min:0|max:1',
            'employee_rate'    => 'required|numeric|min:0|max:1',
            'max_salary_basis' => 'nullable|numeric|min:0',
            'min_salary_basis' => 'nullable|numeric|min:0',
            'effective_from'   => 'required|date',
            'notes'            => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            // Auto-close previous active version for same program
            BpjsRateVersion::where('program', $validated['program'])
                ->whereNull('effective_until')
                ->update([
                    'effective_until' => \Carbon\Carbon::parse($validated['effective_from'])->subDay()->toDateString(),
                ]);

            $rate = BpjsRateVersion::create([
                ...$validated,
                'created_by' => auth()->id(),
            ]);

            FormulaAuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'create_bpjs_rate',
                'entity_type' => 'BpjsRateVersion',
                'entity_id'   => $rate->id,
                'old_formula' => null,
                'new_formula' => json_encode($validated),
                'ip_address'  => request()->ip(),
                'metadata'    => ['program' => $validated['program'], 'effective_from' => $validated['effective_from']],
                'created_at'  => now(),
            ]);
        });

        return redirect()
            ->route('admin.rate-management.bpjs')
            ->with('success', 'Tarif BPJS berhasil ditambahkan.');
    }

    // ─── TER Rates ───────────────────────────────────────────────
    public function terRates()
    {
        $regulations = TaxTerRateVersion::orderByDesc('effective_from')
            ->orderBy('category')
            ->orderBy('min_income')
            ->get()
            ->groupBy('regulation_code');

        return view('admin.rate-management.ter', compact('regulations'));
    }

    public function createTerRegulation()
    {
        // Get latest regulation for pre-fill
        $latestCode = TaxTerRateVersion::orderByDesc('effective_from')->value('regulation_code');
        $existingRates = [];

        if ($latestCode) {
            $existingRates = TaxTerRateVersion::where('regulation_code', $latestCode)
                ->orderBy('category')
                ->orderBy('min_income')
                ->get()
                ->groupBy('category')
                ->map(fn ($group) => $group->map(fn ($r) => [
                    'min_income' => $r->min_income,
                    'max_income' => $r->max_income,
                    'rate'       => $r->rate,
                ]))
                ->toArray();
        }

        return view('admin.rate-management.ter-create', compact('latestCode', 'existingRates'));
    }

    public function storeTerRegulation(Request $request)
    {
        $request->validate([
            'regulation_code' => 'required|string|max:50',
            'effective_from'  => 'required|date',
            'rates'           => 'required|array|min:1',
            'rates.*.category'   => 'required|in:A,B,C',
            'rates.*.min_income' => 'required|numeric|min:0',
            'rates.*.max_income' => 'nullable|numeric|min:0',
            'rates.*.rate'       => 'required|numeric|min:0|max:1',
        ]);

        DB::transaction(function () use ($request) {
            $effectiveFrom = $request->effective_from;

            // Auto-close existing open TER versions
            TaxTerRateVersion::whereNull('effective_until')
                ->update([
                    'effective_until' => \Carbon\Carbon::parse($effectiveFrom)->subDay()->toDateString(),
                ]);

            $insertedIds = [];
            foreach ($request->rates as $rateData) {
                $rate = TaxTerRateVersion::create([
                    'regulation_code' => $request->regulation_code,
                    'category'        => $rateData['category'],
                    'min_income'      => $rateData['min_income'],
                    'max_income'      => $rateData['max_income'] ?? null,
                    'rate'            => $rateData['rate'],
                    'effective_from'  => $effectiveFrom,
                    'created_by'      => auth()->id(),
                ]);
                $insertedIds[] = $rate->id;
            }

            FormulaAuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'create_ter_regulation',
                'entity_type' => 'TaxTerRateVersion',
                'entity_id'   => $insertedIds[0] ?? null,
                'old_formula' => null,
                'new_formula' => json_encode([
                    'regulation_code' => $request->regulation_code,
                    'total_rates'     => count($request->rates),
                ]),
                'ip_address'  => request()->ip(),
                'metadata'    => [
                    'regulation_code' => $request->regulation_code,
                    'effective_from'  => $effectiveFrom,
                    'rate_count'      => count($request->rates),
                    'inserted_ids'    => $insertedIds,
                ],
                'created_at'  => now(),
            ]);
        });

        return redirect()
            ->route('admin.rate-management.ter')
            ->with('success', 'Regulasi TER berhasil dipublikasikan.');
    }

    // ─── PTKP Rates ──────────────────────────────────────────────
    public function ptkpRates()
    {
        $rates = TaxPtkpRateVersion::orderByDesc('effective_from')
            ->orderBy('status')
            ->get();

        return view('admin.rate-management.ptkp', compact('rates'));
    }

    public function createPtkpRates()
    {
        $statuses = ['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/0', 'K/1', 'K/2', 'K/3'];

        // Pre-fill from current active values
        $currentRates = TaxPtkpRateVersion::whereNull('effective_until')
            ->orderBy('status')
            ->pluck('amount', 'status')
            ->toArray();

        return view('admin.rate-management.ptkp-create', compact('statuses', 'currentRates'));
    }

    public function storePtkpRates(Request $request)
    {
        $request->validate([
            'effective_from'    => 'required|date',
            'rates'             => 'required|array|size:8',
            'rates.*.status'    => 'required|string|in:TK/0,TK/1,TK/2,TK/3,K/0,K/1,K/2,K/3',
            'rates.*.amount'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $effectiveFrom = $request->effective_from;

            // Auto-close existing open PTKP versions
            TaxPtkpRateVersion::whereNull('effective_until')
                ->update([
                    'effective_until' => \Carbon\Carbon::parse($effectiveFrom)->subDay()->toDateString(),
                ]);

            $insertedIds = [];
            foreach ($request->rates as $rateData) {
                $rate = TaxPtkpRateVersion::create([
                    'status'         => $rateData['status'],
                    'amount'         => $rateData['amount'],
                    'effective_from' => $effectiveFrom,
                    'created_by'     => auth()->id(),
                ]);
                $insertedIds[] = $rate->id;
            }

            FormulaAuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'create_ptkp_rates',
                'entity_type' => 'TaxPtkpRateVersion',
                'entity_id'   => $insertedIds[0] ?? null,
                'old_formula' => null,
                'new_formula' => json_encode([
                    'effective_from' => $effectiveFrom,
                    'total_rates'    => count($request->rates),
                ]),
                'ip_address'  => request()->ip(),
                'metadata'    => [
                    'effective_from' => $effectiveFrom,
                    'rate_count'     => count($request->rates),
                    'inserted_ids'   => $insertedIds,
                ],
                'created_at'  => now(),
            ]);
        });

        return redirect()
            ->route('admin.rate-management.ptkp')
            ->with('success', 'Tarif PTKP berhasil dipublikasikan.');
    }
}
