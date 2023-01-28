<?php $this->setCode(404); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>404 Not Found</title>
    <style>
      <?php include(__DIR__."/errors.css") ?>
    </style>
  </head>
  <body>
    <div class="center">
      <h1>404</h1>
      <h3>Oops! We can't seem to find the page you're looking for.</h3>
      <a href="/">
        <button>Go to homepage</button>
      </a>
    </div>
  </body>
</html>
