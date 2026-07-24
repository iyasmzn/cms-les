<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Return the current cart from session.
     *
     * @return array<int, array{product_id: int, qty: int}>
     */
    private function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Save cart back to session.
     *
     * @param  array<int, array{product_id: int, qty: int}>  $cart
     */
    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /** Total number of distinct items in the cart. */
    public static function itemCount(): int
    {
        return count(session('cart', []));
    }

    public function index(): View
    {
        $cart = $this->getCart();

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = collect($cart)->map(fn ($item, $productId) => [
            'product' => $products->get($productId),
            'qty' => $item['qty'],
        ])->filter(fn ($item) => $item['product'] !== null);

        $total = $items->sum(fn ($item) => $item['product']->price * $item['qty']);

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        abort_unless($product->is_available && $product->stock > 0, 422, 'Produk tidak tersedia.');

        $qty = max(1, (int) $request->input('qty', 1));

        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['qty'] + $qty;
            $cart[$product->id]['qty'] = min($newQty, $product->stock);
        } else {
            $cart[$product->id] = ['product_id' => $product->id, 'qty' => min($qty, $product->stock)];
        }

        $this->saveCart($cart);

        return back()->with('success', "«{$product->title}» ditambahkan ke keranjang.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $qty = max(1, (int) $request->input('qty', 1));

        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] = min($qty, $product->stock);
            $this->saveCart($cart);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        $this->saveCart($cart);

        return redirect()->route('cart.index')
            ->with('success', "«{$product->title}» dihapus dari keranjang.");
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index');
    }
}
