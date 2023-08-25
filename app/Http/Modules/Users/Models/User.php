<?php

namespace App\Http\Modules\Users\Models;

use App\Http\Modules\DocumentTypes\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $gender
 * @property string|null $phone
 * @property string|null $cell_phone
 * @property string|null $address
 * @property int $document_type_id
 * @property string|null $document
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
 */
class User extends Authenticatable implements JWTSubject, AuditableContract
{
    use HasFactory, Notifiable, Auditable, HasRoles;

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
        'last_name',
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
