<h3>Recuperar contraseña</h3>

<p>
    Hemos recibido una petición para recuperar contraseña de <b><?php echo $email; ?></b>
</p>

<p>
    Para continuar por favor da clic en el siguiente enlace
</p>
<p>
    <a href="<?php echo HCStudio\Connection::getMainPath(); ?>/apps/login/newPassword?token=<?php echo $token;?>">Recuperar contraseña</a>
</p>

<p>
    <small>
        Si no has hecho la petición de recuperación de contraseña has caso omiso a éste mensaje
    </small>
</p>