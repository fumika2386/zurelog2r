<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $topic->title }} の投稿</h2></x-slot>

    <div class="max-w-4xl mx-auto p-6 space-y-4">
        @forelse($posts as $post)
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 shadow">
                <a href="{{ route('posts.show', $post) }}" class="text-lg font-semibold">{{ $post->title }}</a>
                <div class="text-sm text-gray-500">
                    {{ $post->user->name }}・{{ $post->created_at->format('Y/m/d H:i') }}
                </div>
                <p class="mt-2">{{ Str::limit($post->body, 160) }}</p>
            </div>
        @empty
            <p class="text-gray-500">まだ投稿がありません。</p>
        @endforelse

        <div>{{ $posts->links() }}</div>
    </div>
</x-app-layout>
