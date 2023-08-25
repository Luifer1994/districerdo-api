<?php

namespace App\Http\Modules\Invoices\Models;

use App\Http\Modules\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

/**
 * Class InvoiceLine
 *
 * @property int $id
 * @property float $price
 * @property float $percentage_tax
 * @property int $quantity
 * @property int $invoice_id
 * @property int $service_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Invoice $Invoice
 * @property Service $Service
 * @property Collection|InvoiceLineSupply[] $InvoiceLineSupply
 *
 * @package App\Models
 */
class InvoiceLine extends Model implements AuditableContract
{
    use Auditable, HasFactory;

    protected $table = 'invoice_lines';

	protected $casts = [
		'price' => 'float',
		'quantity' => 'float',
		'invoice_id' => 'int',
		'product_id' => 'int',
		'batch_id' => 'int'
	];

	protected $fillable = [
		'price',
		'quantity',
		'invoice_id',
		'product_id',
		'batch_id'
	];

    /**
     * Relation to Invoice.
     *
     * @return BelongsTo
     */
	public function Invoice():BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

    /**
     * Relation to Service.
     *
     * @return BelongsTo
     */
	public function Product()
	{
		return $this->belongsTo(Product::class);
	}

    public function Outputs()
	{
		return $this->belongsToMany(Output::class, 'output_invoice_lines')
					->withPivot('id')
					->withTimestamps();
	}
}
