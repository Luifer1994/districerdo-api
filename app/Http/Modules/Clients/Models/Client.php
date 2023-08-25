<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Clients\Models;

use App\Http\Modules\Cities\Models\City;
use App\Http\Modules\DocumentTypes\Models\DocumentType;
use App\Http\Modules\Invoices\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
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
 * @property Collection|Invoice[] $invoices
 *
 * @package App\Models
 */
class Client extends Model
{
	protected $table = 'clients';

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

	public function Invoices()
	{
		return $this->hasMany(Invoice::class);
	}
}
