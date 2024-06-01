<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportUser implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('id', 'name', 'phone', 'email', 'address', 'shop_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Phone',
            'Email',
            'Address',
            'Shop',
            'Status',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($post): array
    {
        $createdByUser = User::find($post->created_by);
        $updatedByUser = User::find($post->updated_by);

        $shopName = Shop::find($post->shop_id);

        return [
            $post->id,
            $post->name,
            $post->phone,
            $post->email,
            $post->address,
            $shopName ? $shopName->name : 'Unknown',
            $post->status,
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}