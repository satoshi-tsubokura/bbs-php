<?php
$firstPage = 1;
?>
<ul class="c-pagination">
  <!-- prevボタン -->
  <?php
    if ($currentPage !== $firstPage) {
        ?>
<li class="c-pagination__item c-pagination__item--prev">
  <a href="/?page=<?= $currentPage - 1 ?>" class="c-pagination__link">前へ</a>
</li>
  <?php } ?>
  <?php
    for($page = $firstPage; $page <= $maxPage; $page++) {
        ?>
    <?php
          if($page === $currentPage) {
              ?>
  <li class="c-pagination__item c-pagination__item--current">
    <a class="c-pagination__link"><?= $page ?></a>
  </li>
    <?php } else { ?>
  <li class="c-pagination__item">
    <a href="/?page=<?= $page ?>" class="c-pagination__link"><?= $page ?></a>
  </li>
  <?php } ?>
  <?php
    }
?>
  <!-- nextボタン -->
    <?php
  if ($currentPage !== $maxPage) {
      ?>
  <li class="c-pagination__item c-pagination__item--next">
    <a href="/?page=<?= $currentPage + 1 ?>" class="c-pagination__link">次へ</a>
  </li>
    <?php } ?>
</ul>