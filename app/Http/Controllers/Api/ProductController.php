<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request) {
        $q = Product::with(['category:id,name','supplier:id,name'])->when($request->filled('search'), fn($b)=>$b->where(fn($w)=>$w->where('name','like','%'.$request->search.'%')->orWhere('sku','like','%'.$request->search.'%')))->latest();
        return $q->paginate(min(max((int)$request->integer('per_page',10),1),100));
    }
    public function store(Request $request) { $p=Product::create($this->validated($request)); return response()->json($p->load(['category','supplier']),201); }
    public function show(Product $product) { return $product->load(['category','supplier']); }
    public function update(Request $request, Product $product) { $product->update($this->validated($request,$product)); return $product->fresh()->load(['category','supplier']); }
    public function destroy(Product $product) { if($product->saleItems()->exists()) return response()->json(['message'=>'Product has sales history and cannot be deleted.'],422); $product->delete(); return response()->json(['message'=>'Product deleted']); }
    private function validated(Request $r, ?Product $p=null): array {
        return $r->validate([
            'name'=>'required|string|max:160','sku'=>['required','string','max:80',Rule::unique('products','sku')->ignore($p?->id)],
            'category_id'=>'required|exists:categories,id','supplier_id'=>'nullable|exists:suppliers,id',
            'unit_price'=>'required|numeric|min:0.01|max:99999999','stock_quantity'=>'required|integer|min:0|max:10000000',
            'reorder_level'=>'required|integer|min:0|max:10000000'
        ]);
    }
}
