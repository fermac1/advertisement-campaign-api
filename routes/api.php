<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;

Route::get('/campaigns', [CampaignController::class, 'index']);
// Route::match(['post', 'put'],'/campaigns/{id?}', [CampaignController::class, 'storeOrUpdate']);
// Route::match(['post', 'put'],'/campaigns/{id}', [CampaignController::class, 'storeOrUpdate']);
// Route::put('/campaigns/{id}', [CampaignController::class, 'storeOrUpdate']);
Route::post('/campaigns/{id?}', [CampaignController::class, 'storeOrUpdate']);

// Route::post('/campaigns/{id}', [CampaignController::class, 'storeOrUpdate']);

