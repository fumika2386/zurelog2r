@props([
  'default' => '/',   // 参照元がないときの戻り先
  'class'   => 'btn-outline', // 見た目のクラス（任意）
  'label'   => '← 戻る',
])

<button type="button" class="{{ $class }}" onclick="smartBack('{{ $default }}')">
  {{ $label }}
</button>

{{-- ページ内で一度だけ定義 --}}
<script>
  if (!window.smartBack) {
    window.smartBack = function(defaultUrl) {
      const ref = document.referrer;
      try {
        if (!ref) { location.href = defaultUrl; return; }

        const u = new URL(ref);
        const sameOrigin = (u.origin === location.origin);
        const path = u.pathname;

        // 直前ページが「投稿の編集」か「新規作成」なら2つ戻る
        const isEditOrCreate =
          sameOrigin && (
            /^\/posts\/\d+\/edit$/.test(path) ||
            /^\/posts\/create$/.test(path)
          );

        if (isEditOrCreate) {
          if (history.length >= 2) {
            history.go(-2);
          } else {
            location.href = defaultUrl;
          }
        } else {
          history.back();
        }
      } catch (e) {
        location.href = defaultUrl;
      }
    };
  }
</script>
