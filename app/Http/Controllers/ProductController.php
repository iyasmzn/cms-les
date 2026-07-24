<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $sort = (string) $request->query('sort', 'default');
        $minPrice = $request->query('min_price') !== null ? (int) $request->query('min_price') : null;
        $maxPrice = $request->query('max_price') !== null ? (int) $request->query('max_price') : null;
        $inStock = $request->boolean('in_stock');

        $products = Product::available()
            ->when($q !== '', fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            }))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($minPrice !== null, fn ($query) => $query->where('price', '>=', $minPrice))
            ->when($maxPrice !== null, fn ($query) => $query->where('price', '<=', $maxPrice))
            ->when($inStock, fn ($query) => $query->where('stock', '>', 0))
            ->when($sort === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when($sort === 'name_asc', fn ($query) => $query->orderBy('title'))
            ->when($sort === 'newest', fn ($query) => $query->latest())
            ->when($sort === 'default', fn ($query) => $query->orderBy('sort_order')->orderBy('title'))
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Product::available()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Price bounds for the range slider
        $priceStats = Product::available()->selectRaw('MIN(price) as min_p, MAX(price) as max_p')->first();
        $priceMin = (int) ($priceStats->min_p ?? 0);
        $priceMax = (int) ($priceStats->max_p ?? 200000);

        // Active filter count (for badge on mobile button)
        $activeFilters = collect([
            $q !== '',
            $category !== '',
            $minPrice !== null || $maxPrice !== null,
            $inStock,
        ])->filter()->count();

        $seo = [
            'title' => 'Toko | '.config('app.name'),
            'description' => 'Belanja produk pilihan dari '.config('app.name').'. Beragam produk berkualitas dengan harga terbaik.',
            'canonical' => route('products.index'),
        ];

        return view('products.index', compact(
            'products', 'categories', 'category',
            'q', 'sort', 'minPrice', 'maxPrice', 'inStock',
            'priceMin', 'priceMax', 'activeFilters', 'seo',
        ));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_available, 404);

        $related = Product::available()
            ->where('id', '!=', $product->id)
            ->where('category', $product->category)
            ->ordered()
            ->limit(4)
            ->get();

        $seo = [
            'title' => "{$product->title} | ".config('app.name'),
            'description' => $product->description
                ? Str::limit(strip_tags($product->description), 155)
                : "Beli {$product->title} di toko ".config('app.name'),
            'canonical' => route('products.show', $product),
            'og_image' => $product->cover_url,
        ];

        return view('products.show', compact('product', 'related', 'seo'));
    }
}
