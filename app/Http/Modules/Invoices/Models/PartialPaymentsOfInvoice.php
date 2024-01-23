<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Invoices\Models;

use App\Http\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PartialPaymentsOfInvoice
 *
 * @property int $id
 * @property int $invoice_id
 * @property float $amount
 * @property string|null $evidence
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Invoice $invoice
 *
 * @package App\Models
 */
class PartialPaymentsOfInvoice extends Model
{
    protected $table = 'partial_payments_of_invoices';

    protected $casts = [
        'invoice_id' => 'int',
        'amount' => 'float'
    ];

    protected $fillable = [
        'invoice_id',
        'amount',
        'evidence',
        'description',
        'user_id'
    ];

    public function Invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
