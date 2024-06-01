<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportCategory implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Category([
            'id' => $row['id'],
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'status' => $row['status'],
            'shop_id' => $row['shop_id'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
    }
}