<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">価値観アンケート</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        @if (session('status') === 'saved')
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">保存しました。</div>
        @endif

        <form method="post" action="{{ route('values.survey.store') }}" class="space-y-6">
            @csrf

            @foreach($questions as $q)
                <div class="p-4 rounded-2xl bg-white shadow">
                    <div class="font-medium mb-3">{{ $q->sort_order }}. {{ $q->text }}</div>

                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="answers[{{ $q->id }}]" value="1"
                                   {{ (isset($existing[$q->id]) ? (int)$existing[$q->id]===1 : false) ? 'checked' : '' }}>
                            <span>はい（1）</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="answers[{{ $q->id }}]" value="0"
                                   {{ (isset($existing[$q->id]) ? (int)$existing[$q->id]===0 : false) ? 'checked' : '' }}>
                            <span>いいえ（0）</span>
                        </label>
                    </div>

                    @error("answers.$q->id")
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <x-primary-button>保存する</x-primary-button>
        </form>
    </div>
</x-app-layout>
