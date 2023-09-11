<?php

namespace App\Http\Modules\Purchases\Services;

use App\Http\Modules\Batchs\Models\Batch;
use App\Http\Modules\Batchs\Repositories\BatchRepository;
use App\Http\Modules\Batchs\Services\BatchService;
use App\Http\Modules\Entrances\Models\Entrance;
use App\Http\Modules\Entrances\Repositories\EntranceRepository;
use App\Http\Modules\Inventories\Models\Inventory;
use App\Http\Modules\Inventories\Repositories\InventoryRepository;
use App\Http\Modules\Purchases\Models\Purchase;
use App\Http\Modules\Purchases\Models\PurchaseLine;
use App\Http\Modules\Purchases\Repositories\PurchaseLineRepository;
use App\Http\Modules\Purchases\Repositories\PurchaseRepository;
use App\Http\Modules\Purchases\Requests\CreateOrUpdatePurchaseRequest;
use App\Traits\GenerateCodeRandom;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    use GenerateCodeRandom;

    public function __construct(
        protected PurchaseRepository $purchaseRepository,
        protected PurchaseLineRepository $purchaseLineRepository,
        protected EntranceRepository $entranceRepository,
        protected BatchService $batchService,
        protected BatchRepository $batchRepository,
        protected InventoryRepository $inventoryRepository
    ) {
    }

    /**
     * Create new Purchase.
     *
     * @param  array $data
     * @return array
     */
    public function createPurchase(CreateOrUpdatePurchaseRequest $request): array
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'user_id' => auth()->user()->id,
                'code' => $this->generateCodeUnique(4)
            ]);
            $newPurchase = $this->purchaseRepository->save(new Purchase($request->all()));

            $newBatch = $this->batchRepository->save(new Batch([
                'code' => $this->batchService->generateCodeUnique(4)
            ]));

            foreach ($request->purchase_lines as $purchase) {

                $newPurchaseLine = $this->purchaseLineRepository->save(new PurchaseLine([
                    'purchase_id' => $newPurchase->id,
                    'product_id' => $purchase['product_id'],
                    'quantity' => $purchase['quantity'],
                    'price' => $purchase['price']
                ]));

                $this->entranceRepository->save(new Entrance([
                    'purchase_line_id' => $newPurchaseLine->id,
                    'quantity' => $purchase['quantity'],
                    'price' => $purchase['price'],
                    'batch_id' => $newBatch->id,
                    'product_id' => $purchase['product_id'],
                    'user_id' => auth()->user()->id
                ]));

                $this->inventoryRepository->save(new Inventory([
                    'quantity' => $purchase['quantity'],
                    'batch_id' => $newBatch->id,
                    'product_id' => $purchase['product_id'],
                    'user_id' => auth()->user()->id
                ]));
            }

            DB::commit();
            return ['res' => true, 'message' => 'Compra creada correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['res' => false, 'message' => 'Error al crear la compra'];
        }
    }

    /**
     * Generate code unique.
     *
     * @param  int $length
     * @return string
     */
    public function generateCodeUnique($length): string
    {
        $code = $this->generateCode($length);

        if ($this->purchaseRepository->findByCode($code)) {
            return $this->generateCodeUnique($length); // Return the result of the recursive call
        } else {
            return $code;
        }
    }

    /**
     * paid Purchase.
     *
     * @param  int $id
     * @return array
     */
    public function paidPurchase(int $id): array
    {
        try {
            $purchase = $this->purchaseRepository->find($id);
            if (!$purchase) {
                return ['res' => false, 'message' => 'Compra no encontrada'];
            }
            $purchase->status = 'paid';
            $this->purchaseRepository->save($purchase);
            return ['res' => true, 'message' => 'Compra pagada correctamente'];
        } catch (\Throwable $th) {
            return ['res' => false, 'message' => 'Error al pagar la compra'];
        }
    }
}
