<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * Month mapping for SKU generation (A-L for Jan-Dec)
     */
    protected static array $monthLetters = [
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F',
        7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'min_stock',
        'image_path',
    ];

    /**
     * Define activity log options for the model
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'category_id',
                'unit_id',
                'name',
                'sku',
                'description',
                'price',
                'stock',
                'min_stock',
                'image_path',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Product')
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}");
    }
    
    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the unit that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the transaction items for the product.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'product_id');
    }

    /**
     * Check if product is below minimum stock threshold
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
    
    /**
     * Generate a unique SKU in the format CYM-{YY}{M}{DD}-{sequence}
     * 
     * @return string
     */
    public static function generateSku(): string
    {
        $now = now();
        $year = substr($now->format('Y'), -2); // Last 2 digits of year
        $month = self::$monthLetters[$now->format('n')]; // Month as letter A-L
        $day = $now->format('d'); // Day with leading zero
        
        $datePrefix = "CYM-{$year}{$month}{$day}-";
        
        // Find the highest sequence number for today
        $latestProduct = self::where('sku', 'like', $datePrefix . '%')
            ->orderByRaw('CAST(SUBSTRING(sku, -4) AS UNSIGNED) DESC')
            ->first();
            
        if ($latestProduct) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($latestProduct->sku, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First product of the day
            $newSequence = 1;
        }
        
        // Format the sequence as 4 digits
        $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
        
        return $datePrefix . $sequence;
    }
}
