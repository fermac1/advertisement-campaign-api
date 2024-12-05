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
        $page = request()->get('page', 1);  // Get the current page from the request
        $cacheKey = "campaigns_page_{$page}";

        $campaigns = Cache::remember($cacheKey, 60, function () { //expires in 60 minutes
            return Campaign::with('creatives')->latest()->paginate(5);
        });
        return response()->json($campaigns);
    }

    // public function storeOrUpdate(Request $request, $id = null)
    // public function storeOrUpdate(Request $request, $id = null)
    // {
    //     // $validated = $request->validate([
    //     //     'name' => 'required|string',
    //     //     'from' => 'required|date',
    //     //     'to' => 'required|date|after_or_equal:from',
    //     //     'total_budget' => 'required|numeric|min:0',
    //     //     'daily_budget' => 'required|numeric|min:0',
    //     //     'creatives' => 'nullable|array',
    //     //     'creatives.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    //     // ]);

    //     $validated = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'from' => 'required|date',
    //         'to' => 'required|date|after_or_equal:from',
    //         'total_budget' => 'required|numeric|min:0',
    //         'daily_budget' => 'required|numeric|min:0',
    //         'creatives' => 'nullable|array',
    //         'creatives.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);
    
    //     if ($validated->fails()) {
    //         return redirect()->back()->withErrors($validated)->withInput();
    //     }

    //       // If 'id' exists in the request, update the record
    //       if ($id) {
    //         echo 'hi';
    //         // $campaign = Campaign::find($id);

    //         // if (!$campaign) {
    //         //     return response()->json(['message' => 'Data not found'], 404);
    //         // }

    //         // $campaign->update($validated);
    //         // return response()->json(['message' => 'Data updated successfully', 'data' => $data], 200);
    //     } else {
    //         echo 'no id';
    //         // $campaign = Campaign::create($validated);

    //         // try {
    //         //     if ($request->hasFile('creatives')) {
    //         //         $creatives = $request->file('creatives');
    //         //         foreach ($creatives as $creative) {
    //         //             $creativePath = $creative->store('creatives', 'public');
    //         //             $creativeName = $creative->getClientOriginalName();
    //         //             echo $creativeName;
    //         //             $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
    //         //         }
    //         //     }
        
    //         //     return response()->json(['message' => 'Data created successfully', 'data' => $campaign], 201);
    //         // } catch (Exception $e) {
    //         //     return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
    //         // }

    //     }
        
 

    // }
    
    public function storeOrUpdate(Request $request, $id = null)
    // {
    //     // Validation
    //     $validated = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'from' => 'required|date',
    //         'to' => 'required|date',
    //         'total_budget' => 'required|numeric|min:0',
    //         'daily_budget' => 'required|numeric|min:0',
    //         'creatives' => 'nullable|array',
    //         'creatives.*' => 'image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     // If validation fails
    //     if ($validated->fails()) {
    //         return redirect()->back()->withErrors($validated)->withInput()->setStatusCode(422);
    //     }

    //     // Extract validated data
    //     $validatedData = $validated->validated();

    //     try {
    //         // If 'id' exists in the request, update the record
    //         if ($id) {
    //             $campaign = Campaign::find($id);

    //             if (!$campaign) {
    //                 return response()->json(['message' => 'Data not found'], 404);
    //             }

    //             $campaign->update($validatedData);


    //             // Invalidate the cache for the affected page
    //             $page = request()->get('page', 1); 
    //             Cache::forget("campaigns_page_{$page}"); 


    //             if ($request->hasFile('creatives')) {
    //                 $creatives = $request->file('creatives');
    //                 foreach ($creatives as $creative) {
    //                     $creativePath = $creative->store('creatives', 'public');
    //                     $creativeName = $creative->getClientOriginalName();
    //                     $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
    //                 }
    //             }

    //             return response()->json(['message' => 'Data updated successfully', 'data' => $campaign], 200);
    //         } else {
    //             $campaign = Campaign::create($validatedData);

    //             if ($request->hasFile('creatives')) {
    //                 $creatives = $request->file('creatives');
    //                 foreach ($creatives as $creative) {
    //                     $creativePath = $creative->store('creatives', 'public');
    //                     $creativeName = $creative->getClientOriginalName();
    //                     $campaign->creatives()->create(['path' => $creativePath, 'file_name' => $creativeName]);
    //                 }
    //             }

    //             return response()->json(['message' => 'Data created successfully', 'data' => $campaign], 201); 
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
    //     }
    // }
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

        // If validation fails
        if ($validated->fails()) {
            $errors = $validated->errors();

            // Loop through the errors and return a detailed message for each
            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            // Optionally, you can log the errors for debugging purposes
            Log::error('Validation errors:', $errorMessages);
            return redirect()->back()->withErrors($validated)->withInput()->setStatusCode(422);
        }

        // Extract validated data
        $validatedData = $validated->validated();

        try {
            // If 'id' exists in the request, update the record
            if ($id) {
                $campaign = Campaign::find($id);

                if (!$campaign) {
                    return response()->json(['message' => 'Data not found'], 404);
                }

                $campaign->update($validatedData);

                // Invalidate the cache for the affected page
                $page = request()->get('page', 1); 
                Cache::forget("campaigns_page_{$page}"); 

                // Handle file upload if any
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

                // Handle file upload if any
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
