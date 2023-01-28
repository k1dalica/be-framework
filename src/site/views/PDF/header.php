<?php
  function formatDate ($d) {
    return  date_format(date_create($d),"F j, Y");
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title><?=$title?></title>
    <style>
      @font-face {
        font-family: 'LatoRegular';
        font-weight: 400;
        src: url('<?=__DIR__?>/assets/Lato-Regular.ttf') format('truetype');
      }
      <?php include(__DIR__."/assets/document.css") ?>
    </style>
  </head>
  <body>
    <div class="document">