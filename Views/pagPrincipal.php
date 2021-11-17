<div class="container was-validated">
    <div class="container" style="padding-top: 100px; padding-bottom: 50px;">
        <div class="text-center">
            <img src="Img/Colombia.png" class="rounded" style="width: 75px; height: 75px;" alt="">
        </div>
        <div class="col-12 text-center">
            <h2>Registro de ingreso de equipos</h2>
            <h5 style="color: rgb(79, 79, 79);">Registro de ingreso al CAD</h5>
        </div>
    </div>
    <div class="container" style="padding-top: 50px; padding-bottom: 150px;">
        <form class="row g-3 col-12" method="post">
            <?php
                $MVC = new controller();
                $MVC->buscar_usuario_controller();
            ?>
        </form>
    </div>
</div>