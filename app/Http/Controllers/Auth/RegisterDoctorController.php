<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\DoctorPendingNotification;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegisterDoctorController extends Controller
{
    public function create()
    {
        return view('auth.register-doctor');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterDoctorRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $image_path = $request->file('image')->store('doctors/images', 'public');
            $license_path = $request->file('license')->store('doctors/licenses', 'public');
            $degree_path = $request->file('degree')->store('doctors/degrees', 'public');

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'doctor'
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'specialty' => $validated['specialty'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'ticket_price' => $validated['ticket_price'],
                'work_from' => $validated['work_from'],
                'work_to' => $validated['work_to'],
                'image' => $image_path,
                'license' => $license_path,
                'degree' => $degree_path,
                'bio' => $validated['bio'] ?? null,
                'active' => false
            ]);

            DB::commit();

            Auth::login($user);

            $user->notify(
                new DoctorPendingNotification($user)
            );

            return redirect()->intended('/')
                ->with('success', 'Doctor application submitted. Awaiting admin approval');

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files
            Storage::disk('public')->delete([
                $image_path ?? '',
                $license_path ?? '',
                $degree_path ?? ''
            ]);


            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
