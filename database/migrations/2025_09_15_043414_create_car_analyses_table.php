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
        Schema::create('car_analyses', function (Blueprint $table) {
            $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('car_info_id')->nullable()->constrained('car_infos')->onDelete('set null');
            $table->string('analysis_type'); // condition_analysis, problem_diagnosis
            $table->json('input_data'); // البيانات المدخلة للتحليل
            $table->text('analysis_result'); // نتيجة التحليل
            $table->timestamp('analysis_date')->useCurrent();
            $table->timestamps();
            $table->json('analysis_images')->nullable(); // ✅ شيلنا after()
            
            // إضافة فهرس للبحث السريع
            $table->index(['analysis_type', 'created_at']);
            $table->index('analysis_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_analyses');
    }
};
