"use strict";

/*****************************************************************************
 * 初期設定
 *****************************************************************************/
window.addEventListener("load", () => {
  const bodyId = document.querySelector("body").getAttribute("id");

  switch (bodyId) {
    case "board-page":
      boardPageInit();
      break;
    default:
      break;
  }
});

/*****************************************************************************
 * /board/{board_id} ページ
 *****************************************************************************/
function boardPageInit() {
  // 削除ボタンのイベントハンドラ登録
  const deleteForms = document.getElementsByClassName("js-delete-form");
  [...deleteForms].forEach((form) => {
    form.addEventListener("submit", handleCommentDelete);
  });

  // 返信リンクのイベントハンドラ登録
  const replyLinks = document.getElementsByClassName("js-reply-link");
  [...replyLinks].forEach((link) => {
    link.addEventListener("click", handlePrepareReply);
  });
}

/**
 * @param {SubmitEvent} ev
 */
function handleCommentDelete(ev) {
  console.log(ev);
  ev.preventDefault();
  if (confirm("本当に削除しますか?")) {
    ev.target.submit();
    return true;
  }
  return false;
}

/**
 *
 * @param {Event} ev
 */
function handlePrepareReply(ev) {
  ev.preventDefault();
  const commentNo = ev.currentTarget.dataset.no;
  const commentTextArea = document.getElementById("comment-body");

  // 返信テキストをセットする
  const replyText = `>>${commentNo}`;
  commentTextArea.innerText = replyText;

  commentTextArea.focus();
  commentTextArea.setSelectionRange(replyText.length, replyText.length);
}
