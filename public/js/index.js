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
  const deleteForms = document.getElementsByClassName("js-delete-form");
  [...deleteForms].forEach((form) => {
    form.addEventListener("submit", handleCommentDelete);
  });
}

/**
 *
 * @param {Event} ev
 */
function handleCommentDelete(ev) {
  ev.preventDefault();
  if (confirm("本当に削除しますか?")) {
    ev.target.submit();
    return true;
  }
  return false;
}
