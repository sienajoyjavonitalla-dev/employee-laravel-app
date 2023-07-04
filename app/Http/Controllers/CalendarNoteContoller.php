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
        // $calendarNote = new CalendarNote($request->message);

        $calendarNote = CalendarNote::create([
            'message' => $request->message,
            'note_date' => $request->note_date
        ]);

        return response()->json([
            'message' => $calendarNote->message,
            'note_date' => $calendarNote->note_date
        ]);
    }

    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
