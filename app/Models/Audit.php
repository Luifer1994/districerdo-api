<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Audit
 * 
 * @property int $id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property int $auditable_id
 * @property string|null $old_values
 * @property string|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Audit extends Model
{
	protected $table = 'audits';

	protected $casts = [
		'user_id' => 'int',
		'auditable_id' => 'int'
	];

	protected $fillable = [
		'user_type',
		'user_id',
		'event',
		'auditable_type',
		'auditable_id',
		'old_values',
		'new_values',
		'url',
		'ip_address',
		'user_agent',
		'tags'
	];
}
