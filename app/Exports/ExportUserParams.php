<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportUserParams implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::searchQuery()
            ->sortingQuery()
            ->filterQuery()
            ->filterDateQuery();
        if (!empty($this->filters)) {
        }

        return $query->select('id', 'name', 'phone', 'email', 'address', 'shop_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
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

        return [
            $post->id,
            $post->name,
            $post->phone,
            $post->email,
            $post->address,
            $post->shop_id,
            $post->status,
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}