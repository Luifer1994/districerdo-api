<?php

namespace App\Http\Modules\Cities\Models;

use App\Http\Modules\Departments\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class City
 *
 * @property int $id
 * @property string $name
 * @property int $department_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Department $department
 */
class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    protected $casts = [
        'department_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'code',
        'department_id'
    ];

    /**
     * Relationship with department
     *
     * @return BelongsTo
     */
    public function Department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
