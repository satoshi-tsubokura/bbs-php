<?php

use App\Utils\AuthUtil;

use function App\Utils\getAppConfig;

$auth = new AuthUtil();
?>
<header class="l-header">
  <div class="l-header__inner">
    <h1><a href=""><?= getAppConfig('appName') ?></a></h1>
    <div class="l-header__navs">
      <nav class="l-header__gnav">
        <a href="" class="l-header__link">
          掲示板一覧
        </a>
      </nav>
      <nav class="l-header__hnav">
    <?php
      if($auth->isAuthenticated()) {
          ?>
      <a href="" class="c-btn c-btn--danger l-header__btn">ログアウト</a>
    <?php
      } else {
          ?>
        <ul class="p-btn-groups">
          <li>
            <a href="" class="c-btn c-btn--primary l-header__btn">ログイン</a>
          </li>
          <li>
            <a href="" class="c-btn l-header__btn">新規登録</a>
          </li>
        </ul>
        <?php
      }
?>
      </nav>
    </div>
    </nav>
  </div>
</header>