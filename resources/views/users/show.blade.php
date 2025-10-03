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

        {{-- resources/views/users/show.blade.php のプロフィールカード内などに --}}
        @php $tags = $user->tags()->orderBy('sort_order')->get(); @endphp
        @if($tags->isNotEmpty())
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($tags as $tag)
            <span class="badge">#{{ $tag->name }}</span>
            @endforeach
        </div>
        @endif


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
