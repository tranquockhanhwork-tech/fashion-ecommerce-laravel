<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartSidebarComposer
{
    public function compose(View $view): void
    {
        $sidebarItems = collect();
        $sidebarTotal = 0;
        $user = Auth::user();
        $cart = $user?->customer?->cart;

        if ($cart) {
            $sidebarItems = $cart->items()->with([
                'variant' => fn ($query) => $query->withOptionRelations()->with('product.images'),
            ])->get();

            foreach ($sidebarItems as $item) {
                $basePrice = $item->variant->product->promotional_price ?: $item->variant->product->price;
                $itemPrice = $item->variant->price_override ?: $basePrice;
                $sidebarTotal += $itemPrice * $item->quantity;
            }
        }

        $view->with([
            'sidebarItems' => $sidebarItems,
            'sidebarTotal' => $sidebarTotal,
        ]);
    }
}
