<?php

namespace App\Exports;

use App\Models\Shop;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportShop implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Shop::select('id', 'name', 'open_time', 'close_time', 'address', 'phone', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Open Time',
            'Close Time',
            'Address',
            'Phone',
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

        $openTime = $post->open_time ? date('h:i A', strtotime($post->open_time)) : '';
        $closeTime = $post->close_time ? date('h:i A', strtotime($post->close_time)) : '';

        return [
            $post->id,
            $post->name,
            $openTime,
            $closeTime,
            $post->address,
            $post->phone,
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}