{{-- resources/views/posts/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">みんなの投稿一覧</h2>
  </x-slot>

  {{-- 並べ替えタブ --}}
  @php
    $q = request()->except('page'); // ページ以外の既存クエリ保持
    $link = fn($key) => route('posts.index', array_merge($q, ['sort'=>$key]));
    $active = $sort ?? request('sort','new');
  @endphp

  <div class="container-page pt-3 pb-0">
    <div class="inline-flex gap-2 text-sm">
      @php
        $tab = 'px-3 py-1 rounded-xl border border-accent-300 text-accent-700 dark:border-accent-600 dark:text-accent-100';
        $tabActive = 'bg-primary-500 text-white border-primary-500';
      @endphp
      <a href="{{ $link('new') }}" class="{{ $tab }} {{ $active==='new' ? $tabActive : '' }}">新着順</a>
      <a href="{{ $link('old') }}" class="{{ $tab }} {{ $active==='old' ? $tabActive : '' }}">古い順</a>
      @auth
        <a href="{{ $link('near') }}" class="{{ $tab }} {{ $active==='near' ? $tabActive : '' }}">価値観の近い順</a>
        <a href="{{ $link('far')  }}" class="{{ $tab }} {{ $active==='far'  ? $tabActive : '' }}">価値観の遠い順</a>
      @endauth
      @guest
        <span class="{{ $tab }} opacity-60" title="ログインすると使えます">価値観の近い順</span>
        <span class="{{ $tab }} opacity-60" title="ログインすると使えます">価値観の遠い順</span>
      @endguest
    </div>
  </div>

  <div class="container-page">
    <div class="flex justify-end mb-4">
      <a href="{{ route('posts.create') }}" class="btn-primary">新規投稿</a>
    </div>

    <div class="space-y-4">
      @foreach($posts as $post)
        <article class="card space-y-2">
          <header class="flex items-center gap-3">
            <img
              src="{{ $post->user->avatar_path ? asset('storage/'.$post->user->avatar_path) : 'https://placehold.co/96x96' }}"
              alt="{{ $post->user->name }}"
              class="avatar w-10 h-10"
              width="40" height="40" loading="lazy"
            />
            <div class="min-w-0">
              <a href="{{ route('users.show', $post->user) }}" class="font-medium hover:text-primary-600 truncate">
                {{ $post->user->name }}
              </a>
              <div class="text-xs text-accent-500">
                {{ $post->created_at->format('Y/m/d H:i') }}
                @if($post->topic)
                  ｜ <span class="badge">お題：{{ $post->topic->title }}</span>
                @endif
              </div>
            </div>
            @if(isset($post->similarity))
              <div class="ml-auto text-xs text-accent-500">
                一致率：{{ number_format($post->similarity * 100, 0) }}%
              </div>
            @endif
          </header>

          <a href="{{ route('posts.show', $post) }}" class="block">
            <h3 class="text-lg font-semibold hover:text-primary-600">{{ $post->title }}</h3>
            @if($post->body)
              <p class="text-sm text-accent-700 dark:text-accent-200 line-clamp-3">
                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
              </p>
            @endif
          </a>
        </article>
      @endforeach
    </div>

    <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
  </div>
</x-app-layout>
