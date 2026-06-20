<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    public function update(User $user, array $validated, ?UploadedFile $photo): void
    {
        if ($photo) {
            $validated['photo_path'] = $photo->store("profiles/{$user->id}", 'public');
        }

        unset($validated['photo']);

        $user->update($validated);
    }
}
