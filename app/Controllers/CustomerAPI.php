<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\RESTful\ResourceController;

/**
 * Customer Service (sub-domain: Customer Management).
 *
 * @property CustomerModel $model
 */
class CustomerAPI extends ResourceController
{
    protected $modelName = CustomerModel::class;
    protected $format    = 'json';

    public function index()
    {
        $limit  = min(max((int) ($this->request->getGet('limit') ?? 20), 1), 100);
        $page   = max((int) ($this->request->getGet('page') ?? 1), 1);
        $result = $this->model->search($this->request->getGet('q'), $limit, ($page - 1) * $limit);

        return $this->respond([
            'data' => $result['data'],
            'meta' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $result['total'],
            ],
        ]);
    }

    // GET /customers/{id}
    public function show($id = null)
    {
        $customer = $this->model->find($id);

        if ($customer === null) {
            return $this->failNotFound("Customer {$id} tidak ditemukan");
        }

        return $this->respond(['data' => $customer]);
    }

    // GET /customers/{id}/summary
    public function summary($id = null)
    {
        $customer = $this->model->find($id);

        if ($customer === null) {
            return $this->failNotFound("Customer {$id} tidak ditemukan");
        }

        $perYear = $this->model->purchasingSummary((int) $id);

        return $this->respond([
            'data' => [
                'customer_id'  => (int) $customer['customer_id'],
                'fullname'     => $customer['fullname'],
                'member_card'  => $customer['member_card'],
                'per_year'     => $perYear,
                'total_sales'  => round(array_sum(array_column($perYear, 'total_sales')), 2),
                'total_units'  => (float) array_sum(array_column($perYear, 'total_units')),
            ],
        ]);
    }
}
