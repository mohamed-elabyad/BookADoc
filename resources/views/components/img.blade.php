@props(['doctor', 'profile' => false, 'class' => ''])

@php
    $imageUrl = $doctor->image
        ? (Str::startsWith($doctor->image, 'doctors/')
            ? Storage::url($doctor->image)
            : asset($doctor->image))
        : asset('images/default-avatar.png');
@endphp

<img src="{{ $imageUrl }}" alt="{{ $doctor->user->name }}" @class([
    'rounded-full object-cover mb-3 border-2 border-slate-300 shadow-md',
    'w-45 h-45 mb-6 mt-4' => $profile,
    'w-25 h-25' => !$profile,
    $class,
])>
