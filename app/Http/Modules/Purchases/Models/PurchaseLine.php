<?php

/**
 * Created by Reliese Model.
 */

 namespace App\Http\Modules\Purchases\Models;

use App\Http\Modules\Entrances\Models\Entrance;
use App\Http\Modules\Products\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseLine
 *
 * @property int $id
 * @property float $price
 * @property float $quantity
 * @property int $purchase_id
 * @property int $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Product $product
 * @property Purchase $purchase
 * @property Collection|Entrance[] $entrances
 *
 * @package App\Models
 */
class PurchaseLine extends Model
{
	protected $table = 'purchase_lines';

	protected $casts = [
		'price' => 'float',
		'quantity' => 'float',
		'purchase_id' => 'int',
		'product_id' => 'int'
	];

	protected $fillable = [
		'price',
		'quantity',
		'purchase_id',
		'product_id'
	];

	public function Product()
	{
		return $this->belongsTo(Product::class);
	}

	public function Purchase()
	{
		return $this->belongsTo(Purchase::class);
	}

	public function Entrance()
	{
		return $this->hasOne(Entrance::class);
	}
}
