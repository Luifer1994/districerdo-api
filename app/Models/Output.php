<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Output
 * 
 * @property int $id
 * @property float $price
 * @property float $quantity
 * @property int $batch_id
 * @property int $product_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Batch $batch
 * @property Product $product
 * @property User $user
 * @property Collection|InvoiceLine[] $invoice_lines
 *
 * @package App\Models
 */
class Output extends Model
{
	protected $table = 'outputs';

	protected $casts = [
		'price' => 'float',
		'quantity' => 'float',
		'batch_id' => 'int',
		'product_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'price',
		'quantity',
		'batch_id',
		'product_id',
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

	public function invoice_lines()
	{
		return $this->belongsToMany(InvoiceLine::class, 'output_invoice_lines')
					->withPivot('id')
					->withTimestamps();
	}
}
