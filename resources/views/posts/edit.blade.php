<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">投稿を編集</h2></x-slot>
    <div class="max-w-3xl mx-auto p-6">
        <form method="post" action="{{ route('posts.update', $post) }}" class="space-y-6">
            @csrf @method('put')
            <div>
                <x-input-label for="title" value="タイトル" />
                <x-text-input id="title" name="title" type="text"
                              class="mt-1 block w-full"
                              :value="old('title', $post->title)" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="body" value="本文" />
                <textarea id="body" name="body" rows="8" class="mt-1 block w-full">{{ old('body', $post->body) }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>
            <x-primary-button>更新する</x-primary-button>
        </form>
    </div>
</x-app-layout>
