<?php

use App\Models\Entities\CommentEntity;

include __DIR__ . '/../templates/head.php' ?>
<body id="board-page">
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="c-section p-board">
      <!-- Todoコメント一覧 -->
      <div class="p-board__heading">
        <h2 class="c-section__ttl p-board__ttl"><?= h($board->getTitle()) ?></h2>
        <p class="p-board__description"><?= h(nl2br($board->getDescription())) ?></p>
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
          ?>
      <li class="p-comment">
      <?php
        if ($comment->getStatus() === CommentEntity::ACTIVE) {
            ?>
        <div class="p-comment__meta">
          <span class="p-comment__no"><?= h($comment->getCommentNo()) ?>.</span>
          <span class="p-comment_user-name">名前: <?= h($comment->getUser()->getUserName()) ?>さん</span>
          <span class="p-comment__date"><?= h($comment->getUpdatedAt()->format("Y年m月d日 h:i:s")) ?></span>
        </div>
        <p class="p-comment__body"><?= h(nl2br($comment->getCommentBody())) ?></p>
        <div class="p-row-groups p-row-groups--right">
          <a href="#comment-form" class="c-link p-comment__link">返信</a>
          <?php
            // ログインユーザーのみ削除できるようにする
            if ($auth->isAuthenticatedUser($comment->getUserId())) {
                ?>
          <form method="POST" action="/comment/delete/<?= $comment->getId() ?>" class="js-delete-form">
            <button type="submit" class="c-btn c-btn--danger p-comment__btn">削除</button>
          </form>
          <?php
            }
            ?>
        </div>
      <?php
        } else {
            ?>
        <p class="p-comment__body">このコメントは削除されました。</p>
              <div class="p-row-groups p-row-groups--right">
            <?php
            // ログインユーザーのみ復元できるようにする
            if ($auth->isAuthenticatedUser($comment->getUserId())) {
                ?>
                <form method="POST" action="/comment/delete/<?= $comment->getId() ?>">
                  <button type="submit" class="c-btn c-btn--danger p-comment__btn">復元</button>
                </form>
                
          <?php
            }
            ?>
      <?php
        }
          ?>
      </li>
    <?php
      }
?>  
    </ul>

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
        <input type="hidden" name="FILE_MAX_SIZE" value="2000000">
        <input type="hidden" name="token" value="<?= $csrfToken ?>">
        <button type="submit" class="c-btn c-btn--primary p-board-form__submit">投稿</button>
      </form>
    <!-- p-board-form --> 
    </section>
    <!-- c-section p-board -->
  </div>
</body>
</html>
