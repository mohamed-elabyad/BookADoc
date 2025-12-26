<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'address', 'specialty']);
        $page = $request->input('page', 1);

        $cache_key = 'doctors:' . md5(json_encode($filters) . $page);

        $doctors = Cache::tags(['doctors'])
            ->remember($cache_key, 1800, function () use ($filters) {
                return Doctor::with('user')
                    ->where('active', true)
                    ->filter($filters)
                    ->latest()
                    ->paginate(20);
            });

        return view('doctors.index', ['doctors' => $doctors]);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return view('doctors.show', ['doctor' => $doctor]);
    }
}
