<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Purchase
 * 
 * @property int $id
 * @property string $code
 * @property int $provider_id
 * @property int $user_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Provider $provider
 * @property User $user
 * @property Collection|PurchaseLine[] $purchase_lines
 *
 * @package App\Models
 */
class Purchase extends Model
{
	protected $table = 'purchases';

	protected $casts = [
		'provider_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'code',
		'provider_id',
		'user_id',
		'status'
	];

	public function provider()
	{
		return $this->belongsTo(Provider::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function purchase_lines()
	{
		return $this->hasMany(PurchaseLine::class);
	}
}
