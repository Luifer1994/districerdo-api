<?php

namespace App\Http\Modules\Purchases\Services;

use App\Http\Modules\Batchs\Models\Batch;
use App\Http\Modules\Batchs\Repositories\BatchRepository;
use App\Http\Modules\Batchs\Services\BatchService;
use App\Http\Modules\Entrances\Models\Entrance;
use App\Http\Modules\Entrances\Repositories\EntranceRepository;
use App\Http\Modules\Inventories\Models\Inventory;
use App\Http\Modules\Inventories\Repositories\InventoryRepository;
use App\Http\Modules\Purchases\Models\PartialPaymentsOfPurchase;
use App\Http\Modules\Purchases\Models\Purchase;
use App\Http\Modules\Purchases\Models\PurchaseLine;
use App\Http\Modules\Purchases\Repositories\PartialPaymentsOfIPurchaseRepository;
use App\Http\Modules\Purchases\Repositories\PurchaseLineRepository;
use App\Http\Modules\Purchases\Repositories\PurchaseRepository;
use App\Http\Modules\Purchases\Requests\CreateOrUpdatePurchaseRequest;
use App\Http\Modules\Purchases\Requests\CreatePaymentPartialPurchaseRequest;
use App\Traits\FileStorage;
use App\Traits\GenerateCodeRandom;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    use GenerateCodeRandom, FileStorage;

    public function __construct(
        protected PurchaseRepository $purchaseRepository,
        protected PurchaseLineRepository $purchaseLineRepository,
        protected EntranceRepository $entranceRepository,
        protected BatchService $batchService,
        protected BatchRepository $batchRepository,
        protected InventoryRepository $inventoryRepository,
        protected PartialPaymentsOfIPurchaseRepository $PartialPaymentsOfIPurchaseRepository
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
            return ['res' => true, 'message' => 'Compra creada correctamente',"data"=>$newPurchase];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['res' => false, 'message' => 'Error al crear la compra',"data"=>null];
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
        DB::beginTransaction();
        try {
            $purchase = $this->purchaseRepository->findById($id);
            if (!$purchase) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'compra no encontrada',
                    'data' => null
                ];
            }

            if ($purchase->status == 'PAGADA') {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'La compra ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($purchase->status == 'Cancelada') {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'La compra se encuentra cancelada',
                    'data' => null
                ];
            }
            $this->PartialPaymentsOfIPurchaseRepository->save(new PartialPaymentsOfPurchase([
                'purchase_id' => $purchase->id,
                'amount' => $purchase->total_for_pay,
                'user_id' => auth()->user()->id,
                'description' => 'Pago total de la compra'
            ]));
            $purchase->status = 'PAID';
            $purchase = $this->purchaseRepository->save($purchase);



            DB::commit();

            return [
                'status' => true,
                'message' => 'compra pagada con éxito',
                'data' => $purchase
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Error al pagar la compra '. $th->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Partial payment of purchase.
     *
     * @param CreatePaymentPartialPurchaseRequest $request
     * @return object
     */
    public function partialPayment(CreatePaymentPartialPurchaseRequest $request): object
    {
        DB::beginTransaction();
        try {
            $purchase = $this->purchaseRepository->findById($request->purchase_id);
            if (!$purchase) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'Compra no encontrada',
                    'data' => null
                ];
            }

            if ($purchase->status == 'PAGADA') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La compra ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($purchase->status == 'Cancelada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La compra se encuentra cancelada',
                    'data' => null
                ];
            }

            if ($request->amount > $purchase->total_for_pay) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'El monto a pagar es mayor al monto pendiente de la compra ($' . number_format($purchase->total_for_pay, 0, '.', '.') . ')',
                    'data' => null
                ];
            }


            $partial = $this->PartialPaymentsOfIPurchaseRepository->save(new PartialPaymentsOfPurchase([
                'purchase_id' => $purchase->id,
                'amount' => $request->amount,
                'evidence' => ($request->has('evidence') && $request->file('evidence') != null) ? $this->uploadFile($request->file('evidence')) : null,
                'user_id' => auth()->user()->id,
                'description' => $request->description
            ]));

            if ($purchase->total_for_pay == $request->amount) {
                $purchase->status = 'PAID';
                $purchase = $this->purchaseRepository->save($purchase);
            }

            DB::commit();

            return (object) [
                'status' => true,
                'message' => 'Pago parcial de compra realizado con éxito',
                'data' => $partial
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return (object) [
                'status' => false,
                'message' => 'Error al realizar el pago parcial de la compra ',
                'data' => null
            ];
        }
    }

    /**
     * Download evidence.
     *
     * @param int $id
     * @return array
     */
    public function downloadEvidence(int $id): array
    {
        try {
            $data = $this->PartialPaymentsOfIPurchaseRepository->find($id);

            if (!$data)
                return [
                    'status' => false,
                    'message' => 'Pago parcial de compra no encontrado',
                    'data' => null
                ];
            if (!$data->evidence)
                return [
                    'status' => false,
                    'message' => 'No se encontró evidencia de pago',
                    'data' => null
                ];
            //sear file in storage
            $file = $this->getFile($data->evidence);

            return [
                'status' => true,
                'message' => 'Compra descargada con éxito',
                'data' => ['base64' => base64_encode($file), 'code' => $data->id]

            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => 'Error al descargar la compra',
                'data' => null
            ];
        }
    }
}
