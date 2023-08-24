<?php include __DIR__ . '/../templates/head.php' ?>
<body>
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="c-section p-board-create">
      <h1 class="c-section__ttl p-board-create__ttl">スレッド新規作成</h1>
      <form action="/create/board" method="POST" class="p-board-create__form">
        <!-- エラーメッセージ(フォーム全体) -->
        <?php if(isset($errorMsgs['messages'])) { ?>
          <ul class="p-board-create__errors">
          <?php foreach($errorMsgs['messages'] as $msg) { ?>
            <li class="c-error-msg"><?= h($msg) ?></li>
          <?php
          }
            ?>
          </ul>
        <?php
        }
?>
        <dl class="p-board-create__fields">
          <dt class="p-board-create__label"><label for="board-title">タイトル</label></dt>
          <dd class="p-board-create__input">
            <input type="text" name="title" id="board-title" size="50" required maxlength="50" value="<?= $originValues['title'] ?? '' ?>">
             <?php if(isset($errorMsgs['title'])) { ?>
              <ul>
              <?php foreach($errorMsgs['title'] as $msg) { ?>
                <li class="c-error-msg"><?= h($msg) ?></li>
              <?php
              }
                 ?>
              </ul>
            <?php
             }
?>
          </dd>
          <dt class="p-board-create__label"><label for="board-description">詳細</label></dt>
          <dd class="p-board-create__input">
            <textarea name="description" id="board-description" cols="100" rows="10" maxlength="1000"><?= $originValues['description'] ?? '' ?></textarea>
            <?php if(isset($errorMsgs['description'])) { ?>
              <ul>
              <?php foreach($errorMsgs['description'] as $msg) { ?>
                <li class="c-error-msg"><?= h($msg) ?></li>
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
        <button type="submit" class="c-btn c-btn--primary p-board-create__submit">スレッド作成</button>
      </form>
    </section>
  </div>
</body>
</html>
