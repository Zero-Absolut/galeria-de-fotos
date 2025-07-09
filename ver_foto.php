<?php

session_start();


if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $ver_foto = $_GET['id'];
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Visualizar Foto - Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/ver_foto.css" />
</head>

<body>
    <div class="background-animation"></div>

    <div class="viewer-container">
        <div class="viewer-content">
            <img src="<?php echo htmlspecialchars($ver_foto);   ?>" alt="Imagem em Visualização">
        </div>
        <a href="fotos.php" class="back-button">Voltar para a Galeria</a>

    </div>

</body>

</html>