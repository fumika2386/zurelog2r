<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $post->title }}</h2></x-slot>
    <div class="max-w-3xl mx-auto p-6">
        <div class="text-sm text-gray-500 mb-4">
            {{ $post->user->name }}・{{ $post->created_at->format('Y/m/d H:i') }}
            @if($post->topic) ｜ お題：{{ $post->topic->title }} @endif
        </div>
        <div class="prose max-w-none whitespace-pre-wrap">{{ $post->body }}</div>

        @if(auth()->id() === $post->user_id)
            <div class="mt-6 flex gap-3">
                <a href="{{ route('posts.edit', $post) }}" class="px-3 py-2 rounded bg-yellow-500 text-white">編集</a>
                <form method="post" action="{{ route('posts.destroy', $post) }}"
                      onsubmit="return confirm('削除しますか？')">
                    @csrf @method('delete')
                    <button class="px-3 py-2 rounded bg-red-600 text-white">削除</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
