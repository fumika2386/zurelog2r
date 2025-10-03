<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $user->name }} のフォロワー</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 space-y-3">
        @forelse($users as $u)
            <a href="{{ route('users.show', $u) }}"
               class="p-3 rounded-xl bg-white dark:bg-gray-800 shadow flex items-center gap-3">
                {{-- アバター：正方形で中央トリミング＆真円 --}}
                <img
                  src="{{ $u->avatar_path ? asset('storage/'.$u->avatar_path) : 'https://placehold.co/96x96' }}"
                  alt="{{ $u->name }} のアバター"
                  class="w-12 h-12 rounded-full object-cover shrink-0"
                  width="48" height="48" loading="lazy"
                  onerror="this.src='https://placehold.co/96x96';"
                />
                {{-- テキスト側は省略や折返しに強く --}}
                <div class="min-w-0">
                    <div class="font-medium truncate">{{ $u->name }}</div>
                    @if(!empty($u->description))
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                            {{ $u->description }}
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-gray-500">フォロワーはいません。</p>
        @endforelse

        <div>{{ $users->links() }}</div>

        <button type="button"
                onclick="history.back()"
                class="inline-flex items-center px-4 py-2 rounded-md border text-sm">
            ← 戻る
        </button>
    </div>
</x-app-layout>
