Hi {{ $name }}, thank you for registering at <?= sitename; ?>.
<br><br>
Your password is {{ $password }}
<br><br>
<A HREF="https://<?= callurl ?>">Click here to start ordering</A>
<br>
@if($requiresauth)
    <A HREF="<?= webroot('auth/login', true) . '?action=verify&code=' . $authcode; ?>">Click here to verify your email</A>
@endif

<?= view("email_test"); ?>