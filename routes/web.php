<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarAssistantController;


Route::get('/', function () {
    return view('/');
})->middleware(['auth', 'verified'])->name('car.assistant');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



// الصفحة الرئيسية للمساعد الذكي
Route::get('/', [CarAssistantController::class, 'index'])->name('car.assistant');

// حفظ معلومات السيارة
Route::post('/save-car-info', [CarAssistantController::class, 'saveCarInfo'])->name('car.info.save');

// تشخيص المشاكل
Route::post('/diagnose-problem', [CarAssistantController::class, 'diagnoseProblem'])->name('car.diagnose');

// معلومات Gemini
Route::get('/gemini-info', [CarAssistantController::class, 'geminiInfo'])->name('car.gemini-info');

// اختبار Gemini
Route::get('/test-gemini', [CarAssistantController::class, 'testGemini'])->name('car.test-gemini');

// === Routes الجديدة للتحليلات ===

// عرض سجل التحليلات
Route::get('/analysis-history', [CarAssistantController::class, 'analysisHistory'])->name('analysis.history');

// عرض تفاصيل تحليل معين
Route::get('/analysis/{id}', [CarAssistantController::class, 'showAnalysis'])->name('analysis.show');

// حذف تحليل
Route::delete('/analysis/{id}', [CarAssistantController::class, 'deleteAnalysis'])->name('analysis.delete');
Route::post('/quick-image-analysis', [CarAssistantController::class, 'quickImageAnalysis'])->name('quick.image.analysis');

});

require __DIR__.'/auth.php';
