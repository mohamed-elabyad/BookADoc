<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpParser\Comment\Doc;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $doctor = Auth::user()->doctor;

        return view('doctors.show', ['doctor' => $doctor]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $doctor = Auth::user()->doctor;

        return view('doctors.profile.edit', ['doctor' => $doctor]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileDoctorRequest $request)
    {
        $doctor = Auth::user()->doctor;

        $validatedData = $request->validated();

        if ($request->has('image')) {
            $validatedData['image'] = $request->file('image')->store('doctors/images', 'public')
                ?? $doctor->image;
        }

        $doctor->update(Arr::except($validatedData, ['name']));
        $user = $doctor->user->update(Arr::only($validatedData, ['name']));

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
