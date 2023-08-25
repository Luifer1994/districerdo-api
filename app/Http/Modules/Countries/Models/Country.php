<?php

namespace App\Http\Modules\Countries\Models;

use App\Http\Modules\Departments\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Department[] $departments
 */
class Country extends Model
{
	protected $table = 'countries';

	protected $fillable = [
		'name',
        'iso_code',
        'iso_code3'
	];

    /**
     * Relationship with departments
     *
     * @return HasMany
     */
	public function departments(): HasMany
	{
		return $this->hasMany(Department::class);
	}
}

