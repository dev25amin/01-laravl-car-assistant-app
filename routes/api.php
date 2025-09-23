<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ApiCarAssistantController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (لا تحتاج authentication)
// التسجيل + تسجيل الدخول
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

// Car Assistant Public Routes
Route::prefix('car-assistant/public')->group(function () {
    Route::get('/status', function () {
        return response()->json(['status' => 'Car Assistant API is running']);
    });
});

// Protected Routes (تحتاج authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [ApiController::class, 'logout']);
    
    // Profile route
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // Car Assistant API Routes
    Route::prefix('car-assistant')->group(function () {
        
        // Get car info and dashboard statistics
        Route::get('/', [ApiCarAssistantController::class, 'index']);
        
        // Car Info Management
        Route::post('/car-info', [ApiCarAssistantController::class, 'saveCarInfo']);
        Route::get('/car-info', [ApiCarAssistantController::class, 'getCarInfo']);
        
        // Problem Diagnosis
        Route::post('/diagnose-problem', [ApiCarAssistantController::class, 'diagnoseProblem']);
        
        // Analysis History
        Route::get('/analysis-history', [ApiCarAssistantController::class, 'analysisHistory']);
        Route::get('/analysis/{id}', [ApiCarAssistantController::class, 'showAnalysis']);
        Route::delete('/analysis/{id}', [ApiCarAssistantController::class, 'deleteAnalysis']);
        
        // Test Gemini Connection
        Route::get('/test-gemini', [ApiCarAssistantController::class, 'testGemini']);
    });

    // Products API Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});