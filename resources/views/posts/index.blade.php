<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">みんなの投稿一覧</h2></x-slot>
            {{-- 並べ替えタブ --}}
        @php
        $q = request()->except('page'); // ページ以外の既存クエリ保持
        $link = function($key) use ($q) { return route('posts.index', array_merge($q, ['sort'=>$key])); };
        $active = $sort ?? request('sort','new');
        @endphp

        <div class="max-w-3xl mx-auto px-6 mt-2 mb-4">
        <div class="inline-flex gap-2 text-sm">
            <a href="{{ $link('new') }}" class="px-3 py-1 rounded border {{ $active==='new' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : '' }}">新着順</a>
            <a href="{{ $link('old') }}" class="px-3 py-1 rounded border {{ $active==='old' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : '' }}">古い順</a>
            @auth
            <a href="{{ $link('near') }}" class="px-3 py-1 rounded border {{ $active==='near' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : '' }}">価値観の近い順</a>
            <a href="{{ $link('far') }}" class="px-3 py-1 rounded border {{ $active==='far' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : '' }}">価値観の遠い順</a>
            @endauth
            @guest
            <span class="px-3 py-1 rounded border opacity-60" title="ログインすると使えます">価値観の近い順</span>
            <span class="px-3 py-1 rounded border opacity-60" title="ログインすると使えます">価値観の遠い順</span>
            @endguest
        </div>
        </div>

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
