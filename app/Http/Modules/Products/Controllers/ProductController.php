<?php

namespace App\Http\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Inventories\Repositories\InventoryRepository;
use App\Http\Modules\Products\Models\Product;
use App\Http\Modules\Products\Repositories\ProductRepository;
use App\Http\Modules\Products\Requests\CreateOrUpdateProductRequest;
use App\Http\Modules\Products\Requests\ValidateStockRequest;
use App\Http\Modules\Products\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected $ProductRepository,$productService,$InventoryRepository;

    public function __construct(ProductRepository $ProductRepository, ProductService $productService, InventoryRepository $InventoryRepository)
    {
        $this->ProductRepository = $ProductRepository;
        $this->productService    = $productService;
        $this->InventoryRepository = $InventoryRepository;
    }

    /**
     * Get all Products.
     *
     * @param  PaginateBaseRequest $request
     * @return JsonResponse
     */
    public function index(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $limit    = $request->limit ?? 10;
            $search   = $request->search ?? '';
            $Products = $this->ProductRepository->getAllProducts($limit, $search);
            return $this->successResponse($Products, 'Ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all Products is active.
     *
     * @param  PaginateBaseRequest $request
     * @return JsonResponse
     */
    public function searchProducts(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $search   = $request->search ?? '';
            $Products = $this->ProductRepository->searchProducts($search);
            return $this->successResponse($Products, 'Ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Product by id.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $Product = $this->ProductRepository->findById($id);
            return $this->successResponse($Product, 'Ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new Product.
     *
     * @param  CreateOrUpdateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrUpdateProductRequest $request): JsonResponse
    {
        try {
            return $this->successResponse($this->productService->createProduct($request), 'Producto registrado con éxito', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Product by id.
     *
     * @param  CreateOrUpdateProductRequest $request
     * @param  int $id
     * @return JsonResponse
     */
    public function update(CreateOrUpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $Product = $this->ProductRepository->findById($id);
            if (!$Product)
                return $this->errorResponse('¡El producto no existe!', Response::HTTP_NOT_FOUND);

            $Product->fill($request->all());
            $Product = $this->ProductRepository->save($Product);
            return $this->successResponse($Product, 'Producto actualizado con éxito', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate stock.
     *
     * @param  CreateOrUpdateProductRequest $request
     * @return JsonResponse
     */
    public function validateStock(ValidateStockRequest $request): JsonResponse
    {
        try {
            $inventory = $this->InventoryRepository->findInventoryByProductIdAndBatchCode($request->product_id, $request->batch);
            if (!$inventory)
                return $this->errorResponse('El producto no tiene existencia en el lote seleccionado', Response::HTTP_NOT_FOUND);

            if ($inventory->quantity < $request->quantity)
                return $this->errorResponse('La cantidad '.$request->quantity .' a facturar del producto es mayor a la cantidad en existencia del lote (Cantidad en existencia: ' . $inventory->quantity . ')', Response::HTTP_BAD_REQUEST);

            return $this->successResponse($inventory, 'Ok', Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
