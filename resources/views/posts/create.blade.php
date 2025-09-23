{{-- resources/views/posts/create.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="section-title flex items-center">新規投稿</h2>
  </x-slot>

  <div class="container-page max-w-3xl">
    @if (session('status') === 'created')
      <div class="mb-4 p-3 rounded-xl bg-green-100 text-green-800">作成しました。</div>
    @endif

    <form
      method="post"
      action="{{ route('posts.store') }}"
      class="space-y-6"
      x-data="{ title: '{{ old('title') }}', body: '{{ str_replace(["\n","'"], ['\n','&#39;'], old('body','')) }}' }"
    >
      @csrf

      {{-- お題の事前選択 or プルダウン --}}
      @if(isset($topic) && $topic)
        <div class="card">
          <div class="text-xs text-accent-500">お題</div>
          <div class="mt-1 font-medium">{{ $topic->title }}</div>
          <input type="hidden" name="topic_id" value="{{ $topic->id }}">
        </div>
      @else
        <div class="card">
          <x-input-label for="topic_id" value="お題（任意）" />
          <select id="topic_id" name="topic_id" class="input mt-1">
            <option value="">選択しない</option>
            @foreach($topics as $t)
              <option value="{{ $t->id }}" @selected(old('topic_id')==$t->id)>{{ $t->title }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('topic_id')" class="mt-2" />
        </div>
      @endif

      {{-- タイトル --}}
      <div class="card">
        <div class="flex items-center justify-between">
          <x-input-label for="title" value="タイトル" />
          <span class="text-xs text-accent-500">
            <span x-text="title.length"></span>/255
          </span>
        </div>
        <x-text-input id="title" name="title" type="text"
                      class="input mt-1"
                      x-model="title"
                      maxlength="255"
                      value="{{ old('title') }}" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
      </div>

      {{-- 本文 --}}
      <div class="card">
        <div class="flex items-center justify-between">
          <x-input-label for="body" value="本文" />
          <span class="text-xs text-accent-500">
            <span x-text="body.length"></span>/10000
          </span>
        </div>
        <textarea id="body" name="body" rows="10"
                  class="input mt-1"
                  x-model="body" maxlength="10000">{{ old('body') }}</textarea>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
      </div>

      <div class="flex gap-3">
        <button class="btn-primary">投稿する</button>
        <a href="{{ url()->previous() }}" class="btn-outline">戻る</a>
      </div>
    </form>
  </div>
</x-app-layout>
