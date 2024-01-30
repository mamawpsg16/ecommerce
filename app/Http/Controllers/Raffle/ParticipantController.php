<?php

namespace App\Http\Controllers\Raffle;

use Illuminate\Http\Request;
use App\Models\Raffle\Participant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Raffle\ParticipantRequest;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
       $participants = Participant::with(['events'])->get();
    //    $participants = Participant::join('event_participant', 'participants.id', '=', 'event_participant.participant_id')
    //    ->join('events', 'event_participant.event_id', '=', 'events.id')
    //    ->join('items', 'event_participant.event_id', '=', 'events.id')
    //    ->get();

       return response(['participants' => $participants]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ParticipantRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                $participant = Participant::create($data);

                $participant->events()->attach([$data['event']['value']]);
        
                return response(['message' => 'Successfully registered!', 'participant' => $participant, 'event_id' => $data['event']['value']]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save product.'.$e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Participant $participant)
    {  
        $details =  $participant->load(['events' => function($q){
            $q->select('events.id', 'name');
        }]);
        return response(['participant' => $details]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeItem(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'participant_id' => ['required','numeric'],
                    'event_id'       => ['required','numeric'],
                    'item'           => ['required','array'],
                ]);
        
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()], 422);
                }
                
                $data = $validator->validated();

                $participant_event_item = DB::table('event_participant_items')
                ->insert([
                    'event_id'       => $data['event_id'], 
                    'participant_id' => $data['participant_id'],
                    'item_id'        => $data['item']['id'],
                    'quantity'       => $data['item']['quantity'],
                    'item_name'      => $data['item']['name'],
                    'created_at'     => now(),
                ]);
                return response(['status' => 200]);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to participant item.'.$e->getMessage()], 500);
        }
    }
}
