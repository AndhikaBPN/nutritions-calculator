<?php

namespace App\Services;

use App\Enums\MealLogStatus;
use App\Models\MealLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class MealLogService
{
    public function __construct(
        private WebhookService $webhookService,
    ) {}

    public function store(User $user, UploadedFile $photo, string $mealType, string $date): MealLog
    {
        $path = $photo->store("meals/{$user->id}/{$date}", 'public');

        $log = MealLog::create([
            'user_id' => $user->id,
            'meal_type' => $mealType,
            'date' => $date,
            'photo_path' => $path,
            'status' => MealLogStatus::Pending,
        ]);

        $this->webhookService->sendToN8n([
            'meal_log_id' => $log->id,
            'photo_url' => asset("storage/{$path}"),
            'meal_type' => $log->meal_type->value,
            'date' => $date,
            'user_id' => $user->id,
        ]);

        return $log;
    }

    public function delete(MealLog $log, int $requestingUserId): void
    {
        abort_unless($log->user_id === $requestingUserId, 403);

        $log->delete();
    }
}
