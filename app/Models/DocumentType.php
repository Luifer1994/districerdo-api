<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentType
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $changed_at
 * 
 * @property Collection|Client[] $clients
 * @property Collection|Provider[] $providers
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class DocumentType extends Model
{
	protected $table = 'document_types';

	protected $casts = [
		'changed_at' => 'datetime'
	];

	protected $fillable = [
		'code',
		'name',
		'changed_at'
	];

	public function clients()
	{
		return $this->hasMany(Client::class);
	}

	public function providers()
	{
		return $this->hasMany(Provider::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
