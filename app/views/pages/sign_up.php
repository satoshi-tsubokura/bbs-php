<?php include __DIR__ . '/../templates/head.php' ?>
<body>
  <?php include __DIR__ . '/../templates/header.php' ?>
  <div class="l-inner">
    <section class="c-card p-signup">
      <div class="p-signup__ttl">新規登録</div>
       <?php if(isset($errorMsgs['messages'])) { ?>
          <ul class="p-signup__errors">
          <?php foreach($errorMsgs['messages'] as $msg) { ?>
            <li class="c-error-msg"><?= $msg ?></li>
          <?php
          }
           ?>
          </ul>
        <?php
       }
?>
      <form method="POST" action="/sign_up" class="p-signup__form" novalidate>
        <dl class="p-signup__fields">
          <dt class="p-signup__label">
            <label for="input_name">ユーザー名</label>
          </dt>
          <dd class="p-signup__input">
            <input type="text" name="name" id="input_name" class="p-signup__item" size="25" required minlength="3" maxlength="20" value="<?= $parameters['name'] ?? '' ?>">
            <small class="p-signup__help">3~20文字</small>
            <?php if(isset($errorMsgs['name'])) { ?>
              <ul>
              <?php foreach($errorMsgs['name'] as $msg) { ?>
                <li class="c-error-msg"><?= $msg ?></li>
              <?php
              }
                ?>
              </ul>
            <?php
            }
?>
          </dd>
          <dt class="p-signup__label">
            <label for="input_email">メールアドレス</label>
          </dt>
          <dd class="p-signup__input">
            <input type="email" name="email" id="input_email" class="p-signup__item" size="50" required value="<?= $parameters['email'] ?? '' ?>">
            <?php if(isset($errorMsgs['email'])) { ?>
              <ul>
              <?php foreach($errorMsgs['email'] as $msg) { ?>
                <li class="c-error-msg"><?= $msg ?></li>
              <?php
              }
                ?>
              </ul>
            <?php
            }
?>
          </dd>
          <dt class="p-signup__label">
            <label for="input_password">パスワード</label>
          </dt>
          <dd class="p-signup__input">
            <input type="password" name="password" id="input_password" class="p-signup__item" size="50" required minlength="10" maxlength="72" value="<?= $parameters['password'] ?? '' ?>">
            <small class="p-signup__help">10文字以上 大文字、小文字、数字をそれぞれ1文字必須</small>
            <?php if(isset($errorMsgs['password'])) { ?>
              <ul>
              <?php foreach($errorMsgs['password'] as $msg) { ?>
                <li class="c-error-msg"><?= $msg ?></li>
              <?php
              }
                ?>
              </ul>
            <?php
            }
?>
          </dd>
        </dl>
        <button type="submit" class="c-btn c-btn--primary p-signup__submit">新規登録</button>
      </form>
    </section>
  </div>
  
  <?php include __DIR__ . '/../templates/footer.php' ?>
</body>
</html>
