<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $type;
    
    public function __construct($type = null)
    {
        $this->type = $type;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Transaction::with(['customer', 'user', 'items.product']);
        
        if ($this->type === 'in') {
            $query->stockIn();
        } elseif ($this->type === 'out') {
            $query->stockOut();
        }
        
        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Transaction Code',
            'Type',
            'Customer',
            'Created By',
            'Total Value',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->transaction_code,
            $row->transaction_type === 'in' ? 'Stock In' : 'Stock Out',
            $row->customer ? $row->customer->name : 'N/A',
            $row->user->name,
            $row->total_value,
            $row->notes,
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
