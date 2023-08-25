<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property int $document_type_id
 * @property string $document
 * @property string $email
 * @property bool $is_active
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $changed_at
 * 
 * @property DocumentType $document_type
 * @property Collection|Category[] $categories
 * @property Collection|Entrance[] $entrances
 * @property Collection|Invoice[] $invoices
 * @property Collection|Output[] $outputs
 * @property Collection|Product[] $products
 * @property Collection|Purchase[] $purchases
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'document_type_id' => 'int',
		'is_active' => 'bool',
		'email_verified_at' => 'datetime',
		'changed_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'document_type_id',
		'document',
		'email',
		'is_active',
		'email_verified_at',
		'password',
		'remember_token',
		'changed_at'
	];

	public function document_type()
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function categories()
	{
		return $this->hasMany(Category::class);
	}

	public function entrances()
	{
		return $this->hasMany(Entrance::class);
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function outputs()
	{
		return $this->hasMany(Output::class);
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}

	public function purchases()
	{
		return $this->hasMany(Purchase::class);
	}
}
