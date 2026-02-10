<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Product;

trait HandlesCart{
    protected function getCartWithProducts(array $cart) : array{
        if(empty($cart)){
            return [];
        }

        $productIds = collect($cart)->pluck('id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)->map(function($item) use ($products){
            $product = $products->get($item['id']);
            if(!$product){
                return null;
            }
            $itemTotal = $product->price * $item['quantity'];
            return [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
                'product' => $product,
                'item_total' => $itemTotal,
            ];
        })->filter()->values()->toArray();

    }
    protected function calculateSubTotal(array $cartItems): float{
        return collect($cartItems)->sum(function($item){
            return $item['product']['price'] * $item['quantity'];
        });
    }
}