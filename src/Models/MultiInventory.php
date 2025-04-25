namespace Botble\MultiInventory\Models;

use Eloquent;

class MultiInventory extends Eloquent
{
    protected $table = 'multi_inventories';
    protected $fillable = ['product_id', 'warehouse', 'quantity'];

    public function product()
    {
        return $this->belongsTo(\Botble\Ecommerce\Models\Product::class);
    }
}
