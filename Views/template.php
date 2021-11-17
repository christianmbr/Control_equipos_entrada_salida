<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "Views/head.php";?>
</head>
<body class="container-fluid" style="background-color: rgb(245, 245, 245);">
    <?php 
        $paginas = new controller();
        $paginas -> controladorPagina();
    ?>
    <?php include "Views/footer.php"?>
    <?php include "Views/scripts.php"?>
</body>
</html>