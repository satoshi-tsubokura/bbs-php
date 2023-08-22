<?php

use App\Kernels\Auth\Authentication;

use function App\Utils\getAppConfig;

$auth = new Authentication();
?>
<header class="l-header">
  <div class="l-header__inner">
    <h1 class="l-header__ttl"><a href="/"><?= getAppConfig('appName') ?></a></h1>
    <div class="l-header__navs">
      <nav class="l-header__gnav">
        <ul class="p-row-groups">
          <li>
            <a href="/" class="l-header__link">
              掲示板一覧
            </a>
          </li>
          <li>
            <a href="/create/board" class="l-header__link">
              掲示板作成
            </a>
          </li>
        </ul>
      </nav>
      <nav class="l-header__hnav">
    <?php
      if($auth->isAuthenticated()) {
          ?>
      <form name="logout_form" action="/sign_out" method="POST">
        <a href="javascript:logout_form.submit()" class="c-btn c-btn--danger l-header__btn">ログアウト</a>
      </form>
    <?php
      } else {
          ?>
        <ul class="p-row-groups">
          <li>
            <a href="/sign_in" class="c-btn c-btn--primary l-header__btn">ログイン</a>
          </li>
          <li>
            <a href="/sign_up" class="c-btn l-header__btn">新規登録</a>
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