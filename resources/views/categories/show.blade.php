<x-app-layout>
    <x-slot name="title">
        {{ __('Category') . ': ' . $category->name }}
    </x-slot>

    <!-- Posts Section -->
    <section class="grid grid-cols-1 md:grid-cols-2  xl:grid-cols-3 md:gap-6">
        <div class="text-3xl font-bold text-center md:col-span-2 xl:col-span-3 py-4">
            <div>{{ __($category->name) }}</div>
            <div class="text-xl font-normal">{{ $countPosts }} {{ __($countPosts === 1 ? 'Post' : 'Posts') }}</div>
        </div>

        @foreach ($posts as $post)
        <x-post.item :post='$post' limit=250 />
        @endforeach
        <!-- Pagination -->
        <div class="md:col-span-2 my-4 mx-auto">
            {{ $posts->onEachSide(5)->links() }}
        </div>
    </section>
</x-app-layout>
