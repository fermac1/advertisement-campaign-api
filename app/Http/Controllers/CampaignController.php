<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class CampaignController extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);  
        $cacheKey = "campaigns_page_{$page}";

        $campaigns = Cache::remember($cacheKey, 60, function () { 
            return Campaign::with('creatives')->latest()->paginate(5);
        });
        return response()->json($campaigns);
    }

    
    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date',
            'total_budget' => 'required|numeric|min:0',
            'daily_budget' => 'required|numeric|min:0',
            'creatives' => 'nullable|array',
            'creatives.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);

  
        if ($validated->fails()) {
            $errors = $validated->errors();

       
            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

          
            Log::error('Validation errors:', $errorMessages);
            return redirect()->back()->withErrors($validated)->withInput()->setStatusCode(422);
        }

 
        $validatedData = $validated->validated();

        try {
        
            if ($id) {
                $campaign = Campaign::find($id);

                if (!$campaign) {
                    return response()->json(['message' => 'Data not found'], 404);
                }

                $campaign->update($validatedData);

         
                $page = request()->get('page', 1); 
                Cache::forget("campaigns_page_{$page}"); 

                if ($request->hasFile('creatives')) {
                    $creatives = $request->file('creatives');
                    foreach ($creatives as $creative) {
                        $creativePath = $creative->store('creatives', 'public');
                        $creativeName = $creative->getClientOriginalName();
                        $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
                    }
                }

                return response()->json(['message' => 'Data updated successfully', 'data' => $campaign], 200);
            } else {
                $campaign = Campaign::create($validatedData);

                if ($request->hasFile('creatives')) {
                    $creatives = $request->file('creatives');
                    foreach ($creatives as $creative) {
                        $creativePath = $creative->store('creatives', 'public');
                        $creativeName = $creative->getClientOriginalName();
                        $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
                    }
                }

                return response()->json(['message' => 'Data created successfully', 'data' => $campaign], 201); 
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }

    }
}
