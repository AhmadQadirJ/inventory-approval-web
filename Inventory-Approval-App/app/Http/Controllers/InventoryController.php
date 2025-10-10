<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('kategori');
        $branchFilter = $request->input('branch');

        $query = Inventory::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($categoryFilter) {
            $query->where('kategori', $categoryFilter);
        }

        if ($branchFilter) {
            $query->where('branch', $branchFilter);
        }

        $inventories = $query->paginate(10)->withQueryString();

        return view('inventory.index', [
            'inventories' => $inventories,
            'categories' => ['Elektronik', 'Non Elektronik', 'Ruangan'],
            'branches' => ['Bandung', 'Jakarta', 'Surabaya'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'brand' => 'required_unless:kategori,Ruangan|nullable|string|max:255',
            'kategori' => 'required|in:Elektronik,Non Elektronik,Ruangan',
            'harga' => 'required_unless:kategori,Ruangan|nullable|numeric',
            'branch' => 'required|in:Bandung,Jakarta,Surabaya',
            'tahun_beli' => 'required_unless:kategori,Ruangan|nullable|digits:4',
            'nama_vendor' => 'required_unless:kategori,Ruangan|nullable|string|max:255',
            'vendor_link' => 'nullable|url',
            'qty' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Buat Kode Otomatis
        $branchCode = strtoupper(substr($validated['branch'], 0, 1));
        $categoryCode = '';
        if ($validated['kategori'] == 'Elektronik') $categoryCode = 'E';
        if ($validated['kategori'] == 'Non Elektronik') $categoryCode = 'F';
        if ($validated['kategori'] == 'Ruangan') $categoryCode = 'G';

        $lastItem = Inventory::where('kode', 'like', "{$branchCode}-{$categoryCode}-%")->latest('id')->first();
        $nextNumber = $lastItem ? intval(substr($lastItem->kode, -6)) + 1 : 1;
        $validated['kode'] = "{$branchCode}-{$categoryCode}-" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('inventory-photos', 'public');
        }

        Inventory::create($validated);

        return redirect()->route('inventory')->with('success', 'New item added successfully.');
    }


    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'brand' => 'required_unless:kategori,Ruangan|nullable|string|max:255',
            'kategori' => 'required|in:Elektronik,Non Elektronik,Ruangan',
            'harga' => 'required_unless:kategori,Ruangan|nullable|numeric',
            'branch' => 'required|in:Bandung,Jakarta,Surabaya',
            'tahun_beli' => 'required_unless:kategori,Ruangan|nullable|digits:4',
            'nama_vendor' => 'required_unless:kategori,Ruangan|nullable|string|max:255',
            'vendor_link' => 'nullable|url',
            'qty' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($inventory->gambar) {
                Storage::disk('public')->delete($inventory->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('inventory-photos', 'public');
        }

        $inventory->update($validated);

        return redirect()->route('inventory')->with('success', 'Item updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->gambar) {
            Storage::disk('public')->delete($inventory->gambar);
        }
        $inventory->delete();
        return redirect()->route('inventory')->with('success', 'Item deleted successfully.');
    }

    public function show(Inventory $inventory)
    {
        return view('inventory.show', compact('inventory'));
    }

    public function getCategoriesForBranch(Request $request)
    {
        $branch = $request->query('branch');
        if (!$branch) {
            return response()->json([]);
        }

        $categories = Inventory::where('branch', $branch)
            ->select('kategori')
            ->distinct()
            ->get();

        return response()->json($categories);
    }

    public function getItemsForCategory(Request $request)
    {
        $branch = $request->query('branch');
        $category = $request->query('kategori');

        if (!$branch || !$category) {
            return response()->json([]);
        }

        $items = Inventory::where('branch', $branch)
            ->where('kategori', $category)
            ->select('id', 'nama', 'kode')
            ->get();

        return response()->json($items);
    }
}