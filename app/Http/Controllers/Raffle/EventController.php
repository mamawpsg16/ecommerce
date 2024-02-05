<?php

namespace App\Http\Controllers\Raffle;

use App\Models\Raffle\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::latest()->get();

        return response(['events' => $events]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'name'          => ['required', 'unique:events'],
                    'description'   => ['sometimes'],
                    'date'          => ['required'],
                    'start_date'    => ['required'],
                    'end_date'      => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $data = $validator->validated();

                $event = Event::create($data);
        
                return response(['event' => $event]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to event.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response(['event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        try {
            return DB::transaction(function () use ($request, $event) {
                $validator = Validator::make($request->all(), [
                    'name'          => ['required', Rule::unique('events')->ignore($event->id)],
                    'description'   => ['sometimes'],
                    'date'          => ['required'],
                    'start_date'    => ['required'],
                    'end_date'      => ['required'],
                    'active'        => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $data = $validator->validated();

                $event->update($data);

                return response(['data' => $event]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to event.'.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function events(){
        $events = Event::select(['name as label', 'id as value'])->active()->latest()->get();
    
        return response(['events' => $events]);
    }

    public function updateStatus(Request $request){
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'id'         => ['required'],
                    'status'     => ['required'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }

                $data = $validator->validated();

                $event = Event::findOrFail($data['id']);

                $event->update(['active' => $data['status']]);
                
                return response(['data' => $event]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to event.'.$e->getMessage()], 500);
        }
    }
}
