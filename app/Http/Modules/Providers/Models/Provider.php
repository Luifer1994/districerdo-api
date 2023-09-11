<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Providers\Models;

use App\Http\Modules\Cities\Models\City;
use App\Http\Modules\DocumentTypes\Models\DocumentType;
use App\Models\Purchase;
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

	public function City()
	{
		return $this->belongsTo(City::class);
	}

	public function DocumentType()
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function Purchases()
	{
		return $this->hasMany(Purchase::class);
	}
}
