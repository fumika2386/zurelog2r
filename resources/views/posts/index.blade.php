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



        </article>
      @endforeach
    </div>

    <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
  </div>
</x-app-layout>
