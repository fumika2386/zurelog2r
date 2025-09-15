{{-- resources/views/posts/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">新規投稿</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        <form method="post" action="{{ route('posts.store') }}" class="space-y-6">
            @csrf

            {{-- 「このお題で投稿」から来た場合の事前選択 --}}
            @if(isset($topic))
                <div class="mb-3 p-3 rounded bg-gray-50 dark:bg-gray-700">
                    <div class="text-sm text-gray-600">お題：</div>
                    <div class="font-medium">{{ $topic->title }}</div>
                    <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                </div>
            @endif

            <div>
                <x-input-label for="title" value="タイトル" />
                <x-text-input id="title" name="title" type="text"
                              class="mt-1 block w-full"
                              :value="old('title')" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="body" value="本文" />
                <textarea id="body" name="body" rows="8" class="mt-1 block w-full">{{ old('body') }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>

            <x-primary-button>投稿する</x-primary-button>
        </form>
    </div>
</x-app-layout>
