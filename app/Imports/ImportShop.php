<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Shop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportShop implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Shop([
            'id' => $row['id'],
            'name' => $row['name'],
            'open_time' => $row['open_time'],
            'close_time' => $row['close_time'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'is_warehouse' => false,
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
    }
}