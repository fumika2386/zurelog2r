
@props(['post'])

@php
  use App\Models\PostReaction;

  // 事前に withCount していれば $post->reactions_total 等から取れるが、
  // 単体でも動くよう保険で集計（小規模用）。必要なら最適化は後段に。
  $counts = $post->reactions()
      ->selectRaw('stamp, COUNT(*) as c')
      ->groupBy('stamp')
      ->pluck('c','stamp');

  $total = (int) $counts->sum();
  $my = auth()->check()
        ? $post->reactions()->where('user_id', auth()->id())->value('stamp')
        : null;

  $stamps = PostReaction::STAMPS;
@endphp

<div class="mt-3 flex flex-wrap gap-2">
  @foreach($stamps as $i => $meta)
    @php
      $cnt = (int) ($counts[$i] ?? 0);
      $pct = $total > 0 ? round($cnt * 100 / $total) : 0;
      $isMine = (!is_null($my) && (int)$my === (int)$i);
    @endphp
    <form method="POST" action="{{ route('posts.react', $post) }}"
          class="inline-flex items-center gap-1">
      @csrf
      <input type="hidden" name="stamp" value="{{ $i }}">
      <button class="px-2 py-1 rounded-xl border text-sm
                     @if($isMine) bg-orange-50 border-orange-400 text-orange-700 @else bg-white @endif">
        <span>{{ $meta['emoji'] }}</span>
        <span class="ml-1">{{ $pct }}%</span>
      </button>
    </form>
  @endforeach
  @if($total>0)
    <span class="ml-2 text-xs text-gray-500">n={{ $total }}</span>
  @endif
</div>
