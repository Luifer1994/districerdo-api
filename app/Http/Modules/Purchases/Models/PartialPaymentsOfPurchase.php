<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Modules\Purchases\Models;

use App\Http\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PartialPaymentsOfPurchase
 *
 * @property int $id
 * @property int $purchase_id
 * @property float $amount
 * @property string|null $evidence
 * @property string|null $description
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Purchase $purchase
 * @property User $user
 *
 * @package App\Models
 */
class PartialPaymentsOfPurchase extends Model
{
    protected $table = 'partial_payments_of_purchases';

    protected $casts = [
        'purchase_id' => 'int',
        'amount' => 'float',
        'user_id' => 'int'
    ];

    protected $fillable = [
        'purchase_id',
        'amount',
        'evidence',
        'description',
        'user_id'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
