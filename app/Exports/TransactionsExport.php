<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $type;
    protected $filters;

    /**
     * @param string|null $type Transaction type ('in' or 'out')
     * @param array $filters Additional filters to apply (customer_id, date_from, date_to)
     */
    public function __construct($type = null, array $filters = [])
    {
        $this->type = $type;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Transaction::with(['customer', 'user', 'items.product']);

        // Apply transaction type filter - this is mandatory
        if ($this->type === 'in') {
            $query->stockIn();
        } elseif ($this->type === 'out') {
            $query->stockOut();
        }

        // Apply additional filters if provided
        if (!empty($this->filters)) {
            // Filter by customer
            if (isset($this->filters['customer_id']) && $this->filters['customer_id']) {
                $query->where('customer_id', $this->filters['customer_id']);
            }

            // Filter by date range
            if (isset($this->filters['date_from']) && $this->filters['date_from']) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            }

            if (isset($this->filters['date_to']) && $this->filters['date_to']) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            }
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $columns = $this->getColumns();
        return array_values($columns);
    }
    
    /**
     * Get the column definitions based on transaction type
     * 
     * @return array
     */
    private function getColumns(): array
    {
        $columns = [
            'row_num' => '#',
            'transaction_code' => 'Nomor Transaksi',
            'transaction_type' => 'Jenis Transaksi',
            'user' => 'Dibuat Oleh',
            'total_value' => 'Total Nominal',
            'notes' => 'Catatan',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
        ];
        
        // Only include customer column for 'out' transactions
        if ($this->type !== 'in') {
            $columns = array_merge(
                array_slice($columns, 0, 3), 
                ['customer' => 'Pelanggan'], 
                array_slice($columns, 3)
            );
        }
        
        return $columns;
    }

    protected $rowNumber = 0;

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Increment row number for each row
        $this->rowNumber++;
        
        $columns = array_keys($this->getColumns());
        $data = [];
        
        foreach ($columns as $column) {
            switch ($column) {
                case 'row_num':
                    $data[] = $this->rowNumber;
                    break;
                case 'transaction_code':
                    $data[] = $row->transaction_code;
                    break;
                case 'transaction_type':
                    $data[] = $row->transaction_type === 'in' ? 'Stock In' : 'Stock Out';
                    break;
                case 'customer':
                    $data[] = $row->customer ? $row->customer->name : 'N/A';
                    break;
                case 'user':
                    $data[] = $row->user->name;
                    break;
                case 'total_value':
                    $data[] = $row->total_value;
                    break;
                case 'notes':
                    $data[] = $row->notes;
                    break;
                case 'created_at':
                    $data[] = $row->created_at->format('Y-m-d H:i:s');
                    break;
                case 'updated_at':
                    $data[] = $row->updated_at->format('Y-m-d H:i:s');
                    break;
            }
        }
        
        return $data;
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
