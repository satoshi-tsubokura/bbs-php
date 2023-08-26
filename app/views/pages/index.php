<?php

include __DIR__ . '/../parts/head.php';
?>
<body>
  <?php include __DIR__ . '/../parts/header.php' ?>
  <div class="l-inner">
    <section class="c-section p-boards">
      <h1 class="c-section__ttl p-boards__ttl">スレッド一覧</h1>
      <div>
        <button ></button>
      </div>
      <ul class="c-card p-boards__list">
        <?php
         foreach($boards as $board) {
             ?>
        <li class="p-boards__item">
          <div class="c-card p-boards__card">
            <h3 class="p-boards__board-ttl">
              <a  href="/board/<?= h($board->getId()) ?>"><?= $board->getTitle() ?></a>
            </h3>
            <span class="p-boards__updated">最終書き込み日: <?= h($board->getUpdatedAt()->format('Y年m月d月 h:i:s')) ?></span>
            <span class="p-boards__created">作成日: <?= h($board->getCreatedAt()->format('Y年m月d月 h:i:s')) ?></span>
          </div>
        </li>
        <?php
         }
?>
      </ul>
      <!-- c-card p-boards__list -->
      <div class="p-boards__pagination">
        <?php include __DIR__ . '/../parts/pagination.php' ?>
      </div>
    </section>
    <!-- c-section p-boards -->
  </div>
</body>
</html>
