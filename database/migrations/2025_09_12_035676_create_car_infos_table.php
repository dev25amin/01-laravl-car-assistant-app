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
        Schema::create('car_infos', function (Blueprint $table) {
            $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('car_brand')->nullable()->comment('ماركة السيارة');
            $table->string('car_model')->nullable()->comment('موديل السيارة');
            $table->integer('car_year')->nullable()->comment('سنة الصنع');
            $table->integer('current_mileage')->nullable()->comment('الكيلومترات الحالية');
            $table->date('last_oil_change')->nullable()->comment('آخر تغيير زيت');
            $table->date('last_maintenance')->nullable()->comment('آخر صيانة');
            $table->decimal('fuel_level', 5, 2)->nullable()->comment('كمية الوقود بالليتر');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_infos');
    }
};