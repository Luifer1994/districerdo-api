<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Batch
 * 
 * @property int $id
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Entrance[] $entrances
 * @property Collection|InvoiceLine[] $invoice_lines
 * @property Collection|Output[] $outputs
 *
 * @package App\Models
 */
class Batch extends Model
{
	protected $table = 'batches';

	protected $fillable = [
		'code'
	];

	public function entrances()
	{
		return $this->hasMany(Entrance::class);
	}

	public function invoice_lines()
	{
		return $this->hasMany(InvoiceLine::class);
	}

	public function outputs()
	{
		return $this->hasMany(Output::class);
	}
}
