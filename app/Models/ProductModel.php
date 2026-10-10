<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'product';
    protected $primaryKey       = 'product_id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = false;

    protected $allowedFields = [];

    private const SELECT = 'product.*, product_class.product_subcategory, product_class.product_category,'
        . ' product_class.product_department, product_class.product_family';

    public function search(array $filters, int $limit, int $offset): array
    {
        $builder = $this->builder()
            ->select(self::SELECT)
            ->join('product_class', 'product_class.product_class_id = product.product_class_id', 'left');

        if (! empty($filters['q'])) {
            $builder->like('product.product_name', $filters['q']);
        }
        if (! empty($filters['brand'])) {
            $builder->where('product.brand_name', $filters['brand']);
        }
        if (! empty($filters['category_id'])) {
            $builder->where('product.product_class_id', $filters['category_id']);
        }

        $total = (clone $builder)->countAllResults(false);
        $data  = $builder->orderBy('product.product_id')->limit($limit, $offset)->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    public function findWithCategory(int $id): ?array
    {
        return $this->builder()
            ->select(self::SELECT)
            ->join('product_class', 'product_class.product_class_id = product.product_class_id', 'left')
            ->where('product.product_id', $id)
            ->get()->getRowArray();
    }

    public function categories(?string $family = null): array
    {
        $builder = $this->db->table('product_class');

        if ($family !== null && $family !== '') {
            $builder->where('product_family', $family);
        }

        return $builder->orderBy('product_class_id')->get()->getResultArray();
    }
}
