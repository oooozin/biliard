<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportPaymentParams implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Payment::searchQuery()
            ->sortingQuery()
            ->filterQuery()
            ->filterDateQuery();
        if (!empty($this->filters)) {
        }

        return $query->select('id', 'name', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
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