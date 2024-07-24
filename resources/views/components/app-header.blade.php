@props(['title' => config('app.name'), 'description' => '', 'image' => ''])

@php
    if (isset($image)) {
        $style = "background-image: url('/images/backgrounds/". $image ."')";
    } else {
        $style = "";
    }
@endphp

<header class="w-full mx-auto grid grid-col-1 gap-4 content-center content-evently bg-blue-700 h-48 bg-cover" style="{{ $style }};">
    <div class="text-center font-bold text-indigo-700 uppercase text-5xl">
        {{ $title }}
    </div>
    <div class="text-lg text-center text-wrap text-gray-100">
        {{ $description }}
    </div>
</header>
