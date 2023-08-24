<?php include __DIR__ . '/../templates/head.php' ?>
<body>
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="c-section p-board">
      <!-- Todoコメント一覧 -->

      <form method="POST" action="/board/<?= $boardId ?>" enctype="multipart/form-data">
        <!-- エラーメッセージ(フォーム全体) -->
        <?php if(isset($errorMsgs['messages'])) { ?>
          <ul class="p-board__errors">
          <?php foreach($errorMsgs['messages'] as $msg) { ?>
            <li class="c-error-msg"><?= $msg ?></li>
          <?php
          }
            ?>
          </ul>
        <?php
        }
?>
        <dl class="p-board__fields">
          <dt class="p-board__label"><label for="comment-body">コメント</label></dt>
          <dd class="p-board__input">
            <textarea type="text" name="comment" id="comment-body" cols="100" rows="10" required maxlength="1000" value="<?= $originValues['comment'] ?? '' ?>"></textarea>
             <?php if(isset($errorMsgs['comment'])) { ?>
              <ul>
              <?php foreach($errorMsgs['comment'] as $msg) { ?>
                <li class="c-error-msg"><?= $msg ?></li>
              <?php
              }
                 ?>
              </ul>
            <?php
             }
?>
          </dd>
          
        </dl>
        <input type="hidden" name="token" value="<?= $csrfToken ?>">
        <button type="submit" class="c-btn c-btn--primary p-board__submit">投稿</button>
      </form>
    </section>
    <!-- c-section p-boards -->
  </div>
</body>
</html>
