<?php include __DIR__ . '/../parts/head.php'; ?>
<body id="board-page">
  <?php include __DIR__ . '/../parts/header.php' ?>
  <div class="l-inner">
    <section class="c-section p-board">
      <div class="p-board__heading">
        <h2 class="c-section__ttl p-board__ttl"><?= h($board->getTitle()) ?></h2>
        <p class="p-board__description"><?= nl2br(h($board->getDescription())) ?></p>
      </div>
    <?php
     if(count($comments) === 0) {
         ?>
    <p>まだコメントはありません。</p>
    <?php
     }
?>
    <ul class="p-board__comments">
    <?php
      foreach($comments as $comment) {
          include __DIR__ . '/../parts/board_comment.php';
      }
?>
    </ul>
    <!-- p-board__comments -->
    <?php include __DIR__ . '/../parts/board_post_form.php'; ?>
    </section>
    <!-- c-section p-board -->
  </div>
</body>
</html>
