<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    html {
      font-size: 14px;
      font-family: Helvetica, Arial, sans-serif;
    }
  </style>
</head>
<body>
  <?php
    if($logo) echo "<img src='$logo'>";
  ?>

  <p>Dear <b><?=$item->firstName?>, <?=$item->lastName?></b>:</p>

  <p>Welcome to <b><?=$system->dbaName?></b>!</p>

  <p>To access the system and to complete setup, you will need to login using the information below. Upon logging in, you will be prompted to create your password.</p>

  Login URL: <a href="<?=htmlspecialchars(route("site_login"))?>"><?=htmlspecialchars(route("site_login"))?></a><br />
  Username: <b><?=$item->email?></b><br />
  Activation Code: <b><?=$item->activationCode?></b>

  <p>Click <a href="<?=htmlspecialchars(route("site_login"))?>">HERE</a> to login and setup your account</p>

  <p>If the link does not work, please copy and paste the login URL into your browser. You have a limited time to activate your account.</p>

  <p>At any time for immediate assistance, please feel free to contact our Support Team at <b><?=$system->supportEmail?></b> or <b><?=$system->supportPhone?></b> and they’ll be happy to assist.</p>

  <p>Get Plugged!</p>

  <p>Best Regards,</p>
  <br />
  <p>
  <?=$system->CompanyName?><br />
  <?=$system->phone?><br />
  <?=$system->email?><br />
  <?=$system->website?>
  </p>
</body>
</html>
