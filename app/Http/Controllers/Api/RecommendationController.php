<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class RecommendationController extends Controller {
 public function __invoke(){
  $since=now()->subDays(30);
  $sales=DB::table('sale_items')->join('sales','sales.id','=','sale_items.sale_id')->where('sales.sold_at','>=',$since)->select('sale_items.product_id',DB::raw('SUM(sale_items.quantity) as units'))->groupBy('sale_items.product_id')->pluck('units','product_id');
  $items=Product::with('category:id,name')->get()->map(function($p) use($sales){
   $units=(float)($sales[$p->id]??0); $daily=$units/30; $days=$daily>0?round($p->stock_quantity/$daily,1):null;
   $target=max((int)$p->reorder_level,(int)ceil($daily*14)); $qty=max(0,$target-(int)$p->stock_quantity);
   $urgency=($p->stock_quantity<=0 || ($days!==null && $days<=3))?'critical':(($p->stock_quantity<=$p->reorder_level || ($days!==null && $days<=7))?'high':'normal');
   return ['product_id'=>$p->id,'name'=>$p->name,'sku'=>$p->sku,'category'=>$p->category?->name,'stock_quantity'=>$p->stock_quantity,'average_daily_sales'=>round($daily,2),'estimated_days_remaining'=>$days,'recommended_reorder_qty'=>$qty,'urgency'=>$urgency,'reason'=>$units>0?"Based on {$units} units sold in the last 30 days.":"No sales history in the last 30 days; recommendation uses reorder level only."];
  })->sortBy(fn($p)=>['critical'=>0,'high'=>1,'normal'=>2][$p['urgency']])->values();
  return response()->json(['method'=>'30-day average demand heuristic','period_days'=>30,'recommendations'=>$items]);
 }
}
