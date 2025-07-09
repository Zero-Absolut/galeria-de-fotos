<?php
//iniciando sessao
session_start();
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
// inicio termo busca vazio
$termo_busca = "";

// verificando se existe sessao ativa
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

    $id = $_SESSION['id_logado'];
    $conexao = conectarBancoDados();


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $termo_busca = trim($_POST['termo_busca'] ?? '');
    } else {

        $termo_busca = trim($_GET['termo_busca'] ?? "str");
    }
    $pesquisa = PesquisaFoto($conexao, $id, $termo_busca);
    mysqli_close($conexao);
}
?>


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="background-animation"></div>

    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="img_site/logo.png" alt="Sua Galeria Fantástica Logo"></a>
            </div>

            <input type="checkbox" id="menu-toggle-checkbox" class="menu-toggle-checkbox" aria-label="Abrir e fechar menu de navegação">
            <label for="menu-toggle-checkbox" class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <nav class="main-nav">
                <ul>
                    <?php
                    // Lógica para alterar os links de navegação baseado no estado de login
                    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                        // Links para usuário logado
                        echo '<li><a href="index.php" class="active">Inicio</a></li>';
                        echo '<li><a href="upload.php">Upload de Fotos</a></li>';
                        echo '<li><a href="fotos.php">Minha Galeria</a></li>';

                        echo '<li><a href="sobre.php">Sobre</a></li>';
                        echo '<li><a href="contato.php">Contato</a></li>';
                    } else {
                        // Links para usuário nao logado 
                        echo '<li><a href="index.php" class="active">Início</a></li>';
                        echo '<li><a href="sobre.php">Sobre</a></li>';
                        echo '<li><a href="contato.php">Contato</a></li>';
                    }
                    ?>
                </ul>
            </nav>
            <div class="user-actions">
                <?php

                // Verifica se a variável de sessão 'logado' existe e é verdadeira.
                // Isso indica que o usuário está autenticado.
                if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                    // Se o usuário está logado:
                    // Exibe uma mensagem de boas-vindas com o nome do usuário.

                    $nome_usuario = htmlspecialchars($_SESSION['nome_logado'] ?? 'Visitante');
                ?>
                    <span class="welcome-message">&nbsp;&nbsp;&nbsp; &nbsp;Olá, <?php echo $nome_usuario . " !    "; ?></span>
                    <a style=" border-color: #ffffff; color: white;" href="logout.php" class="button-logout">Sair</a>
                <?php
                } else {
                    // Se o usuário NÃO está logado:
                    // Exibe os botões de "Entrar" e "Cadastrar" normalmente.
                ?>
                    <a href="loguin.php" class="button-loguin">Entrar</a>
                    <a href="cadastro.php" class="button-register">Cadastrar</a>
                <?php
                }

                ?>
            </div>
        </div>
    </header>

    <main>
        <?php
        // Lógica para exibir conteúdo diferente baseado no estado de login
        if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

            // CONTEÚDO PARA USUÁRIO LOGADO 
            $nome_usuario_logado = htmlspecialchars($_SESSION['nome_logado'] ?? 'Usuário');
            // Nesta seção, você integraria a lógica para buscar e exibir as fotos do usuário do banco de dados.
            // Por enquanto, usamos placeholders para demonstrar a estrutura.
        ?>
            <section class="user-gallery-dashboard">
                <div class="container">
                    <h2>Bem-vindo, <?php echo $nome_usuario_logado; ?>! Sua Galeria Pessoal</h2>
                    <p>Organize, adicione e veja suas fotos exclusivas aqui.</p>

                    <div class="gallery-controls">
                        <div class="search-bar">
                            <form action="index.php" method="POST">
                                <input type="text" name="termo_busca" placeholder="Buscar em suas fotos..." class="search-input">
                                <button type="submit" class="search-button">Buscar</button>
                            </form>
                        </div>
                        <a href="upload.php" class="upload-new-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="upload-icon">
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"></path>
                                <polyline points="7 9 12 4 17 9"></polyline>
                                <line x1="12" y1="4" x2="12" y2="16"></line>
                            </svg>
                            <span>Adicionar Fotos</span>
                        </a>
                    </div>
                    <div class="gallery-grid user-photos-grid">
                        <?php

                        if ($pesquisa['status'] === true) {
                            foreach ($pesquisa['dados'] as $chave_pesquisa) {

                        ?>


                                <div class="gallery-item">
                                    <a href="#">
                                        <img src="<?php echo $chave_pesquisa['caminho_arquivo']    ?>" alt="">



                                        <h3><?php echo $chave_pesquisa['titulo'];   ?></h3>
                                    </a>
                                    <p><?php echo $chave_pesquisa['descricao'];   ?></p>
                                    <div class="item-actions">
                                        <a href="edita_foto.php?id=
                                       <?php echo htmlspecialchars($chave_pesquisa['id']); ?>
                                       &description=<?php echo urlencode($chave_pesquisa['descricao']); ?>&title=
                                       <?php echo urlencode($chave_pesquisa['titulo']); ?>&adress=
                                       <?php echo urlencode($chave_pesquisa['caminho_arquivo']); ?>" class="edit-button">Editar</a>
                                        <a href="excluir_foto.php?id=
                                            <?php echo trim($chave_pesquisa['id']); ?>"
                                            class="delete-button" onclick="return confirm('Tem certeza que deseja excluir esta imagem?')">Excluir</a>
                                    </div>
                                </div>





                            <?php
                            }
                        } else {
                            ?>

                            <?php print_r($pesquisa['erro']);  ?>



                        <?php
                        }
                        ?>
                    </div>
                </div>
            </section>

        <?php
        } else {
            // CONTEÚDO PARA USUÁRIO NÃO LOGADO 

        ?>
            <section class="hero-section hero-landing">
                <div class="container">
                    <h1>Sua Galeria Fantástica: Mantenha Suas Memórias Vivas e Privadas.</h1>
                    <p>Um espaço seguro e exclusivo para você guardar, organizar e reviver cada momento especial, longe dos olhos públicos.</p>
                    <div class="hero-buttons">
                        <a href="cadastro.php" class="button-register-large">Começar Agora - É Grátis!</a>
                        <a href="loguin.php" class="button-login-large">Já sou membro</a>
                    </div>
                </div>
            </section>

            <section class="features-section">
                <div class="container">
                    <h2>Por que escolher "Sua Galeria Fantástica"?</h2>
                    <div class="feature-grid">
                        <div class="feature-item">
                            <img src="img_site/trancar.png" alt="Ícone de Segurança" width="60" height="60">
                            <h3>Total Segurança e Privacidade</h3>
                            <p>Suas fotos são protegidas com criptografia e acessíveis apenas por você.</p>
                        </div>
                        <div class="feature-item">
                            <img src="img_site/linhas-de-calendario.png" alt="Ícone de Organização" width="60" height="60">
                            <h3>Organização Impecável</h3>
                            <p>Crie álbuns personalizados, adicione tags e encontre suas fotos rapidamente.</p>
                        </div>
                        <div class="feature-item">
                            <img src="img_site/filmadora.png" alt="Ícone de Acesso" width="60" height="60">
                            <h3>Acesso em Qualquer Lugar</h3>
                            <p>Visualize suas memórias de qualquer dispositivo, a qualquer momento, com segurança.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cta-section">
                <div class="container">
                    <h2>Pronto para proteger suas lembranças?</h2>
                    <a href="cadastro.php" class="button-register-cta">Criar Minha Galeria Agora!</a>
                </div>
            </section>
        <?php
        }
        ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="sobre.php">Sobre</a>
                    <a href="contato.php">Contato</a>
                    <a href="#">Privacidade</a>
                    <a href="#">Termos de Uso</a>
                </div>
                <div class="social-media">
                    <a href="https://github.com/Zero-Absolut" aria-label="github"><img src="img_site/github.png" alt="GitHub"></a>
                    <a href="http://linkedin.com/in/mateus-fbs" aria-label="linkedin"><img src="img_site/linkdin.png" alt="linkedin"></a>
                </div>
            </div>
            <p class="copyright">&copy; <?php echo htmlspecialchars(date('Y'));    ?> Sua Galeria Fantástica. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>