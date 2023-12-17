<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function showDashboard()
    {
        $allEvents = Event::all(); // Получаем все события
        return view('events.dashboard', compact('allEvents'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'text' => 'required',
        ]);

        $validatedData['creator_id'] = auth()->id();

        $event = Event::create($validatedData);

        return response()->json([
            'error' => null,
            'result' => $event
        ]);
    }

    public function getUserEvents(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не аутентифицирован'], 401);
        }

        $event = $user->events;

        return response()->json([
            'error' => null,
            'result' => $event
        ]);

    }


    public function index()
    {
        $events = Event::with('participants')->get(); // Загрузка событий с участниками
        return response()->json(['error' => null, 'result' => $events]);
    }

    public function show(Event $event) {
        // Загрузка всех пользователей, связанных с событием, через сводную таблицу event_user
        $participants = $event->participants()->get();
        $user = auth()->user();
        $result = [
            'event' => $event,
            'user' => $user,
            'participants' => $participants
        ];
        // Формирование ответа
        return response()->json([
            'error' => null,
            'result' => $result
        ]);
    }

    public function participate(Request $request, Event $event)
    {
        $event->participants()->attach(auth()->id()); // Добавить текущего пользователя в участники
        return response()->json(['error' => null, 'result' => 'Participation successful.']);
    }

    public function withdraw(Request $request, Event $event)
    {
        $event->participants()->detach(auth()->id()); // Удалить текущего пользователя из участников
        return response()->json(['error' => null, 'result' => 'Participation cancelled.']);
    }

    public function destroy(Event $event)
    {
        if ($event->creator_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $event->delete(); // Удалить событие
        return response()->json(['error' => null, 'result' => 'Event deleted.']);
    }
}
