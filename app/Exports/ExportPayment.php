<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportPayment implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Payment::select('id', 'name', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($post): array
    {
        $createdByPayment = User::find($post->created_by);
        $updatedByPayment = User::find($post->updated_by);


        return [
            $post->id,
            $post->name,
            $createdByPayment ? $createdByPayment->name : 'Unknown',
            $updatedByPayment ? $updatedByPayment->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}