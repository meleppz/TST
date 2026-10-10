<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\RESTful\ResourceController;


class ProductAPI extends ResourceController
{
    protected $modelName = ProductModel::class;
    protected $format    = 'json';

    // GET /products?page=1&limit=20&q=nama&brand=Washington&category_id=30
    public function index()
    {
        $limit = min(max((int) ($this->request->getGet('limit') ?? 20), 1), 100);
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);

        $result = $this->model->search([
            'q'           => $this->request->getGet('q'),
            'brand'       => $this->request->getGet('brand'),
            'category_id' => (int) $this->request->getGet('category_id'),
        ], $limit, ($page - 1) * $limit);

        return $this->respond([
            'data' => $result['data'],
            'meta' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $result['total'],
            ],
        ]);
    }

    // GET /products/{id}
    public function show($id = null)
    {
        $product = $this->model->findWithCategory((int) $id);

        if ($product === null) {
            return $this->failNotFound("Product {$id} tidak ditemukan");
        }

        return $this->respond(['data' => $product]);
    }

    // GET /categories?family=Food
    public function categories()
    {
        return $this->respond(['data' => $this->model->categories($this->request->getGet('family'))]);
    }
}
