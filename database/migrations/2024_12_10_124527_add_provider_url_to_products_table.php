<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('provider_url')->nullable()->after('img');
            $table->decimal('base_price', 8, 2)->nullable()->after('price');
        });

        DB::table('products')->insert([
            'alias' => '110gems',
            'base_price' => 929,
            'provider_url' => 'https://playerok.com/products/b734da25dd1e-110-gemov-brawl-stars-bezopasno-bystro-i-deshevo',
            'price' => 2000
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('provider_url');
            $table->dropColumn('base_price');
        });
    }
};
