<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model {
    protected $fillable = ['name','sku','category_id','supplier_id','unit_price','stock_quantity','reorder_level'];
    protected function casts(): array { return ['unit_price'=>'decimal:2','stock_quantity'=>'integer','reorder_level'=>'integer']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }
}
