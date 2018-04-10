Hi {{ $name }}, thank you for registering at <?= sitename; ?>.
<br><br>
Your password is {{ $password }}
<br><br>
Happy Ordering!
<br>
@if($requiresauth)
    <A HREF="<?= webroot('auth/login', true) . '?action=verify&code=' . $authcode; ?>">Click here to verify your email</A>
@endif

<?= view("email_test"); ?>