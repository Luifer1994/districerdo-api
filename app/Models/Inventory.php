<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 * 
 * @property int $id
 * @property float $quantity
 * @property int $product_id
 * @property int $batch_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Batch $batch
 * @property Product $product
 * @property User $user
 *
 * @package App\Models
 */
class Inventory extends Model
{
	protected $table = 'inventories';

	protected $casts = [
		'quantity' => 'float',
		'product_id' => 'int',
		'batch_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'quantity',
		'product_id',
		'batch_id',
		'user_id'
	];

	public function batch()
	{
		return $this->belongsTo(Batch::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
