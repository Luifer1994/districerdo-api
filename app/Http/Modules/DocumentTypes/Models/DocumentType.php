<?php

namespace App\Http\Modules\DocumentTypes\Models;

use App\Http\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property Collection|User[] $users
 */
class DocumentType extends Model
{
    use HasFactory;

    protected $table = 'document_types';

    protected $dates = [
        'changed_at'
    ];

    protected $fillable = [
        'code',
        'name',
        'changed_at'
    ];

    /**
     * Relationship with users.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
