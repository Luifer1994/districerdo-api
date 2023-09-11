<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Batchs\Models;

use App\Http\Modules\Invoices\Models\InvoiceLine;
use App\Models\Output;
use Carbon\Carbon;
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

	public function Entrances()
	{
		return $this->hasMany(Entrance::class);
	}

	public function InvoiceLines()
	{
		return $this->hasMany(InvoiceLine::class);
	}

	public function Outputs()
	{
		return $this->hasMany(Output::class);
	}
}
