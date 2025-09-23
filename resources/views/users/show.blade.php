<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $user->name }} のプロフィール</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-800 shadow flex gap-4 items-center">
            <img
            src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://placehold.co/96x96' }}"
            alt="avatar"
            class="mt-1 rounded-full object-cover"
            style="width:96px;height:96px;border-radius:9999px;object-fit:cover"
            />
            <div>
                <div class="text-xl font-semibold">{{ $user->name }}</div>
                @if(!empty($user->description))
                    <p class="mt-1 text-gray-600 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $user->description }}
                    </p>
                @endif
            </div>
        </div>

        @php
            $isMe = auth()->check() && auth()->id() === $user->id;
            $iFollow = auth()->check() ? auth()->user()->isFollowing($user) : false;
        @endphp

        <div class="flex items-center gap-3 mt-2">
            {{-- フォロワー/フォロー数 --}}
            <a href="{{ route('users.followers', $user) }}" class="text-sm underline">
                フォロワー {{ $user->followers_count }}
            </a>
            <a href="{{ route('users.followings', $user) }}" class="text-sm underline">
                フォロー {{ $user->followings_count }}
            </a>


            {{-- 自分のプロフィールではボタン非表示 --}}
            @auth
                @unless($isMe)
                    @if($iFollow)
                        <form method="post" action="{{ route('users.unfollow', $user) }}">
                            @csrf @method('delete')
                            <button class="px-3 py-1 rounded bg-gray-600 text-black text-sm">フォロー中</button>
                        </form>
                    @else
                        <form method="post" action="{{ route('users.follow', $user) }}">
                            @csrf
                            <button class="px-3 py-1 rounded bg-blue-600 text-black text-sm">フォロー</button>
                        </form>
                    @endif
                @endunless
            @endauth
        </div>


        @if($posts instanceof \Illuminate\Contracts\Pagination\Paginator || $posts instanceof \Illuminate\Support\Collection)
            <div class="space-y-3">
                <h3 class="font-semibold">投稿</h3>
                @forelse($posts as $post)
                    <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 shadow">
                        <a href="{{ route('posts.show', $post) }}" class="font-medium hover:underline">
                            {{ $post->title }}
                        </a>
                        <div class="text-sm text-gray-500">
                            {{ $post->created_at->format('Y/m/d H:i') }}
                            @if($post->topic) ｜ お題：<a class="underline" href="{{ route('topics.posts', $post->topic) }}">{{ $post->topic->title }}</a>@endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">まだ投稿がありません。</p>
                @endforelse

                @if(method_exists($posts, 'links'))
                    <div>{{ $posts->links() }}</div>
                @endif
            </div>
        @endif

        <a href="{{ route('topics.index') }}" class="inline-flex items-center px-4 py-2 rounded-md border text-sm">
            一覧へ戻る
        </a>
    </div>
</x-app-layout>
