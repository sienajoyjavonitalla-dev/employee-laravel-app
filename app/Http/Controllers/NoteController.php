<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    // Display a list of notes
    public function index()
    {
        $notes = Note::all();
        return view('notes.index', compact('notes'));
    }

    // Show the form for creating a new note
    public function create()
    {
        return view('notes.create');
    }

    // Store a newly created note in the database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $note = Note::updateOrCreate([
            'id' => $request->id
        ],
        [
            'title' => $request->title,
            'content' => $request->content
        ]);


        return redirect()->route('notes.index')->with('success', 'Note saved successfully.');
    }

    // Display the specified note
    public function show(Note $note)
    {
        return view('notes.show', compact('note'));
    }

    // Show the form for editing the specified note
    public function edit(Note $note)
    {
        return response()->json($note);
    }

    // Update the specified note in the database
    public function update(Request $request, Note $note)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $note->update($validatedData);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    // Remove the specified note from the database
    public function destroy(Note $note)
    {
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}
