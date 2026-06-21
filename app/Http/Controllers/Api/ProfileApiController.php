<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Traits\ApiResponsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    use ApiResponsable;

    public function __construct(private ProfileService $profileService) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'id'         => $user->id,
            'name'       => $user->name,
            'full_name'  => $user->full_name,
            'email'      => $user->email,
            'gender'     => $user->gender?->value,
            'height_cm'  => $user->height_cm,
            'weight_kg'  => $user->weight_kg,
            'photo_path' => $user->photo_path,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $this->profileService->update(
            user: $request->user(),
            validated: $request->validated(),
            photo: $request->file('photo'),
        );

        $user = $request->user()->fresh();

        return $this->success([
            'id'         => $user->id,
            'name'       => $user->name,
            'full_name'  => $user->full_name,
            'email'      => $user->email,
            'gender'     => $user->gender?->value,
            'height_cm'  => $user->height_cm,
            'weight_kg'  => $user->weight_kg,
            'photo_path' => $user->photo_path,
        ], 'Profil berhasil diperbarui.');
    }
}
