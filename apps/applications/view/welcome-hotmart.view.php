<h2><b><?php echo $names; ?></b> Bienvenido a bordo</h2>

<h3>Estamos muy felices de que seas parte de DummieTrading.</h3>

<?php if(isset($sendPassword)) { ?>
    <h3>
        Como dato adicional te queremos recordar que la contraseña que elegiste es <strong><?php echo $password;?></strong>.
    </h3>
<?php } ?>

<h3>
    <a href="<?php echo $tokenUrl;?>">ingresa a tu cuenta aquí</a>
</h3>

<h3>
    <a href="https://zuum.link/BienvenidoDummieTrading">Abre nuestra guía de ingreso</a>
</h3>

<h3>
    <b>
        Gracias de parte del equipo de IAM
    </b>
</h3>