<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\temp_orders;
use App\Models\products;

class ClearExpiredTempOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:clear-expired-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus temp_orders yang expired dan kembalikan stok';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = temp_orders::where('expired_at', '<', now())->get();
        $this->info("Jumlah temp_orders expired: " . $expiredOrders->count());

        foreach ($expiredOrders as $order) {
            $cart = json_decode($order->cart, true);
            foreach ($cart as $productId => $item) {
                $product = products::find($productId);
                if ($product) {
                    $product->quantity += $item['quantity'];
                    $product->save();
                }
            }
            $order->delete();
        }

        $this->info("Expired orders berhasil dihapus dan stok dikembalikan.");
    
    }
}
