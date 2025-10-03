<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">価値観アンケート</h2>
  </x-slot>

  <div class="container-page max-w-3xl">
    @if (session('status') === 'saved')
      <div class="mb-4 p-3 rounded-xl bg-green-100 text-green-800">保存しました。</div>
    @endif

    <form method="post" action="{{ route('values.survey.store') }}" class="space-y-5">
      @csrf

      @foreach($questions as $q)
        <div class="card p-5">
          <div class="font-medium mb-3">
            {{ $q->sort_order }}. {{ $q->text }}
          </div>

          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2">
              <input
                type="radio"
                name="answers[{{ $q->id }}]"
                value="1"
                class="form-radio text-primary-600 focus:ring-primary-400"
                {{ (isset($existing[$q->id]) ? (int)$existing[$q->id]===1 : false) ? 'checked' : '' }}
              >
              <span class="text-accent-700 dark:text-accent-100">はい（1）</span>
            </label>

            <label class="inline-flex items-center gap-2">
              <input
                type="radio"
                name="answers[{{ $q->id }}]"
                value="0"
                class="form-radio text-primary-600 focus:ring-primary-400"
                {{ (isset($existing[$q->id]) ? (int)$existing[$q->id]===0 : false) ? 'checked' : '' }}
              >
              <span class="text-accent-700 dark:text-accent-100">いいえ（0）</span>
            </label>
          </div>

          @error("answers.$q->id")
            <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
          @enderror
        </div>
      @endforeach

      <button class="btn-primary">保存する</button>
    </form>
  </div>
</x-app-layout>
