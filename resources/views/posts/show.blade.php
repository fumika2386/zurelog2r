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

    {{-- ★ スタンプ：割合表示（非同期） --}}
    @php
    // withCount から初期値を作る
    $initialCounts = [];
    for ($i=0; $i<=4; $i++) {
        $initialCounts[$i] = (int) ($post->{"reactions_s{$i}"} ?? 0);
    }
    $initialTotal = (int) ($post->reactions_total ?? array_sum($initialCounts));
    $my = auth()->check()
        ? $post->reactions()->where('user_id', auth()->id())->value('stamp')
        : null;
    @endphp

    <div
    x-data="postReactions({
        postId: {{ $post->id }},
        counts: @json($initialCounts),
        total: {{ $initialTotal }},
        my: {{ is_null($my) ? 'null' : (int)$my }},
        csrf: '{{ csrf_token() }}'
    })"
    class="mt-3 flex flex-wrap gap-2"
    >
    @foreach(\App\Models\PostReaction::STAMPS as $i => $meta)
        <form
        method="POST"
        action="{{ route('posts.react', $post) }}"
        @submit.prevent="toggle({{ $i }})"
        class="inline-flex"
        >
        @csrf
        <input type="hidden" name="stamp" value="{{ $i }}">
        <button type="submit"
            class="px-2 py-1 rounded-xl border text-sm inline-flex items-center gap-1"
            :class="my === {{ $i }} ? 'bg-orange-50 border-orange-400 text-orange-700' : 'bg-white'"
            :aria-label="`スタンプ {{ $meta['label'] }} を付ける（${pct({{ $i }})}%）`"
        >
            <span>{{ $meta['emoji'] }}</span>
            <span class="ml-1" x-text="pct({{ $i }}) + '%'"></span>
        </button>
        </form>
    @endforeach

    </div>

    {{-- Alpine ロジック（レイアウトの末尾に1回置いてもOK。重複時はどちらか一方に） --}}
    <script>
    document.addEventListener('alpine:init', () => {
    Alpine.data('postReactions', ({ postId, counts, total, my, csrf }) => ({
        postId, counts, total, my, csrf,
        pct(i) { const c = this.counts[i] || 0; return this.total ? Math.round(c*100/this.total) : 0; },
        async toggle(i) {
        // フォーム送信を横取りして fetch（スクロール位置維持）
        const fd = new FormData(); fd.append('stamp', i);
        const res = await fetch(`/posts/${this.postId}/react`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept':'application/json' },
            body: fd
        });
        if (!res.ok) return; // 必要ならトースト等
        const json = await res.json();
        if (json?.ok) {
            this.counts = {0:0,1:0,2:0,3:0,4:0, ...json.counts};
            this.total  = json.total ?? 0;
            this.my     = (json.my ?? null);
        }
        }
    }));
    });
    </script>


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
