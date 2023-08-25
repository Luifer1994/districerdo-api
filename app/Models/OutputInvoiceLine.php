<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OutputInvoiceLine
 * 
 * @property int $id
 * @property int $output_id
 * @property int $invoice_line_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property InvoiceLine $invoice_line
 * @property Output $output
 *
 * @package App\Models
 */
class OutputInvoiceLine extends Model
{
	protected $table = 'output_invoice_lines';

	protected $casts = [
		'output_id' => 'int',
		'invoice_line_id' => 'int'
	];

	protected $fillable = [
		'output_id',
		'invoice_line_id'
	];

	public function invoice_line()
	{
		return $this->belongsTo(InvoiceLine::class);
	}

	public function output()
	{
		return $this->belongsTo(Output::class);
	}
}
