<x-app-layout>
    <x-slot name="title">
        {{ $title }}
    </x-slot>
    <section class="w-full flex flex-col items-center px-3 mb-6">
        <x-flexible-hero :page="$page" />
        <x-flexible-content-blocks :page="$page"/>
    </section>
</x-app-layout>
