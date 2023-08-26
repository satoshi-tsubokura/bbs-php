<?php
use function App\Kernels\Utils\getAppConfig;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= getAppConfig('appName') ?></title>
  <link rel="shortcut icon" href="data:," type="image/x-icon">
  <!-- stylesheet -->
  <link rel="stylesheet" href="/css/index.css">
  <!-- script -->
  <script src="/js/index.js"></script>
</head>