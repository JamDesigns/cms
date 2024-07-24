<footer class="w-full bg-gray-600 pb-8">
    <div class="w-full container mx-auto flex flex-col items-center text-sm text-gray-100">
        <div class="flex flex-col md:flex-row text-center md:text-left md:justify-between py-6">
            <a href="{{ route('about-us') }}"
                class="uppercase mx-3 border-b-4 border-transparent hover:border-b-4 hover:border-blue-900">{{ __('About Us') }}</a>
            {{-- <a href="{{ route('policy.show') }}"
                class="uppercase mx-3 border-b-4 border-transparent hover:border-b-4 hover:border-blue-900"
                target="_blanck">{{ __('Privacy Policy') }}</a>
            <a href="{{ route('terms.show') }}"
                class="uppercase mx-3 border-b-4 border-transparent hover:border-b-4 hover:border-blue-900"
                target="_blanck">{{ __('Terms & Conditions') }}</a>
            <a href="{{ route('contactus.index') }}"
                class="uppercase mx-3 border-b-4 border-transparent hover:border-b-4 hover:border-blue-900">{{ __('Contact Us') }}</a> --}}
        </div>
        <div class="uppercase pb-6">2024 &copy; {{ config('app.name') }}</div>
    </div>
</footer>
