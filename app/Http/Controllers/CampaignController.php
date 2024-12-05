<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;

class CampaignController extends Controller
{
    public function index()
    {
        // $campaigns = Campaign::with('creatives')->latest()->paginate(5);
        // $campaigns = Cache::remember('campaigns', 60, function () {
        //     return Campaign::all();
        // });
        $page = request()->get('page', 1);  // Get the current page from the request
        $cacheKey = "campaigns_page_{$page}";

        $campaigns = Cache::remember($cacheKey, 60, function () { //expires in 60 minutes
            return Campaign::with('creatives')->latest()->paginate(5);
        });
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'total_budget' => 'required|numeric|min:0',
            'daily_budget' => 'required|numeric|min:0',
            'creatives' => 'nullable|array',
            'creatives.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $campaign = Campaign::create($validated);

        try {
            if ($request->hasFile('creatives')) {
                $creatives = $request->file('creatives');
                foreach ($creatives as $creative) {
                    $creativePath = $creative->store('creatives', 'public');
                    $creativeName = $creative->getClientOriginalName();
                    echo $creativeName;
                    $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
                }
            }
    
            return response()->json($campaign, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }
    
        
}
