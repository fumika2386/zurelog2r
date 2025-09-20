<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">My Page</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow">
                <div class="flex items-center gap-4">
                    <img
                        src="{{ asset('storage/'.auth()->user()->avatar_path) }}"
                        alt="avatar"
                        class="mt-3 rounded-full object-cover"
                        style="width:96px;height:96px;object-fit:cover;border-radius:9999px"
                    >
                    <div>
                        <div class="text-2xl font-bold">{{ $user->name }}</div>
                        <!-- <div class="text-gray-500">{{ $user->email }}</div> -->
                    </div>
                </div>
                <p class="mt-4 whitespace-pre-wrap text-gray-700">{{ $user->description ?? '自己紹介はまだありません。' }}</p>

                <div class="mt-6 flex gap-6 text-sm">
                    <div><span class="font-semibold">{{ $user->posts_count ?? 0 }}</span> 投稿</div>
                        <div class="mt-4 flex items-center gap-4">
                            <a href="{{ route('users.followers', $user) }}" class="text-sm underline">
                                フォロワー {{ $user->followers_count }}
                            </a>
                            <a href="{{ route('users.followings', $user) }}" class="text-sm underline">
                                フォロー {{ $user->followings_count }}
                            </a>
                        </div>

                </div>

                <a href="{{ route('profile.edit') }}" class="mt-6 inline-block px-4 py-2 rounded-xl bg-blue-600 text-white">
                    プロフィールを編集
                </a>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow">
                <h3 class="text-lg font-semibold mb-4">あなたの最新投稿</h3>
                @forelse($user->posts()->latest()->limit(5)->get() as $post)
                    <div class="border-b py-3">
                        <div class="font-semibold">{{ $post->title }}</div>
                        <div class="text-sm text-gray-500">{{ $post->created_at->format('Y/m/d H:i') }}</div>
                        <p class="mt-1 line-clamp-3">{{ Str::limit($post->body, 160) }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">まだ投稿がありません。</p>
                @endforelse
                <a href="{{ url('/posts') }}" class="mt-4 inline-block text-blue-600">投稿一覧へ</a>
            </div>
        </div>
    </div>
</x-app-layout>
