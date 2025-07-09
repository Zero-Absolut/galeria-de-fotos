<?php
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');

// iniciando a sessao
session_start();
//verificando se existe sessao ativa
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    // verifica o tipo do metodo
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        // recupera os valores enviados pela url
        $id_foto = $_GET['id'];
        $id_editar = $_GET['id'];
        $id_user = $_SESSION['id_logado'];
        $caminho = $_GET['adress'];
        $descricao = $_GET['description'];
        $titulo = $_GET['title'];
    }
} else {
    //redireciona caso nao exista sessao 
    header('location:negado.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Foto - Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link rel="stylesheet" href="css/edita_foto.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet" />
</head>

<body class="page-login">
    <div class="background-animation"></div>

    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="img_site/logo.png" alt="Logo Galeria" /></a>
            </div>

            <input type="checkbox" id="menu-toggle-checkbox" class="menu-toggle-checkbox" />
            <label for="menu-toggle-checkbox" class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="sobre.php">Sobre</a></li>
                    <li><a href="contato.php">Contato</a></li>
                    <li><a href="ajuda.php">Ajuda</a></li>
                </ul>
            </nav>

            <div class="user-actions">
                <a href="loguin.php" class="button-login">Entrar</a>
                <a href="cadastro.php" class="button-register">Cadastre-se</a>
            </div>
        </div>
    </header>

    <main class="form-page-container">
        <section class="form-card">
            <h2>Editar Foto</h2>
            <form action="salva_edicao.php" method="post" class="main-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_foto); ?>">

                <div class="form-group">
                    <label>Imagem Atual:</label>
                    <img src="<?php echo htmlspecialchars($caminho); ?>" alt="Imagem atual" class="imagem-visualizacao" />
                </div>

                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Digite o novo título da foto" required value="<?php echo htmlspecialchars($titulo); ?>" />
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="4" placeholder="Digite a descrição da foto" required><?php echo htmlspecialchars($descricao); ?></textarea>
                </div>

                <button type="submit" class="submit-button">Salvar Alterações</button>
            </form>

            <p class="form-footer-link"><a href="galeria.php">← Voltar para a galeria</a></p>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="sobre.php">Sobre</a>
                    <a href="#">Contato</a>
                    <a href="#">Privacidade</a>
                    <a href="#">Termos de Uso</a>
                </div>
                <div class="social-media">
                    <a href="https://github.com/Zero-Absolut" aria-label="github"><img src="img_site/github.png" alt="GitHub"></a>
                    <a href="http://linkedin.com/in/mateus-fbs" aria-label="linkedin"><img src="img_site/linkdin.png" alt="linkedin"></a>
                </div>
            </div>
            <p class="copyright">&copy; <?php echo htmlspecialchars(date('Y')); ?> Sua Galeria Fantástica. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>

<?php
