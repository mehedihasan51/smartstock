<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller {
 public function __invoke(){
  return response()->json([
   'product_count'=>Product::count(),
   'stock_units'=>(int)Product::sum('stock_quantity'),
   'stock_value'=>(float)Product::select(DB::raw('COALESCE(SUM(unit_price * stock_quantity),0) as value'))->value('value'),
   'low_stock_count'=>Product::whereColumn('stock_quantity','<=','reorder_level')->count(),
   'sales_today'=>(float)Sale::whereDate('sold_at',today())->sum('total_amount'),
   'recent_sales'=>Sale::with('user:id,name')->latest()->limit(5)->get(),
  ]);
 }
}
