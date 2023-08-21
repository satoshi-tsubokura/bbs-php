<?php include __DIR__ . '/../templates/head.php' ?>
<body>
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="p-boards">
      <h1 class="p-boards__ttl">スレッド一覧</h1>
      <ul class="p-boards__list">
        <?php
          foreach($boards as $board) {
              ?>
        <li class="p-boards__item">
          <div class="c-card p-boards__card">
            <h3 class="p-boards__board-ttl"><?= $board->getTitle() ?></h3>
            <!-- TODO -->
            <!-- <span class="p-boards__updated">最終書き込み日: </span> -->
            <span class="p-boards__created">スレ立て日: <?= $board->getUpdatedAt ?></span>
          </div>
        </li>
        <?php
          }
?>
      </ul>
    </section>
  </div>
</body>
</html>
