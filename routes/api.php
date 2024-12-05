<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;

Route::get('/campaigns', [CampaignController::class, 'index']);
Route::post('/campaigns/{id?}', [CampaignController::class, 'storeOrUpdate']);

