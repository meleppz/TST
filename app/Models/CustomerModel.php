<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table         = 'customer';
    protected $primaryKey    = 'customer_id';
    protected $returnType    = 'array';
    protected $useAutoIncrement = false;

    protected $allowedFields = [];

    public function search(?string $keyword, int $limit, int $offset): array
    {
        $builder = $this->builder();

        if ($keyword !== null && $keyword !== '') {
            $builder->like('fullname', $keyword);
        }

        $total = (clone $builder)->countAllResults();
        $data  = $builder->orderBy('customer_id')->limit($limit, $offset)->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    public function purchasingSummary(int $customerId): array
    {
        $sql = <<<'SQL'
            SELECT the_year,
                   COUNT(*)         AS total_transactions,
                   SUM(unit_sales)  AS total_units,
                   SUM(store_sales) AS total_sales,
                   SUM(store_cost)  AS total_cost
            FROM (
                SELECT 1997 AS the_year, unit_sales, store_sales, store_cost
                FROM sales_fact_1997 WHERE customer_id = ?
                UNION ALL
                SELECT 1998 AS the_year, unit_sales, store_sales, store_cost
                FROM sales_fact_1998 WHERE customer_id = ?
            ) AS s
            GROUP BY the_year
            ORDER BY the_year
            SQL;

        return $this->db->query($sql, [$customerId, $customerId])->getResultArray();
    }
}
