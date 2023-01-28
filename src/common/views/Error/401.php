<?php $this->setCode(404); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Oops! Unauthorized.</title>
    <style>
      <?php include(__DIR__."/errors.css") ?>
    </style>
  </head>
  <body>
    <div class="center">
      <h1>Oops!</h1>
      <h3>Looks like you are trying to access the page that you aren't authorized to see.</h3>
      <a href="/">
        <button>Go to homepage</button>
      </a>
    </div>
  </body>
</html>
