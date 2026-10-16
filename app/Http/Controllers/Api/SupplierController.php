<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
class SupplierController extends Controller {
 public function index(){return Supplier::withCount('products')->orderBy('name')->get();}
 public function store(Request $r){return response()->json(Supplier::create($r->validate(['name'=>'required|string|max:120','email'=>'nullable|email|max:190','phone'=>'nullable|string|max:40','company'=>'nullable|string|max:160'])),201);}
 public function show(Supplier $supplier){return $supplier->loadCount('products');}
 public function update(Request $r,Supplier $supplier){$supplier->update($r->validate(['name'=>'required|string|max:120','email'=>'nullable|email|max:190','phone'=>'nullable|string|max:40','company'=>'nullable|string|max:160']));return $supplier;}
 public function destroy(Supplier $supplier){if($supplier->products()->exists())return response()->json(['message'=>'Supplier is assigned to products.'],422);$supplier->delete();return response()->json(['message'=>'Supplier deleted']);}
}
