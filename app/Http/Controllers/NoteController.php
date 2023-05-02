<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\NoteRequest;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $notes = Note::query()
            ->select(['id', 'title', 'content'])
            ->whereUser($authUser)
            ->whereContains($request->input('q'))
            ->latest()
            ->get()
            ->append(['excerpt']);

        return Inertia::render('Notes/Index', [
            'notes' => $notes->toArray()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Notes/Create');
    }

    public function store(NoteRequest $request): RedirectResponse
    {
        $request->user()->notes()->create($request->all());

        return redirect()->route('notes.index')->with('status', 'Note created!!');
    }

    public function show(Note $note): Response
    {
        return Inertia::render('Notes/Show', compact('note'));
    }

    public function edit(Note $note): Response
    {
        return Inertia::render('Notes/Edit', compact('note'));
    }

    public function update(NoteRequest $request, Note $note): RedirectResponse
    {
        $note->update($request->all());

        return redirect()->route('notes.index')->with('status', 'Note updated!!');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Note deleted');
    }
}
