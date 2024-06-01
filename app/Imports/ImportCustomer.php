<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportCustomer implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Customer([
            'id' => $row['id'],
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
    }
}