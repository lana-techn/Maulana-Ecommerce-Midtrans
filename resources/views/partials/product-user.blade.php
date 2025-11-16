{{-- resources/views/partials/products.blade.php --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse ($products as $product)
        <div class="product-card bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition-transform transform hover:scale-105"
            data-id="{{ $product->id }}"
            data-name="{{ $product->name }}"
            data-price="{{ $product->price }}"
            data-image="{{ $product->getPrimaryImage() }}"
            data-category="{{ $product->category->name }}">
            <!-- Product Image -->
            <div class="aspect-w-1 aspect-h-1">
                <img src="{{ $product->getPrimaryImage() }}" alt="{{ $product->name }}"
                    class="w-full h-48 sm:h-72 object-cover rounded-t-lg" />
            </div>
            <!-- Product Info -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ Str::limit($product->description, 40) }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-emerald-600 font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <button
                        class="add-to-cart-btn bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    @empty
        <!-- No Results -->
        <div id="noResults" class="col-span-full text-center py-12">
            <h3 class="text-lg font-medium text-gray-900">No products found</h3>
            <p class="mt-2 text-gray-500">Try adjusting your search terms or filters.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if ($products->hasPages())
    <div class="mt-6">
        <nav class="justify-center flex-wrap gap-2" id="pagination-links">
            {{ $products->links() }}
        </nav>
    </div>
@endif
