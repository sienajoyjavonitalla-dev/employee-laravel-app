<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarNote;

class CalendarNoteContoller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $calendarNote = CalendarNote::updateOrCreate([
            'id' => $request->id
        ],[
            'message' => $request->message,
            'note_date' => $request->note_date
        ]);

        return response()->json([
            'id' => $calendarNote->id,
            'message' => $calendarNote->message,
            'note_date' => $calendarNote->note_date
        ]);
    }

    public function destroy($id)
    {
        $calendarNote = CalendarNote::find($id);

        $deletedNoteId = $calendarNote->id;

        $calendarNote->delete();

        return response()->json([
            'id' => $deletedNoteId
        ]);
    }
}
