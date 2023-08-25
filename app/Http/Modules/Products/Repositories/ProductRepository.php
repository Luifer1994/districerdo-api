<?php

namespace App\Http\Modules\Products\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Products\Models\Product;

class ProductRepository extends RepositoryBase
{
    protected  $modelProduct;

    public function __construct(Product $modelProduct)
    {
        parent::__construct($modelProduct);
        $this->modelProduct = $modelProduct;
    }


    /**
     * Get all Products.
     *
     * @param  int $limit
     * @param  string $search
     * @return object
     */
    public function getAllProducts(int $limit, string $search): object
    {
        return $this->modelProduct
            ->select('id', 'name', 'description', 'sku', 'category_id')
            ->with(['category:id,name'])
            ->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('sku', 'like', "%$search%")
            ->orderBy('id', 'desc')
            ->paginate($limit);
    }

    /**
     * Get all Products is active.
     *
     * @param  string $search
     * @return object
     */
    public function searchProducts(string $search): object
    {
        return $this->modelProduct
            ->select('id', 'name', 'description', 'sku', 'category_id')
            ->with(['category:id,name'])
            ->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('sku', 'like', "%$search%")
            ->orderBy('name', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Find a Product by id.
     *
     * @param  int $id
     * @return ?Product
     */
    public function findById(int $id): ?Product
    {
        return $this->modelProduct
            ->select('id', 'name', 'description', 'sku','category_id')
            ->with(['category:id,name'])
            ->where(['id' => $id])
            ->first();
    }

   /**
     * Find a Product by id.
     *
     * @param  string $id
     * @return ?Product
     */
    public function findBySku(string $id): ?Product
    {
        return $this->modelProduct
            ->select('id', 'name', 'description', 'sku')
            ->where(['sku' => $id])
            ->first();
    }
}
