{{-- resources/views/mypage/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">My Page</h2>
  </x-slot>

  <div class="container-page">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      {{-- 左：プロフィールカード --}}
      <section class="card">
        <div class="flex items-center gap-4">
          <img
            src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://placehold.co/192x192' }}"
            alt="{{ $user->name }} のアバター"
            class="avatar w-24 h-24"
            width="96" height="96" loading="lazy"
          >
          <div class="min-w-0">
            <div class="text-2xl font-bold truncate">{{ $user->name }}</div>
            {{-- <div class="text-accent-500 text-sm truncate">{{ $user->email }}</div> --}}
          </div>
        </div>

        <p class="mt-4 whitespace-pre-wrap text-accent-700 dark:text-accent-200">
          {{ $user->description ?: '自己紹介はまだありません。' }}
        </p>

        <div class="mt-6 flex items-center gap-6 text-sm">
          <div><span class="font-semibold">{{ $user->posts_count ?? 0 }}</span> 投稿</div>
          <div class="flex items-center gap-4">
            <a href="{{ route('users.followers', $user) }}" class="hover:text-primary-600 underline">
              フォロワー {{ $user->followers_count ?? $user->followers()->count() }}
            </a>
            <a href="{{ route('users.followings', $user) }}" class="hover:text-primary-600 underline">
              フォロー {{ $user->followings_count ?? $user->followings()->count() }}
            </a>
          </div>
        </div>

        <a href="{{ route('profile.edit') }}" class="mt-6 inline-flex btn-primary">
          プロフィールを編集
        </a>
      </section>

      {{-- 右：最新投稿 --}}
      <section class="md:col-span-2 card">
        <h3 class="text-lg font-semibold mb-4">あなたの最新投稿</h3>

        @php
          $latest = $user->posts()->latest()->limit(5)->get();
        @endphp

        @forelse($latest as $post)
          <article class="py-3 border-b last:border-0">
            <a href="{{ route('posts.show', $post) }}" class="font-semibold hover:text-primary-600">
              {{ $post->title }}
            </a>
            <div class="text-xs text-accent-500">{{ $post->created_at->format('Y/m/d H:i') }}</div>
            @if($post->body)
              <p class="mt-1 text-sm text-accent-700 dark:text-accent-200 line-clamp-3">
                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 160) }}
              </p>
            @endif
          </article>
        @empty
          <p class="text-accent-500">まだ投稿がありません。</p>
        @endforelse

        <div class="mt-4 flex gap-3">
          <a href="{{ route('posts.index') }}" class="btn-outline">投稿一覧へ</a>
          <a href="{{ route('posts.create') }}" class="btn-primary">新規投稿</a>
        </div>
      </section>
    </div>
  </div>
</x-app-layout>
