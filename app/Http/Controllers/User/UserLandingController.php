<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class UserLandingController extends Controller
{
    public function index(Request $request)
{
    $categories = Category::all();
    $query = Product::query()->active()->inStock();

    if ($request->search) {
        $query->where('name', 'like', "%{$request->search}%");
    }
    if ($request->category) {
        $query->where('category_id', $request->category);
    }
    if ($request->sort) {
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
        }
    } else {
        $query->latest();
    }

    $products = $query->paginate(8);
    $products->appends($request->all());

    if ($request->ajax()) {
        return view('partials.product-user', compact('products'))->render();
    }

    return view('landing.landing-page', compact('products', 'categories'));
}

}
