<?php

namespace App\Exports;

use App\Models\TransactionItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactionId;

    public function __construct($transactionId = null)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = TransactionItem::with(['transaction', 'product']);

        if ($this->transactionId) {
            $query->where('transaction_id', $this->transactionId);
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
            'Nomor Transaksi',
            'Produk',
            'Qty',
            'Harga Satuan',
            'Subtotal',
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
            $row->transaction->transaction_code,
            $row->product->name,
            $row->quantity,
            $row->unit_price,
            $row->subtotal,
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
