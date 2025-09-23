{{-- resources/views/posts/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">{{ $post->title }}</h2>
  </x-slot>

  <div class="container-page">
    {{-- メタ情報 --}}
    <div class="text-sm text-accent-500 mb-4 flex items-center gap-2">
      @if($post->user)
        <a href="{{ route('users.show', $post->user) }}" class="hover:text-primary-600 font-medium">
          {{ $post->user->name }}
        </a>
      @else
        <span class="text-accent-400">（退会ユーザー）</span>
      @endif
      <span>・{{ $post->created_at->format('Y/m/d H:i') }}</span>
      @if($post->topic)
        <span class="hidden sm:inline">｜</span>
        <a href="{{ route('topics.posts', $post->topic) }}" class="badge hover:opacity-90">
          お題：{{ $post->topic->title }}
        </a>
      @endif
    </div>

    {{-- 本文 --}}
    <article class="card">
      <div class="prose max-w-none prose-p:leading-relaxed dark:prose-invert whitespace-pre-wrap">
        {!! nl2br(e($post->body)) !!}
      </div>
    </article>

    {{-- オーナー操作 --}}
    @if(auth()->id() === $post->user_id)
      <div class="mt-6 flex gap-3">
        <a href="{{ route('posts.edit', $post) }}" class="btn-outline">
          編集
        </a>
        <form method="post" action="{{ route('posts.destroy', $post) }}"
              onsubmit="return confirm('削除しますか？')">
          @csrf @method('delete')
          <button class="btn-outline border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-accent-800">
            削除
          </button>
        </form>
      </div>
    @endif

    {{-- 戻る導線（トピック一覧 or お題の投稿一覧） --}}
    @php
      $topicForBack = isset($topic) ? $topic : ($post->topic ?? null);
    @endphp
    <div class="mt-6">
    <x-back-smart :default="route('topics.index')" />

      </a>
    </div>
  </div>
</x-app-layout>
