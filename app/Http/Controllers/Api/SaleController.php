<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request) {
        return Sale::with(['user:id,name','items.product:id,name,sku'])->latest()->paginate(min(max((int)$request->integer('per_page',10),1),100));
    }
    public function store(Request $request) {
        $data=$request->validate(['items'=>'required|array|min:1|max:50','items.*.product_id'=>'required|integer|distinct|exists:products,id','items.*.quantity'=>'required|integer|min:1|max:100000']);
        $sale=DB::transaction(function() use($data,$request) {
            $total=0; $prepared=[];
            foreach($data['items'] as $item) {
                $product=Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                if($product->stock_quantity < $item['quantity']) throw ValidationException::withMessages(['items'=>["Insufficient stock for {$product->name}. Available: {$product->stock_quantity}."]]);
                $price=(float)$product->unit_price; $line=round($price*(int)$item['quantity'],2); $total+=$line;
                $prepared[]=['product'=>$product,'quantity'=>(int)$item['quantity'],'unit_price'=>$price,'line_total'=>$line];
            }
            $sale=Sale::create(['user_id'=>$request->user()->id,'total_amount'=>round($total,2),'sold_at'=>now()]);
            foreach($prepared as $row) {
                $sale->items()->create(['product_id'=>$row['product']->id,'quantity'=>$row['quantity'],'unit_price'=>$row['unit_price'],'line_total'=>$row['line_total']]);
                $row['product']->decrement('stock_quantity',$row['quantity']);
            }
            return $sale->load(['user:id,name','items.product:id,name,sku']);
        });
        return response()->json($sale,201);
    }
}
