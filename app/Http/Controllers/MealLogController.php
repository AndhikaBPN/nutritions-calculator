<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealLogRequest;
use App\Repositories\MealLogRepository;
use App\Services\MealLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MealLogController extends Controller
{
    public function __construct(
        private MealLogService $mealLogService,
        private MealLogRepository $mealLogRepo,
    ) {}

    public function store(StoreMealLogRequest $request): RedirectResponse
    {
        $this->mealLogService->store(
            user: $request->user(),
            photo: $request->file('photo'),
            mealType: $request->input('meal_type'),
            date: $request->input('date', today()->toDateString()),
        );

        return back()->with('success', 'Foto berhasil diupload. Menunggu analisis nutrisi...');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $log = $this->mealLogRepo->findOrFail($id);

        $this->mealLogService->delete($log, $request->user()->id);

        return back()->with('success', 'Data makanan berhasil dihapus.');
    }
}
