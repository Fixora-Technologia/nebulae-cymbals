<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $product;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Low Stock Alert: ' . $this->product->name)
                    ->greeting('Hello!')
                    ->line('This is to inform you that the following product is running low on stock:')
                    ->line('Product: ' . $this->product->name)
                    ->line('SKU: ' . $this->product->sku)
                    ->line('Current Stock: ' . $this->product->stock)
                    ->line('Minimum Stock Threshold: ' . $this->product->min_stock)
                    ->action('View Product', url('/admin/products/' . $this->product->id))
                    ->line('Please consider restocking this item soon.')
                    ->salutation('Thank you, Nebulae Cymbals');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'current_stock' => $this->product->stock,
            'min_stock' => $this->product->min_stock,
            'message' => 'Product is running low on stock',
        ];
    }
}
