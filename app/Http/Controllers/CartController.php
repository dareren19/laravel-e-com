<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Http\Controllers\Concerns\HandlesCart;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseIsRedirected;

class CartController extends Controller
{
    
    use HandlesCart;
    public function index() {
        $cart = $this->getCart();
        $cartItems = $this->getCartWithProducts($cart);
        $subtotal = $this->calculateSubTotal($cartItems);
        // dd($cartItems);
        return view('cart' , ['cartItems' => $cartItems, 'subtotal' => $subtotal]);
    }
    public function add(Request $request): RedirectResponse {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity" => 'required|integer|min:1',
        ]);

        $product = Product::findorfail($request->product_id);
        $cart = $this->getCart();

        $existingItem = collect($cart)->firstWhere('id', $product->id);
        if($existingItem){
            $cart = collect($cart)->map(function($item) use ($product, $request){
                if($item['id'] === $product->id){
                    $newQuantity = $item['quantity'] + $request->quantity;
                    $item['quantity'] = min($newQuantity, $product->stock);
                }
                return $item;
            })->toArray();
        }else{
            $cart[] = [
                "id" => $product->id,
                "quantity" => min($request->quantity, $product->stock),
            ];
        }
        session(['cart' => $cart]);
        return redirect()->back()->with('success' , "Product added to cart.");
    }
    // public function update(Request $request, int $id):  RedirectResponse {
        
    //     $request->validate([
    //         'quantity' => "required|integer|min:1",
    //     ]);
    //     $product = Product::findorfail($id);
    //     $cart = $this->getCart();

    //     $cart = collect($cart)->map(function($item) use ($id, $request, $product){
    //         if($item['id'] === $id){
    //             $item['quantity'] = min($request->quantity, $product->stock);
    //         }
    //         return $item;
    //     })->toArray();
    //     session(['cart' => $cart]);
    //     return redirect()->back();
        
    // }
    
  public function update(Request $request, int $id): RedirectResponse
{
    $product = Product::findOrFail($id);
    $cart = $this->getCart();

    $cart = collect($cart)->map(function ($item) use ($id, $request, $product) {

        if ($item['id'] === $id) {

            if ($request->action === 'increase') {
                if ($item['quantity'] < $product->stock) {
                    $item['quantity']++;
                }
            }

            if ($request->action === 'decrease') {
                if ($item['quantity'] > 1) {
                    $item['quantity']--;
                }
            }
        }
        
        return $item;
    })->toArray();

    session(['cart' => $cart]);

    return redirect()->back()->with('success' , "Product updated to cart.");
}


    public function remove(int $id): RedirectResponse{
        $cart = $this->getCart();
        $cart = collect($cart)->filter(fn($item)=> $item['id'] !== $id)->values()->toArray();
        session(['cart' => $cart]);
        
        return redirect()->back()->with('success' , "Item removed from the cart.");
    }
    public function clear(): RedirectResponse{
        session()->forget('cart');
        return redirect()->back()->with('success' , "Cart empty.");
    }
    public function getCart(): array {
        return session('cart', []);
    }
    
}
