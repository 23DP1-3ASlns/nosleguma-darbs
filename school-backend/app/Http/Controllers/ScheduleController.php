<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ScheduleEntry::query();

        if ($request->has('group') && $request->group !== '') {
            $query->where('group_name', $request->group);
        }

        $schedule = $query->orderByRaw("CASE day_of_week WHEN 'Pirmdiena' THEN 1 WHEN 'Otrdiena' THEN 2 WHEN 'Trešdiena' THEN 3 WHEN 'Ceturtdiena' THEN 4 WHEN 'Piektdiena' THEN 5 END")
                          ->orderBy('start_time')
                          ->get();

        return response()->json($schedule);
    }

    public function groups(): JsonResponse
    {
        $groups = ScheduleEntry::distinct()->pluck('group_name')->sort()->values();
        return response()->json($groups);
    }

    private function checkConflict(Request $request, ?int $excludeId = null): ?array
    {
        $query = ScheduleEntry::where('teacher', $request->teacher)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->where('start_time', '<', $request->end_time)
                          ->where('end_time', '>', $request->start_time);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflict = $query->first();

        if ($conflict) {
            return [
                'message' => "Skolotājam '{$request->teacher}' jau ir nodarbība {$request->day_of_week} no {$conflict->start_time} līdz {$conflict->end_time} ({$conflict->subject}, {$conflict->group_name}).",
                'conflict' => $conflict,
            ];
        }

        return null;
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'group_name' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'day_of_week' => 'required|in:Pirmdiena,Otrdiena,Trešdiena,Ceturtdiena,Piektdiena',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $conflict = $this->checkConflict($request);
        if ($conflict) {
            return response()->json(['errors' => ['conflict' => [$conflict['message']]], 'conflict' => $conflict['conflict']], 422);
        }

        $entry = ScheduleEntry::create($request->only([
            'group_name', 'subject', 'teacher', 'day_of_week', 'start_time', 'end_time'
        ]));

        return response()->json($entry, 201);
    }

    public function update(Request $request, ScheduleEntry $schedule): JsonResponse
    {
        $request->validate([
            'group_name' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'day_of_week' => 'required|in:Pirmdiena,Otrdiena,Trešdiena,Ceturtdiena,Piektdiena',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $conflict = $this->checkConflict($request, $schedule->id);
        if ($conflict) {
            return response()->json(['errors' => ['conflict' => [$conflict['message']]], 'conflict' => $conflict['conflict']], 422);
        }

        $schedule->update($request->only([
            'group_name', 'subject', 'teacher', 'day_of_week', 'start_time', 'end_time'
        ]));

        return response()->json($schedule->fresh());
    }

    public function destroy(ScheduleEntry $schedule): JsonResponse
    {
        $schedule->delete();
        return response()->json(['message' => 'Ieraksts dzēsts.']);
    }
}
