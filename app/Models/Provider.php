<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Provider
 * 
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $document_number
 * @property string|null $address
 * @property int $document_type_id
 * @property int $city_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property City $city
 * @property DocumentType $document_type
 * @property Collection|Purchase[] $purchases
 *
 * @package App\Models
 */
class Provider extends Model
{
	protected $table = 'providers';

	protected $casts = [
		'document_type_id' => 'int',
		'city_id' => 'int'
	];

	protected $fillable = [
		'name',
		'last_name',
		'email',
		'phone',
		'document_number',
		'address',
		'document_type_id',
		'city_id'
	];

	public function city()
	{
		return $this->belongsTo(City::class);
	}

	public function document_type()
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function purchases()
	{
		return $this->hasMany(Purchase::class);
	}
}
