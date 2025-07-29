<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-low {--threshold=5 : Số lượng tối thiểu để cảnh báo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cảnh báo về sản phẩm có stock thấp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = $this->option('threshold');
        
        $this->info("🔍 Đang kiểm tra sản phẩm có stock thấp (dưới {$threshold} sản phẩm)...");
        
        $lowStockProducts = Product::where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->where('is_active', true)
            ->get();
            
        $outOfStockProducts = Product::where('stock', '<=', 0)
            ->where('is_active', true)
            ->get();
        
        if ($lowStockProducts->count() > 0) {
            $this->warn("\n⚠️  Sản phẩm có stock thấp:");
            $this->table(
                ['ID', 'Tên sản phẩm', 'SKU', 'Stock hiện tại', 'Danh mục'],
                $lowStockProducts->map(function ($product) {
                    return [
                        $product->id,
                        $product->name,
                        $product->sku ?? 'N/A',
                        $product->stock,
                        $product->category_name
                    ];
                })
            );
        }
        
        if ($outOfStockProducts->count() > 0) {
            $this->error("\n❌ Sản phẩm hết hàng:");
            $this->table(
                ['ID', 'Tên sản phẩm', 'SKU', 'Danh mục'],
                $outOfStockProducts->map(function ($product) {
                    return [
                        $product->id,
                        $product->name,
                        $product->sku ?? 'N/A',
                        $product->category_name
                    ];
                })
            );
        }
        
        if ($lowStockProducts->count() == 0 && $outOfStockProducts->count() == 0) {
            $this->info("\n✅ Tất cả sản phẩm đều có đủ stock!");
        }
        
        $this->info("\n📊 Thống kê tổng quan:");
        $this->info("- Tổng số sản phẩm: " . Product::where('is_active', true)->count());
        $this->info("- Sản phẩm có stock thấp: " . $lowStockProducts->count());
        $this->info("- Sản phẩm hết hàng: " . $outOfStockProducts->count());
        
        return 0;
    }
}
