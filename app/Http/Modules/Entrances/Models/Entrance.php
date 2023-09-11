<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Entrances\Models;

use App\Http\Modules\Products\Models\Product;
use App\Http\Modules\Users\Models\User;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Entrance
 *
 * @property int $id
 * @property float $quantity
 * @property int $batch_id
 * @property int $product_id
 * @property int $user_id
 * @property int $purchase_line_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Batch $batch
 * @property Product $product
 * @property PurchaseLine $purchase_line
 * @property User $user
 *
 * @package App\Models
 */
class Entrance extends Model
{
	protected $table = 'entrances';

	protected $casts = [
		'quantity' => 'float',
		'batch_id' => 'int',
		'product_id' => 'int',
		'user_id' => 'int',
		'purchase_line_id' => 'int'
	];

	protected $fillable = [
		'quantity',
		'batch_id',
		'product_id',
		'user_id',
		'purchase_line_id'
	];

	public function Batch()
	{
		return $this->belongsTo(Batch::class);
	}

	public function Product()
	{
		return $this->belongsTo(Product::class);
	}

	public function PurchaseLine()
	{
		return $this->belongsTo(PurchaseLine::class);
	}

	public function User()
	{
		return $this->belongsTo(User::class);
	}
}
