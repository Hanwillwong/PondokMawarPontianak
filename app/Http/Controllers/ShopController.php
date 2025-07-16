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
        // Ambil data brand dan kategori, lengkap dengan jumlah produk
        $brands = brands::withCount('products')->get();
        $categories = categories::with('sampleProduct')->withCount('products')->get();

        // Ambil input filter dari request
        $searchKeyword = $request->input('search-keyword');
        $selectedBrands = $request->input('brands', []);
        $selectedCategories = $request->input('categories', []);
        $sortOption = $request->input('sort'); // << ini tambahan untuk sorting

        // Query awal produk
        $query = products::with(['brand', 'supplier', 'category']);

        // Filter berdasarkan keyword
        if ($searchKeyword) {
            $query->where('name', 'like', '%' . $searchKeyword . '%');
        }

        // Filter berdasarkan kategori
        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Filter berdasarkan brand
        if (!empty($selectedBrands)) {
            $query->whereIn('brand_id', $selectedBrands);
        }

        // Sorting
        switch ($sortOption) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc'); // default
        }

        // Paginate + withQueryString agar filter tetap aktif saat pindah halaman
        $products = $query->paginate(10);
        $products->appends($request->query());

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
        $keyword = strtolower($request->input('q'));
        $brands = brands::all();
        $categories = categories::with('sampleProduct')->get();

        $allProducts = products::with(['brand', 'supplier', 'category'])->get();

        $keywordWords = explode(' ', $keyword); // Pecah input keyword menjadi kata-kata
        $matches = [];

        foreach ($allProducts as $product) {
            $productNameWords = explode(' ', strtolower($product->name));
            $minDistance = PHP_INT_MAX;

            // Bandingkan setiap kata di keyword dengan setiap kata di nama produk
            foreach ($keywordWords as $kw) {
                foreach ($productNameWords as $pnw) {
                    $distance = levenshtein($kw, $pnw);
                    $minDistance = min($minDistance, $distance);
                }
            }

            // Simpan kalau ada kecocokan cukup dekat (bisa diatur toleransinya di sini)
            if ($minDistance <= 3) {
                $matches[] = [
                    'product' => $product,
                    'distance' => $minDistance,
                ];
            }
        }

        // Urutkan dari yang paling mirip
        usort($matches, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $matchedProducts = collect($matches)->pluck('product');

        // Paginasi
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $pagedResults = $matchedProducts->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedProducts = new LengthAwarePaginator($pagedResults, $matchedProducts->count(), $perPage);
        $paginatedProducts->setPath($request->url());
        $paginatedProducts->appends($request->all());

        return view('pages.shop', [
            'products' => $paginatedProducts,
            'logDistances' => [], // bisa isi log kalau perlu debugging
            'categories' => $categories,
            'brands' => $brands,
            'keyword' => $keyword,
            'message' => $matchedProducts->isEmpty()
                ? 'Tidak ditemukan produk yang mirip dengan "' . $keyword . '"'
                : null,
        ]);
    }




}
