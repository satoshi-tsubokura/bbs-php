<?php
use function App\Kernels\Utils\getAppConfig;

include __DIR__ . '/../templates/head.php';

$defaultErrorMsg = '予期せぬエラーが発生しました。';
?>
<body>
  <header class="l-header">
    <div class="l-header__inner">
      <h1 class="l-header__ttl"><a href="/"><?= getAppConfig('appName') ?></a></h1>
  </header>
  <div class="l-inner">
    <section class="c-section p-error">
      <h1 class="c-section_ttl p-error__ttl"><?= $errorMsg ?? $defaultErrorMsg ?></h1>
      <p class="p-error__description"><?= $errorDescription ?? '' ?></p>
      <a href="/" class="c-link p-error__link">トップ画面に戻る</a>
    </section>
  </div>
</body>
</html>