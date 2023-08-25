<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property string $code
 * @property string $state
 * @property string|null $observation
 * @property int $client_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 * @property User $user
 * @property Collection|InvoiceLine[] $invoice_lines
 *
 * @package App\Models
 */
class Invoice extends Model
{
	protected $table = 'invoices';

	protected $casts = [
		'client_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'code',
		'state',
		'observation',
		'client_id',
		'user_id'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function invoice_lines()
	{
		return $this->hasMany(InvoiceLine::class);
	}
}
