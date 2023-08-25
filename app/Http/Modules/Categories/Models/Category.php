<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Categories\Models;

use App\Http\Modules\Products\Models\Product;
use App\Http\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Category extends Model
{
	protected $table = 'categories';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'user_id'
	];

	public function User()
	{
		return $this->belongsTo(User::class);
	}

	public function Products()
	{
		return $this->hasMany(Product::class);
	}
}
