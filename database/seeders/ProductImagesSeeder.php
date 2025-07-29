<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Tạo tên ảnh dựa trên category
            $imageName = $this->getImageNameByCategory($product->category);
            
            // Cập nhật sản phẩm với ảnh mẫu
            $product->update([
                'image' => 'products/' . $imageName
            ]);
        }
        
        $this->command->info('Đã cập nhật ảnh cho ' . $products->count() . ' sản phẩm!');
    }
    
    private function getImageNameByCategory($category)
    {
        $images = [
            'skincare' => [
                'skincare-1.jpg',
                'skincare-2.jpg', 
                'skincare-3.jpg',
                'skincare-4.jpg',
                'skincare-5.jpg'
            ],
            'makeup' => [
                'makeup-1.jpg',
                'makeup-2.jpg',
                'makeup-3.jpg'
            ],
            'perfume' => [
                'perfume-1.jpg',
                'perfume-2.jpg'
            ],
            'haircare' => [
                'haircare-1.jpg',
                'haircare-2.jpg'
            ],
            'bodycare' => [
                'bodycare-1.jpg'
            ],
            'tools' => [
                'tools-1.jpg'
            ]
        ];
        
        $categoryImages = $images[$category] ?? ['default.jpg'];
        return $categoryImages[array_rand($categoryImages)];
    }
}
