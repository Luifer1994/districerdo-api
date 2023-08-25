<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Country $country
 * @property Collection|City[] $cities
 *
 * @package App\Models
 */
class Department extends Model
{
	protected $table = 'departments';

	protected $casts = [
		'country_id' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'country_id'
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function cities()
	{
		return $this->hasMany(City::class);
	}
}
