<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMaintenance;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AdminAssetController extends Controller
{
    protected AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request)
    {
        $query = Asset::with(['category', 'assignedUser']);

        if ($request->filled('category')) {
            $query->where('asset_category_id', $request->category);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($request->assignment === 'available') {
                $query->whereNull('assigned_to');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest()->paginate(15)->withQueryString();
        $categories = AssetCategory::orderBy('name')->get();

        $totalAssets = Asset::count();
        $totalValue = Asset::sum('current_value');
        $assignedCount = Asset::whereNotNull('assigned_to')->count();
        $maintenanceNeeded = Asset::whereIn('condition', ['rusak_ringan', 'rusak_berat'])->count();

        return view('admin.assets.index', compact(
            'assets',
            'categories',
            'totalAssets',
            'totalValue',
            'assignedCount',
            'maintenanceNeeded'
        ));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();

        return view('admin.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name' => 'required|string|max:255',
            'asset_code' => 'required|string|max:255|unique:assets,asset_code',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat,hilang,dihapuskan',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (!isset($validated['current_value'])) {
            $validated['current_value'] = $validated['purchase_price'];
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets/photos', 'public');
        }

        Asset::create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'assignedUser', 'assignments.user', 'assignments.assignedByUser', 'maintenances']);

        $depreciationSchedule = $this->assetService->getDepreciationSchedule($asset);
        $currentDepreciatedValue = $this->assetService->calculateDepreciation($asset);

        return view('admin.assets.show', compact('asset', 'depreciationSchedule', 'currentDepreciatedValue'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();

        return view('admin.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name' => 'required|string|max:255',
            'asset_code' => 'required|string|max:255|unique:assets,asset_code,' . $asset->id,
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat,hilang,dihapuskan',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets/photos', 'public');
        }

        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    public function assign(Asset $asset)
    {
        $users = User::orderBy('name')->get();

        return view('admin.assets.assign', compact('asset', 'users'));
    }

    public function storeAssignment(Request $request, Asset $asset)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($asset->assigned_to) {
            return back()->with('error', 'Aset ini sudah ditugaskan ke karyawan lain. Kembalikan terlebih dahulu.');
        }

        $user = User::findOrFail($request->user_id);
        $this->assetService->assignAsset($asset, $user, auth()->user(), $request->notes);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Aset berhasil ditugaskan ke ' . $user->name . '.');
    }

    public function returnAsset(Request $request, Asset $asset)
    {
        $request->validate([
            'condition_on_return' => 'required|in:baik,rusak_ringan,rusak_berat,hilang,dihapuskan',
            'notes' => 'nullable|string',
        ]);

        $this->assetService->returnAsset($asset, $request->condition_on_return, $request->notes);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Aset berhasil dikembalikan.');
    }

    public function categories()
    {
        $categories = AssetCategory::withCount('assets')->orderBy('name')->get();

        return view('admin.assets.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:asset_categories,code',
            'description' => 'nullable|string',
            'depreciation_method' => 'required|in:straight_line,declining_balance,none',
            'useful_life_years' => 'nullable|integer|min:1|max:100',
        ]);

        AssetCategory::create($validated);

        return redirect()->route('admin.assets.categories')
            ->with('success', 'Kategori aset berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:asset_categories,code,' . $category->id,
            'description' => 'nullable|string',
            'depreciation_method' => 'required|in:straight_line,declining_balance,none',
            'useful_life_years' => 'nullable|integer|min:1|max:100',
        ]);

        $category->update($validated);

        return redirect()->route('admin.assets.categories')
            ->with('success', 'Kategori aset berhasil diperbarui.');
    }

    public function destroyCategory(AssetCategory $category)
    {
        if ($category->assets()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset.');
        }

        $category->delete();

        return redirect()->route('admin.assets.categories')
            ->with('success', 'Kategori aset berhasil dihapus.');
    }

    public function storeMaintenance(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|in:preventive,corrective,upgrade',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'performed_by' => 'nullable|string|max:255',
            'performed_at' => 'required|date',
            'next_maintenance_at' => 'nullable|date|after:performed_at',
        ]);

        $validated['asset_id'] = $asset->id;
        $validated['cost'] = $validated['cost'] ?? 0;

        AssetMaintenance::create($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Log pemeliharaan berhasil ditambahkan.');
    }
}
