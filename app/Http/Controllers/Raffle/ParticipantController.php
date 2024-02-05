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
        try {
            $participants = DB::table('participants')
            ->select(
                'participants.id',
                'participants.name',
                'participants.email',
                'participants.birth_date',
                'participants.personal_phone_number',
                'participants.company_phone_number',
                'participants.industry',
                'participants.company',
                'participants.position',
                'participants.created_at',
                'participants.updated_at',
                'event_items.name AS item',
                'event_participant_items.quantity',
                'events.name AS event'
            )
            ->join('event_participant', 'participants.id', '=', 'event_participant.participant_id')
            ->join('events', 'event_participant.event_id', '=', 'events.id')
            ->join('event_participant_items', function ($join) {
                $join->on('events.id', '=', 'event_participant_items.event_id')
                     ->on('participants.id', '=', 'event_participant_items.participant_id');
            })
            ->join('event_items', 'event_participant_items.item_id', '=', 'event_items.id')
            ->get();
        
            return response(['participants' => $participants]);
        } catch (\Exception $e) {
            return response(['error' => 'Failed to participant item.'.$e->getMessage()], 500);
        }
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
