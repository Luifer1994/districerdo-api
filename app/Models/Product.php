<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $sku
 * @property int $category_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Category $category
 * @property User $user
 * @property Collection|Entrance[] $entrances
 * @property Collection|InvoiceLine[] $invoice_lines
 * @property Collection|Output[] $outputs
 * @property Collection|PurchaseLine[] $purchase_lines
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'category_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'sku',
		'category_id',
		'user_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function entrances()
	{
		return $this->hasMany(Entrance::class);
	}

	public function invoice_lines()
	{
		return $this->hasMany(InvoiceLine::class);
	}

	public function outputs()
	{
		return $this->hasMany(Output::class);
	}

	public function purchase_lines()
	{
		return $this->hasMany(PurchaseLine::class);
	}
}
