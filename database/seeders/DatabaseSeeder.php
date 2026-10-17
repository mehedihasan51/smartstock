<?php
namespace Database\Seeders;
use App\Models\User; use App\Models\Category; use App\Models\Supplier; use App\Models\Product;
use Illuminate\Database\Seeder; use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  User::updateOrCreate(['email'=>'admin@smartstock.test'],['name'=>'SmartStock Admin','password'=>Hash::make('Password123!'),'role'=>'admin']);
  User::updateOrCreate(['email'=>'staff@smartstock.test'],['name'=>'SmartStock Staff','password'=>Hash::make('Password123!'),'role'=>'staff']);
  $electronics=Category::firstOrCreate(['name'=>'Electronics'],['description'=>'Electronic accessories and devices']);
  $office=Category::firstOrCreate(['name'=>'Office Supplies'],['description'=>'Everyday office products']);
  $supplier=Supplier::firstOrCreate(['name'=>'Demo Supplier'],['email'=>'supplier@smartstock.test','company'=>'Demo Trading']);
  Product::firstOrCreate(['sku'=>'KB-001'],['name'=>'USB Keyboard','category_id'=>$electronics->id,'supplier_id'=>$supplier->id,'unit_price'=>850,'stock_quantity'=>8,'reorder_level'=>10]);
  Product::firstOrCreate(['sku'=>'MS-001'],['name'=>'Wireless Mouse','category_id'=>$electronics->id,'supplier_id'=>$supplier->id,'unit_price'=>650,'stock_quantity'=>25,'reorder_level'=>8]);
  Product::firstOrCreate(['sku'=>'NB-001'],['name'=>'Notebook A5','category_id'=>$office->id,'supplier_id'=>$supplier->id,'unit_price'=>90,'stock_quantity'=>100,'reorder_level'=>20]);
 }
}
