<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $post->title }}</h2></x-slot>
    <div class="max-w-3xl mx-auto p-6">
        <div class="text-sm text-gray-500 mb-4">
            @if($post->user)
                <a href="{{ route('users.show', $post->user) }}" class="text-blue-600 hover:underline">
                    {{ $post->user->name }}
                </a>
            @else
                <span class="text-gray-400">（退会ユーザー）</span>
            @endif
            ・{{ $post->created_at->format('Y/m/d H:i') }}
            @if($post->topic)
                ｜ お題：<a href="{{ route('topics.posts', $post->topic) }}" class="underline">{{ $post->topic->title }}</a>
            @endif
        </div>

        <div class="prose max-w-none whitespace-pre-wrap">{{ $post->body }}</div>

        @if(auth()->id() === $post->user_id)
            <div class="mt-6 flex gap-3">
                <a href="{{ route('posts.edit', $post) }}" class="px-3 py-2 rounded bg-yellow-500 text-white">
                    編集
                </a>
                <form method="post" action="{{ route('posts.destroy', $post) }}"
                      onsubmit="return confirm('削除しますか？')">
                    @csrf @method('delete')
                    <button class="px-3 py-2 rounded bg-red-600 text-white">削除</button>
                </form>

            </div>
        @endif
                @php
                    $topicForBack = isset($topic)
                        ? $topic
                        : ((isset($post) && $post->topic) ? $post->topic : null);
                @endphp

                <a href="{{ $topicForBack ? route('topics.posts', $topicForBack) : route('topics.index') }}"
                class="inline-flex items-center px-4 py-2 rounded-md border text-sm">
                一覧へ戻る
                </a>

    </div>
</x-app-layout>
