<?php

namespace App\Http\Modules\Invoices\Models;

use App\Http\Modules\Clients\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

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
 * @property Client $Client
 * @property Collection|InvoiceLine[] $InvoiceLines
 */
class Invoice extends Model implements AuditableContract
{
    use Auditable, HasFactory;

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


    /**
     * Get state attribute.
     *
     * @param string $value
     * @return string
     */
    public function getStateAttribute(string $value)
    {
            switch ($value) {
                case 'PAID':
                    return 'Pagada';
                    break;
                case 'CANCELLED':
                    return 'Cancelada';
                    break;
                case 'PENDING':
                    return 'Pendiente';
                    break;
                default:
                    break;
            }
    }

    //formatea la fecha de creacion de la factura a español
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Relation with Cliet.
     *
     * @return BelongsTo
     */
    public function Client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation with Cliet.
     *
     * @return HasMany
     */
    public function InvoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Relation to user.
     *
     * User creator the invoice.
     *
     * @return BelongsTo
     */
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
