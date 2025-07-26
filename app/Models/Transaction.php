<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Month mapping for transaction code generation (A-L for Jan-Dec)
     */
    protected static array $monthLetters = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
        7 => 'G',
        8 => 'H',
        9 => 'I',
        10 => 'J',
        11 => 'K',
        12 => 'L'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_type',
        'transaction_code',
        'customer_id',
        'user_id',
        'total_value',
        'notes',
    ];

    /**
     * Define activity log options for the model
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'transaction_type',
                'transaction_code',
                'customer_id',
                'user_id',
                'total_value',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Transaction')
            ->setDescriptionForEvent(fn(string $eventName) => "Transaction {$eventName}");
    }

    /**
     * Get the customer associated with the transaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the user that created the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the transaction items for the transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    /**
     * Scope a query to only include stock in transactions.
     */
    public function scopeStockIn($query)
    {
        return $query->where('transaction_type', 'in');
    }

    /**
     * Scope a query to only include stock out transactions.
     */
    public function scopeStockOut($query)
    {
        return $query->where('transaction_type', 'out');
    }

    /**
     * Generate a unique transaction code in the format TRX-{YY}{M}{DD}-{sequence}
     * 
     * @return string
     */
    public static function generateTransactionCode(?string $type = null): string
    {
        $now = now();
        $year = substr($now->format('Y'), -2); // Last 2 digits of year
        $month = self::$monthLetters[$now->format('n')]; // Month as letter A-L
        $day = $now->format('d'); // Day with leading zero

        $typeInitial = $type === 'out' ? 'O' : 'I';
        $datePrefix = "TRX{$typeInitial}-{$year}{$month}{$day}-";

        // Find the highest sequence number for today
        $latestTransaction = self::where('transaction_code', 'like', $datePrefix . '%')
            ->orderByRaw('CAST(SUBSTRING(transaction_code, -4) AS UNSIGNED) DESC')
            ->first();

        if ($latestTransaction) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($latestTransaction->transaction_code, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First transaction of the day
            $newSequence = 1;
        }

        // Format the sequence as 4 digits
        $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        return $datePrefix . $sequence;
    }
}
