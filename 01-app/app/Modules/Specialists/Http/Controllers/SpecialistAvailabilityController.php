<?php

namespace App\Modules\Specialists\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Specialists\Infrastructure\Models\SpecialistAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialistAvailabilityController extends Controller
{
    public function index(Request $request): View
    {
        $availabilities = SpecialistAvailability::query()
            ->where('specialist_id', $request->user()->specialist?->id)
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get();

        return view('specialist.availability', [
            'availabilities' => $availabilities,
            'weekdayNames' => SpecialistAvailability::weekdayNames(),
            'specialist' => $request->user()->specialist,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'weekday' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'consultation_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
        ]);

        $specialist = $request->user()->specialist;

        SpecialistAvailability::query()->create([
            'specialist_id' => $specialist->id,
            'weekday' => $validated['weekday'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        $specialist->update([
            'consultation_duration_minutes' => $validated['consultation_duration_minutes'],
        ]);

        return back()->with('status', 'Disponibilidad agregada.');
    }

    public function destroy(Request $request, SpecialistAvailability $availability): RedirectResponse
    {
        if ($availability->specialist_id !== $request->user()->specialist?->id) {
            abort(403);
        }

        $availability->delete();

        return back()->with('status', 'Disponibilidad eliminada.');
    }
}
