<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;
use App\Models\brands;
use App\Models\categories;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $brands = brands::all();
        $categories = categories::with('sampleProduct')->get();

        // Ambil keyword dari input form (GET)
        $searchKeyword = $request->input('search-keyword');

        // Mulai query produk
        $query = products::with(['brand', 'supplier', 'category'])->orderBy('id', 'asc');

        // Jika ada keyword pencarian, filter berdasarkan nama produk
        if ($searchKeyword) {
            $query->where('name', 'like', '%' . $searchKeyword . '%');
        }

        // Paginate hasilnya
        $products = $query->paginate(10);

        return view('pages.shop', compact('products', 'categories', 'brands'));
    }

    public function ajaxSearchSuggestion(Request $request)
    {
        $keyword = $request->input('q');
        if (!$keyword) return response()->json([]);

        $products = products::select('name')
            ->get()
            ->map(function ($product) use ($keyword) {
                return [
                    'name' => $product->name,
                    'distance' => levenshtein(strtolower($keyword), strtolower($product->name)),
                ];
            })
            ->sortBy('distance')
            ->take(5)
            ->values();

        return response()->json($products);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('q');
        $brands = brands::all();
        $categories = categories::with('sampleProduct')->get();
        

        $allProducts = products::with(['brand', 'supplier', 'category'])->get();

        $matches = [];
        foreach ($allProducts as $product) {
            $distance = levenshtein(strtolower($keyword), strtolower($product->name));
            Log::info("Compare '$keyword' with '{$product->name}' => distance: $distance");
            if ($distance < 5) {
                $matches[] = [
                    'product' => $product,
                    'distance' => $distance,
                ];
            }
        }

        $logDistances = [];
        foreach ($matches as $match) {
            $logDistances[] = [
                'keyword' => $keyword,
                'product_name' => $match['product']->name,
                'distance' => $match['distance'],
            ];
        }

        usort($matches, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $matchedProducts = collect($matches)->pluck('product');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $pagedResults = $matchedProducts->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedProducts = new LengthAwarePaginator($pagedResults, $matchedProducts->count(), $perPage);
        $paginatedProducts->setPath($request->url());
        $paginatedProducts->appends($request->all());

        return view('pages.shop', [
            'products' => $paginatedProducts, // <-- ini yang dipakai untuk @foreach dan pagination
            'logDistances' => $logDistances,
            'categories' => $categories,
            'brands' => $brands,
            'keyword' => $keyword,
            'message' => 'Tidak ditemukan produk yang mirip dengan "' . $keyword . '"'  
        ]);

    }


}
