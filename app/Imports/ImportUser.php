<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportUser implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'id' => $row['id'],
            'name' => $row['name'],
            'image' => null,
            'phone' => $row['phone'],
            'email' => $row['email'],
            'address' => $row['address'],
            'shop_id' => 1,
            'status' => $row['status'],
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
    }
}