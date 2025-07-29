<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->default('skincare')->after('image');
            $table->string('brand')->nullable()->after('category');
            $table->string('sku')->unique()->nullable()->after('brand');
            $table->integer('stock')->default(0)->after('sku');
            $table->boolean('is_featured')->default(false)->after('stock');
            $table->boolean('is_active')->default(true)->after('is_featured');
            $table->string('meta_title')->nullable()->after('is_active');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('ingredients')->nullable()->after('meta_description');
            $table->text('usage_instructions')->nullable()->after('ingredients');
            $table->string('skin_type')->nullable()->after('usage_instructions');
            $table->string('age_group')->default('all')->after('skin_type');
            $table->string('shelf_life')->nullable()->after('age_group');
            $table->string('weight')->nullable()->after('shelf_life');
            $table->string('dimensions')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'brand',
                'sku',
                'stock',
                'is_featured',
                'is_active',
                'meta_title',
                'meta_description',
                'ingredients',
                'usage_instructions',
                'skin_type',
                'age_group',
                'shelf_life',
                'weight',
                'dimensions'
            ]);
        });
    }
};
