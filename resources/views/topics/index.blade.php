<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">お題一覧</h2></x-slot>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($topics as $topic)
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-800 shadow">
                <div class="text-lg font-semibold">{{ $topic->title }}</div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                    {{ Str::limit($topic->description, 120) }}
                </p>
            
                <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('topics.posts', $topic) }}"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700">
                    {{ $topic->posts_count }} 件の投稿
                </a>

                <a href="{{ route('posts.create', ['topic_id' => $topic->id]) }}"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border  bg-gray-100 text-gray-900 w-auto shrink-0 ml-auto relative z-10">
                    このお題で投稿
                </a>
                </div>

            </div>
        @empty
            <p class="col-span-full text-gray-500">お題がまだありません。</p>
        @endforelse
    </div>

    <div class="max-w-6xl mx-auto p-6">{{ $topics->links() }}</div>
</x-app-layout>
