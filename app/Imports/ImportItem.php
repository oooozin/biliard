<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportItem implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Item([
            'id' => $row['id'],
            'name' => $row['name'],
            'image' => null,
            'phone' => $row['phone'],
            'email' => $row['email'],
            'address' => $row['address'],
            'shop_id' => $row['shop_id'],
            'status' => $row['status'],
            'is_warehouse' => false,
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
    }
}