<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">投稿一覧</h2></x-slot>
    <div class="max-w-4xl mx-auto p-6">
        <a href="{{ route('posts.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">新規投稿</a>
        <div class="mt-4 space-y-4">
            @foreach($posts as $post)
                <div class="p-4 rounded bg-white dark:bg-gray-800 shadow">
                    <a href="{{ route('posts.show', $post) }}" class="font-semibold text-lg">{{ $post->title }}</a>
                    <div class="text-sm text-gray-500">
                        {{ $post->user->name }}・{{ $post->created_at->format('Y/m/d H:i') }}
                        @if($post->topic) ｜ お題：{{ $post->topic->title }} @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $posts->links() }}</div>
    </div>
</x-app-layout>
