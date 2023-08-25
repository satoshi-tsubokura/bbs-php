<?php include __DIR__ . '/../templates/head.php' ?>
<body>
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="c-section c-card p-signinup">
      <div class="p-signinup__ttl">ログイン</div>
       <?php if(isset($errorMsgs['messages'])) { ?>
          <ul class="p-signinup__errors">
          <?php foreach($errorMsgs['messages'] as $msg) { ?>
            <li class="c-error-msg"><?= h($msg) ?></li>
          <?php
          }
           ?>
          </ul>
        <?php
       }
?>
      <form method="POST" action="/sign_in" class="p-signinup__form">
        <dl class="p-signinup__fields">
          <dt class="p-signinup__label">
            <label for="input_name">ユーザー名</label>
          </dt>
          <dd class="p-signinup__input">
            <input type="text" name="name" id="input_name" class="p-signinup__item" size="25" required minlength="3" maxlength="20" value="<?= $parameters['name'] ?? '' ?>">
            <?php if(isset($errorMsgs['name'])) { ?>
              <ul>
              <?php foreach($errorMsgs['name'] as $msg) { ?>
                <li class="c-error-msg"><?= h($msg) ?></li>
              <?php
              }
                ?>
              </ul>
            <?php
            }
?>
          </dd>
          <dt class="p-signinup__label">
            <label for="input_password">パスワード</label>
          </dt>
          <dd class="p-signinup__input">
            <input type="password" name="password" id="input_password" class="p-signinup__item" size="30" required minlength="10" maxlength="72" value="<?= $parameters['password'] ?? '' ?>">
            <?php if(isset($errorMsgs['password'])) { ?>
              <ul>
              <?php foreach($errorMsgs['password'] as $msg) { ?>
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
        <button type="submit" class="c-btn c-btn--primary p-signinup__submit">ログイン</button>
      </form>
      <p class="c-link p-signinup__link"><a href="/sign_up">新規登録がまだの方はこちら</a></p>
    </section>
  </div>
</body>
</html>
