{{-- resources/views/users/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">{{ $user->name }} のプロフィール</h2>
  </x-slot>

  <div class="container-page space-y-6">
    {{-- プロフィール情報 --}}
    <section class="card flex gap-4 items-center">
      <img
        src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://placehold.co/192x192' }}"
        alt="{{ $user->name }} のアバター"
        class="avatar w-24 h-24"
        width="96" height="96" loading="lazy"
      />
      <div class="min-w-0">
        <div class="text-2xl font-bold truncate">{{ $user->name }}</div>
        @if(!empty($user->description))
          <p class="mt-1 text-accent-700 dark:text-accent-200 whitespace-pre-wrap">
            {{ $user->description }}
          </p>
        @endif
      </div>
    </section>

    @php
      $isMe    = auth()->check() && auth()->id() === $user->id;
      $iFollow = auth()->check() ? auth()->user()->isFollowing($user) : false;
      $followersCount  = $user->followers_count  ?? $user->followers()->count();
      $followingsCount = $user->followings_count ?? $user->followings()->count();
    @endphp

    {{-- フォロー情報＆ボタン --}}
    <div class="flex items-center gap-4">
      <a href="{{ route('users.followers', $user) }}" class="underline hover:text-primary-600 text-sm">
        フォロワー {{ $followersCount }}
      </a>
      <a href="{{ route('users.followings', $user) }}" class="underline hover:text-primary-600 text-sm">
        フォロー {{ $followingsCount }}
      </a>

      @auth
        @unless($isMe)
          @if($iFollow)
            <form method="post" action="{{ route('users.unfollow', $user) }}" class="ml-auto">
              @csrf @method('delete')
              <button class="btn-outline text-sm" onclick="this.disabled=true;this.form.submit()">
                フォロー中（解除）
              </button>
            </form>
          @else
            <form method="post" action="{{ route('users.follow', $user) }}" class="ml-auto">
              @csrf
              <button class="btn-primary text-sm" onclick="this.disabled=true;this.form.submit()">
                このアカウントをフォローする
              </button>
            </form>
          @endif
        @endunless
      @endauth

      @guest
        <a href="{{ route('login') }}" class="ml-auto btn-outline text-sm">ログインしてフォロー</a>
      @endguest
    </div>

    {{-- 投稿一覧（このユーザーの） --}}
    @if($posts instanceof \Illuminate\Contracts\Pagination\Paginator || $posts instanceof \Illuminate\Support\Collection)
      <section class="space-y-3">
        <h3 class="text-lg font-semibold">投稿</h3>

        @forelse($posts as $post)
          <article class="card">
            <a href="{{ route('posts.show', $post) }}" class="font-medium hover:text-primary-600">
              {{ $post->title }}
            </a>
            <div class="text-sm text-accent-500 mt-1">
              {{ $post->created_at->format('Y/m/d H:i') }}
              @if($post->topic)
                ｜ お題：
                <a class="underline hover:text-primary-600" href="{{ route('topics.posts', $post->topic) }}">
                  {{ $post->topic->title }}
                </a>
              @endif
            </div>
          </article>
        @empty
          <p class="text-accent-500">まだ投稿がありません。</p>
        @endforelse

        @if(method_exists($posts, 'links'))
          <div>{{ $posts->links() }}</div>
        @endif
      </section>
    @endif

    <a href="{{ route('topics.index') }}" class="btn-outline">一覧へ戻る</a>
  </div>
</x-app-layout>
