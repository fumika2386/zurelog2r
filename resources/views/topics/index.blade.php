{{-- resources/views/topics/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">お題一覧</h2>
  </x-slot>

  <div class="container-page grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($topics as $topic)
      <article class="card">
        <h3 class="text-lg font-semibold">{{ $topic->title }}</h3>
        <p class="mt-2 text-sm text-accent-700 dark:text-accent-200 line-clamp-3">
          {{ \Illuminate\Support\Str::limit($topic->description, 120) }}
        </p>

        <div class="mt-4 flex items-center gap-3">
          {{-- 皆の投稿へ（バッジ表示） --}}
          <a href="{{ route('topics.posts', $topic) }}"
             class="badge">
            {{ $topic->posts_count }} 件の投稿
          </a>

          {{-- このお題で投稿（右寄せ） --}}
          <a href="{{ route('posts.create', ['topic_id' => $topic->id]) }}"
             class="btn-primary ml-auto">
            このお題で投稿
          </a>
        </div>
      </article>
    @empty
      <p class="col-span-full text-accent-500">お題がまだありません。</p>
    @endforelse
  </div>

  <div class="container-page">
    {{ $topics->links() }}
  </div>
</x-app-layout>
