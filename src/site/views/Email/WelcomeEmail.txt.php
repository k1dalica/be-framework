Dear <?=$item->firstName?>, <?=$item->lastName?>:

Welcome to <?=$system->dbaName?>!

To access the system and to complete setup, you will need to login using the information below. Upon logging in, you will be prompted to create your password.

Login URL: <?=htmlspecialchars(route("site_login"))?>\n
Username: <?=$item->email?>\n
Activation Code: <?=$item->activationCode?>

Open this link <?=htmlspecialchars(route("site_login"))?> to login and setup your account

If the link does not work, please copy and paste the login URL into your browser. You have a limited time to activate your account.

At any time for immediate assistance, please feel free to contact our Support Team at <?=$system->supportEmail?> or <?=$system->supportPhone?> and they’ll be happy to assist.

Get Plugged!

Best Regards,
\n

<?=$system->CompanyName?>\n
<?=$system->phone?>\n
<?=$system->email?>\n
<?=$system->website?>
