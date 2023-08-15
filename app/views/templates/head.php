<?php
require_once __DIR__ . '/../../utils/getAppConfig.php';
use function App\Utils\getAppConfig;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= getAppConfig('appName') ?></title>
  <!-- stylesheet -->
  <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="css/index.css">
  <!-- script -->
  <!-- <script src="js/bootstrap.min.js"></script> -->
</head>