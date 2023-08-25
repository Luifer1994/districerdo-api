<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceLine
 * 
 * @property int $id
 * @property float $price
 * @property float $quantity
 * @property int $invoice_id
 * @property int $product_id
 * @property int $batch_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Batch $batch
 * @property Invoice $invoice
 * @property Product $product
 * @property Collection|Output[] $outputs
 *
 * @package App\Models
 */
class InvoiceLine extends Model
{
	protected $table = 'invoice_lines';

	protected $casts = [
		'price' => 'float',
		'quantity' => 'float',
		'invoice_id' => 'int',
		'product_id' => 'int',
		'batch_id' => 'int'
	];

	protected $fillable = [
		'price',
		'quantity',
		'invoice_id',
		'product_id',
		'batch_id'
	];

	public function batch()
	{
		return $this->belongsTo(Batch::class);
	}

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function outputs()
	{
		return $this->belongsToMany(Output::class, 'output_invoice_lines')
					->withPivot('id')
					->withTimestamps();
	}
}
