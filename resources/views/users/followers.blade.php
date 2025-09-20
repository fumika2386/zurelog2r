<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $user->name }} のフォロワー</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 space-y-3">
        @forelse($users as $u)
            <a href="{{ route('users.show', $u) }}" class="p-3 rounded-xl bg-white dark:bg-gray-800 shadow flex items-center gap-3">
                <img src="{{ $u->avatar_path ? asset('storage/'.$u->avatar_path) : 'https://placehold.co/48x48' }}"
                     class="w-12 h-12 rounded-full object-cover" alt="">
                <div class="font-medium">{{ $u->name }}</div>
            </a>
        @empty
            <p class="text-gray-500">フォロワーはいません。</p>
        @endforelse

        <div>{{ $users->links() }}</div>

        <button type="button" onclick="history.back()" class="inline-flex items-center px-4 py-2 rounded-md border text-sm">
        ← 戻る
        </button>    </div>
</x-app-layout>
