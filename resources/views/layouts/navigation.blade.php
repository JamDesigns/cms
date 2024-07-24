<nav x-data="{ open: false }" class="sticky top-0 w-full h-14 mx-auto bg-white border-b-2 border-gray-500">

    <!-- Primary Navigation Menu -->
    <div class="container mx-auto px-3 sm:p-0">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}">
                        <x-application-mark class="block h-8 w-auto" />
                    </a>
                </div>
                <div class="hidden sm:block">
                    <div class="flex gap-2 items-baseline space-x-4">
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a class="px-4 py-2 border border-transparent rounded text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                                href="{{ route('home') }}">
                                {{ __('Home') }}
                            </a>
                        </div>
                        @if ($totalPosts > 5)
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a class="px-4 py-2 border border-transparent rounded text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                                href="{{ route('posts.index') }}">
                                {{ __('Blog') }}
                            </a>
                        </div>
                        @endif
                        @if ($categories && $categories->count() > 0)
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-dropdown align="right" width="48" contentClasses="bg-white overflow-hidden">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm rounded-md text-gray-700 bg-white hover:text-white hover:bg-indigo-800">
                                        {{ __('Categories') }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">

                                    @foreach ($categories as $category)
                                    <a class="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                                        href="{{ route('category.show', $category) }}">
                                        {{ __($category->name) }}
                                        ({{ $category->posts_count <= 99 ? $category->posts_count : '99+' }})
                                    </a>
                                    @endforeach

                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="flex items-center">
                    @if (count(config('laravellocalization.supportedLocales')) > 1)
                    <x-dropdown align="right" width="48" contentClasses="py-0 overflow-hidden bg-white">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center content-center px-4 py-2 gap-2 border border-transparent text-sm rounded-md text-gray-700 bg-white hover:text-white hover:bg-indigo-800">
                                <span class="inline-flex flex-row gap-x-2 h-6 w-auto">
                                    <img class="h-6 w-6" src={{ strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0,
                                        strpos($_SERVER['SERVER_PROTOCOL'], '/' ))) . '://' . $_SERVER['HTTP_HOST'] . "/language-flags/country-"
                                        . LaravelLocalization::getCurrentLocale() . ".svg" }} alt="">
                                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                                </span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a class="{{ $localeCode === LaravelLocalization::getCurrentLocale() ? "
                                hidden " : ""}}inline-flex flex-row gap-x-2 h-10 w-full px-4 py-2 text-start text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                                hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                <img class="h-6 w-6" src={{ strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0,
                                    strpos($_SERVER['SERVER_PROTOCOL'], '/' ))) . '://' . $_SERVER['HTTP_HOST']
                                    . "/language-flags/country-$localeCode.svg" }} alt="" />
                                <span class="col-span-2">{{ $properties['native'] }}</span>
                            </a>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                    @endif
                    <div class="relative ml-3">
                        @auth
                        <!-- Settings Dropdown -->
                        <x-dropdown align="right" width="48" contentClasses="pt-1 pb-0 overflow-hidden bg-white">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm h-10 w-10 rounded-full border-2 border-transparent hover:border-2 hover:border-indigo-800">
                                    <img class="h-9 w-9 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->name }}" />
                                </button>
                                @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm  rounded-md text-gray-700 bg-white hover:text-white hover:bg-indigo-800">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                @unlessrole('Front User')
                                <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                                    href="{{ route('dashboard') }}">
                                    {{ __('Dashboard') }}
                                </a>
                                @endunlessrole

                                <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                                    href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </a>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                                    href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </a>
                                @endif

                                <div class="border-t border-gray-200"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                                        href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </x-slot>
                        </x-dropdown>
                        @else
                        <a class="px-4 py-2 border border-transparent rounded text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                            href="{{ route('login') }}" class="text-sm text-gray-700 underline">{{ __('Login') }}</a>

                        @if (Route::has('register'))
                        <a class="px-4 py-2 border border-transparent rounded text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                            href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">{{ __('Register') }}</a>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex sm:hidden">
                <!-- Mobile menu button -->
                <button type="button" @click="open = ! open"
                    class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-900 hover:bg-gray-700  hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">{{ __('Open main menu') }}</span>
                    <!-- Menu open: "hidden", Menu closed: "block" -->
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Menu open: "block", Menu closed: "hidden" -->
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile menu, show/hide based on menu state. -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <a href="{{ route('home') }}" class="block rounded-md text-gray-700 hover:text-white hover:bg-indigo-800 px-4 py-2 text-base font-medium"
                aria-current="page">
                {{ __('Home') }}
            </a>
            @if ($totalPosts > 5)
            <a class="w-full px-4 py-2 text-center text-gray-700 hover:text-white hover:bg-indigo-800"
                href="{{ route('posts.index') }}">
                {{ __('Blog') }}
            </a>
            @endif
        </div>

    </div>
