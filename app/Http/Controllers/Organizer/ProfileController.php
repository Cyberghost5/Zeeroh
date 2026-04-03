<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\OrganizerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = auth()->user()->organizerProfile;

        return view('organizer.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:120'],
            'bio'               => ['nullable', 'string', 'max:1000'],
            'logo'              => ['nullable', 'image', 'max:2048'],
            'website'           => ['nullable', 'url', 'max:255'],
            'facebook'          => ['nullable', 'url', 'max:255'],
            'twitter'           => ['nullable', 'url', 'max:255'],
            'instagram'         => ['nullable', 'url', 'max:255'],
            'bank_name'         => ['nullable', 'string', 'max:100'],
            'account_number'    => ['nullable', 'string', 'max:10', 'min:10', 'regex:/^\d+$/'],
            'account_name'      => ['nullable', 'string', 'max:120'],
        ]);

        $profile = $user->organizerProfile ?? new OrganizerProfile(['user_id' => $user->id]);

        // Generate slug from organization name if not set
        if (empty($profile->slug)) {
            $baseSlug = Str::slug($validated['organization_name']);
            $slug     = $baseSlug;
            $i        = 1;
            while (OrganizerProfile::where('slug', $slug)->where('id', '!=', $profile->id ?? 0)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $profile->slug = $slug;
        }

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $profile->fill($validated);
        $profile->user_id = $user->id;
        $profile->save();

        return redirect()->route('organizer.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
