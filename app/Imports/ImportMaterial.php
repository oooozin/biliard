<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportMaterial implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Material([
            'id' => $row['id'],
            'name' => $row['name'],
            'status' => $row['status'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
    }
}