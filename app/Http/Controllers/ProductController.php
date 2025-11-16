<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::paginate(10);

        return view('admin.product', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric'],
                'image' => ['required', 'image', 'max:2048'],
                'stock' => ['required', 'numeric'],
                'category_id' => ['required', 'exists:categories,id'],
            ],);

            $image = $request->file('image');
            $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));
            $validated['image'] = 'data:' . $image->getMimeType() . ';base64,' . $imageBase64;

            Product::create($validated);
            DB::commit();
            return redirect()->back()
            ->with( 'success', 'Product created successfully' );
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()
            ->with( 'error', 'Product not created' );
        }
    }

    public function update(Request $request, Product $product){
        try{
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric'],
                'stock' => ['required', 'numeric'],
                'image' => ['nullable', 'image', 'max:2048'],
                'category_id' => ['required', 'exists:categories,id'],
            ],);

            if($request->hasFile('image')) {
                $image = $request->file('image');
                $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));
                $validated['image'] = 'data:' . $image->getMimeType() . ';base64,' . $imageBase64;
            }

            $product->update($validated);
            DB::commit();
            return redirect()->back()
            ->with( 'success', 'Product updated successfully' );
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()
            ->with( 'error', 'Product not updated' );
        }
    }

    public function destroy(Product $product){
        try{
            DB::beginTransaction();
            $product->delete();
            DB::commit();
            return redirect()->back()
            ->with( 'success', 'Product deleted successfully' );
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()
            ->with( 'error', 'Product not deleted' );
        }
    }
}
