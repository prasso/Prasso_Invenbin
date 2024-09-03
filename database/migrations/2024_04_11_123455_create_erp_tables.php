<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('emails_queue')) {
            Schema::create('emails_queue', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255);
                $table->boolean('email_sent')->default(false);
                $table->text('text_of_email');
                $table->timestamp('date_sent');
                $table->timestamp('date_queued')->useCurrent();
                $table->timestamps();
            });
        }
        Schema::create('erp_product_statuses', function (Blueprint $table) {
            $table->id('id');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->constrained('users');
        });

        Schema::create('erp_product_types', function (Blueprint $table) {
            $table->id('id');
            $table->string('product_type');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->constrained('users');
        });

        Schema::create('erp_unit_of_measure', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->timestamps();
        });
        Schema::create('erp_categories', function (Blueprint $table) {
            $table->id('id');
            $table->string('category_name');
            $table->string('image_file')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->constrained('users');
        });

        Schema::create('erp_products', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('guid');
            $table->string('sku');
            $table->string('product_name');
            $table->string('short_description')->nullable();
            $table->string('stock_location')->nullable();
            $table->decimal('our_price');
            $table->decimal('retail_price');
            $table->string('currency_code')->nullable();
            $table->foreignId('unit_of_measure_id')->constrained('erp_unit_of_measure');
            $table->string('admin_comments')->nullable();
            $table->decimal('weight')->nullable();
            $table->decimal('length')->nullable();
            $table->decimal('height')->nullable();
            $table->decimal('width')->nullable();
            $table->foreignId('dimension_unit_id')->nullable()->constrained('erp_unit_of_measure');
            $table->integer('list_order')->nullable();
            $table->integer('rating_sum')->nullable();
            $table->integer('total_rating_votes')->nullable();
            $table->string('default_image')->nullable();
            $table->integer('owned_by')->nullable();
            $table->integer('inventory_count')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->foreignId('product_status_id')->nullable()->constrained('erp_product_statuses');
            $table->foreignId('product_type_id')->nullable()->constrained('erp_product_types');
            $table->text('attribute_xml')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->constrained('users');
        });

        Schema::create('erp_product_usage_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erp_product_id');
            $table->string('adjustment_type');
            $table->string('reason');
            $table->integer('adjustment');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // adds 'created_at' and 'updated_at' columns
        });

        Schema::create('erp_images', function (Blueprint $table) {
            $table->id('id');
            $table->string('image_file');
            $table->foreignId('erp_product_id');
            $table->integer('list_order')->nullable();;
            $table->string('caption')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->constrained('users');
        });

        Schema::create('erp_product_descriptors', function (Blueprint $table) {
            $table->id('id');
            $table->string('title');
            $table->string('descriptor')->nullable();;
            $table->boolean('is_bulleted_list')->nullable();;
            $table->integer('list_order')->nullable();;
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('erp_product_id');
        });

        Schema::create('erp_product_category_maps', function (Blueprint $table) {
            $table->foreignId('erp_product_id')->constrained('erp_products');
            $table->foreignId('erp_category_id')->constrained('erp_categories');
            $table->integer('list_order')->nullable();;
            $table->boolean('is_featured')->nullable();;
            $table->timestamps();
            $table->softDeletes();
            $table->primary(['erp_product_id', 'erp_category_id']);
        });

        Schema::create('erp_boms', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('guid');
            $table->foreignId('erp_product_id');
            $table->string('bom_name');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('erp_components', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('erp_product_id')->unsigned()->nullable();
            $table->string('item_description')->nullable();;
            $table->decimal('adjustment_units');
            $table->foreignId('erp_bom_id');
            $table->timestamps();
            $table->softDeletes();

             // Define the foreign key constraint
             $table->foreign('erp_product_id')->references('id')->on('erp_products')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
