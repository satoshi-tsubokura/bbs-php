<form method="POST" action="/board/<?= $boardId ?>" enctype="multipart/form-data" id="comment-form">
<!-- エラーメッセージ(フォーム全体) -->
<?php if(isset($errorMsgs['messages'])) { ?>
  <ul class="p-board-form__errors">
  <?php foreach($errorMsgs['messages'] as $msg) { ?>
    <li class="c-error-msg"><?= h($msg) ?></li>
  <?php
  }
    ?>
  </ul>
<?php
}
?>
  <dl class="p-board-form__fields">
    <dt class="p-board-form__label"><label for="comment-body">コメント</label></dt>
    <dd class="p-board-form__input">
      <textarea type="text" name="comment" id="comment-body" cols="100" rows="10" required maxlength="1000"><?= $originValues['comment'] ?? '' ?></textarea>
      <?php if(isset($errorMsgs['comment'])) { ?>
        <ul>
        <?php  foreach($errorMsgs['comment'] as $msg) { ?>
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
  <button type="submit" class="c-btn c-btn--primary p-board-form__submit">投稿</button>
</form>