</nav>

                {{--
    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="flex flex-col gap-2 ">
            <a class="w-full px-4 py-2 text-center text-gray-700 hover:text-white hover:bg-indigo-800"
                href="{{ route('home') }}">{{ __('Home') }}</a>
            @if ($totalPosts > 5)
            <a class="w-full px-4 py-2 text-center text-gray-700 hover:text-white hover:bg-indigo-800"
                href="{{ route('posts.index') }}">{{ __('Blog') }}</a>
            @endif
            @if ($categories && $categories->count() > 0)
            <x-dropdown align="left" width="w-full" contentClasses="block bg-white overflow-hidden" dropdownClasses="inline-block static">
                <x-slot name="trigger">
                    <button
                        class="block w-full px-4 py-2 text-center align-content-center text-gray-700 hover:text-white hover:bg-indigo-800">
                        {{ __('Categories') }}
                    </button>
                </x-slot>

                <x-slot name="content">
                    @foreach ($categories as $category)
                    <a class="block w-full px-4 py-2 text-center text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('category.show', $category) }}">
                        {{ __($category->name) }}
                        ({{ $category->posts_count <= 99 ? $category->posts_count : '99+' }})
                    </a>
                    @endforeach
                </x-slot>
            </x-dropdown>
            @endif
            @if (count(config('laravellocalization.supportedLocales')) > 1)
            <x-dropdown align="left" width="w-full" contentClasses="flex flex-col gap-2 py-2 bg-white overflow-hidden" dropdownClasses="inline-block static">
                <x-slot name="trigger">
                    <button
                        class="block w-full align-content-center px-4 py-2 gap-2 border border-transparent text-sm rounded-md text-gray-700 bg-white hover:text-white hover:bg-indigo-800">
                        <div class="inline-flex gap-1">
                            <img class="h-6 w-6 mr-2" src={{ strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))) . '://' . $_SERVER['HTTP_HOST'] . "/language-flags/country-" . LaravelLocalization::getCurrentLocale()
                                . ".svg" }} alt="">
                            {{ LaravelLocalization::getCurrentLocaleNative() }}
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <div class="text-center">
                    <a class="{{ $localeCode === LaravelLocalization::getCurrentLocale() ? "
                        hidden " : ""}}px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-indigo-800"
                        hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <div class="inline-flex gap-2">
                            <img class="h-6 w-6" src={{ strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))) . '://' . $_SERVER['HTTP_HOST'] . "/language-flags/country-$localeCode.svg" }} alt="" />
                            {{ $properties['native'] }}
                        </div>
                    </a>
                    </div>
                    @endforeach
                </x-slot>
            </x-dropdown>
            @endif
            @auth
            <!-- Responsive Settings Options -->
            <div class="border-t border-gray-200 align-content-center">
                <div class="py-2 flex flex-row gap-2 justify-center">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="inline-block text-center text-sm">
                        <img class="h-6 w-6 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                    @endif
                    <div class="flex-col gap-1 text-sm">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1 border-t">
                    <!-- Account Management -->
                    @unlessrole('Front User')
                    <a class="block w-full px-4 py-2 text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                    @endunlessrole

                    <a class="block w-full px-4 py-2 text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </a>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <a class="block w-full px-4 py-2 text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('api-tokens.index') }}">
                        {{ __('API Tokens') }}
                    </a>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <a class="block w-full px-4 py-2 text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                            href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
            @else
            <div class="py-2 border-t border-gray-200">
                <div class="flex flex-col gap-2 items-center">
                    <a class="block w-full text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('login') }}">{{ __('Login') }}</a>

                    @if (Route::has('register'))
                    <a class="block w-full text-center leading-5 text-gray-700 hover:text-white hover:bg-indigo-800"
                        href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</nav> --}}
