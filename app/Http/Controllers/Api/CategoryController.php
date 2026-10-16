<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller {
 public function index(){return Category::withCount('products')->orderBy('name')->get();}
 public function store(Request $r){return Category::create($r->validate(['name'=>'required|string|max:120|unique:categories,name','description'=>'nullable|string|max:500']));}
 public function show(Category $category){return $category->loadCount('products');}
 public function update(Request $r,Category $category){$d=$r->validate(['name'=>'required|string|max:120|unique:categories,name,'.$category->id,'description'=>'nullable|string|max:500']);$category->update($d);return $category;}
 public function destroy(Category $category){if($category->products()->exists())return response()->json(['message'=>'Move or remove this category’s products first.'],422);$category->delete();return response()->json(['message'=>'Category deleted']);}
}
