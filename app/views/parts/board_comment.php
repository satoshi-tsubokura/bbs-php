<?php
use App\Models\Entities\CommentEntity;

?>
<li class="p-comment" id="comment-<?= h($comment->getCommentNo()) ?>">
<?php
  if ($comment->getStatus() === CommentEntity::ACTIVE) {
      ?>
  <div class="p-comment__meta">
    <span class="p-comment__no"><?= h($comment->getCommentNo()) ?>.</span>
    <span class="p-comment_user-name">名前: <?= h($comment->getUser()->getUserName()) ?>さん</span>
    <span class="p-comment__date"><?= h($comment->getUpdatedAt()->format("Y年m月d日 h:i:s")) ?></span>
  </div>
  <p class="p-comment__body"><?= toReplyLink(nl2br(h($comment->getCommentBody()))) ?></p>
  <div class="p-row-groups p-row-groups--right">
    <a href="#comment-form" class="c-link p-comment__link js-reply-link" data-no="<?= h($comment->getCommentNo()) ?>">返信</a>
    <?php
      // ログインユーザーのみ削除できるようにする
      if ($auth->isAuthenticatedUser($comment->getUserId())) {
          ?>
    <form method="POST" action="/comment/delete/<?= h($comment->getId()) ?>" class="js-delete-form">
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
          <form method="POST" action="/comment/delete/<?= h($comment->getId()) ?>">
            <button type="submit" class="c-btn c-btn--primary p-comment__btn">復元</button>
          </form>
          
    <?php
      }
      ?>
<?php
  }
?>
</li>