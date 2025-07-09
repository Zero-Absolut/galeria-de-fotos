<?php

include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
session_start();

// Obtém o nome do arquivo atual para usar na lógica de "active link"
$current_page = basename($_SERVER['PHP_SELF']);

// Verificando sessão ativa
if (isset($_SESSION['id_logado']) && $_SESSION['logado'] == true) {

    // Inicializa a variável para as fotos do usuário
    $fotos_do_usuario = [];
    $mensagem_galeria = ''; // Para exibir mensagens de sucesso/erro da busca

    // Verifica se o método é GET (requisição normal da página)
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $id = htmlspecialchars($_SESSION['id_logado'] ?? '');
        $nome = htmlspecialchars($_SESSION['nome_logado'] ?? '');

        $conexao = conectarBancoDados();

        if ($conexao) {
            // A função BuscaFoto deve retornar um array com 'status', 'dados' (array de fotos) e 'erro'
            $resultado_busca_fotos = BuscaFoto($conexao, $id);

            if ($resultado_busca_fotos['status'] && !empty($resultado_busca_fotos['dados'])) {
                $fotos_do_usuario = $resultado_busca_fotos['dados'];
            } else {
                // Se houver um erro ou nenhuma foto for encontrada, exibe a mensagem apropriada
                $mensagem_galeria = $resultado_busca_fotos['erro'] ?? 'Nenhuma foto encontrada na sua galeria.';
            }
            // Fechando conexão com o banco de dados
            mysqli_close($conexao);
        } else {
            $mensagem_galeria = 'Erro ao conectar ao banco de dados para buscar suas fotos.';
        }
    }

    // O HTML deve ser exibido após o processamento PHP inicial
?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Sua Galeria - Sua Galeria Fantástica</title>
        <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="css/index.css" />
        <link rel="stylesheet" href="css/fotos.css" />
    </head>

    <body>
        <div class="background-animation"></div>

        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="index.php"><img src="img_site/logo.png" alt="Logo da Sua Galeria Fantástica" /></a>
                </div>
                <input type="checkbox" id="menu-toggle-checkbox" class="menu-toggle-checkbox" aria-label="Abrir e fechar menu de navegação">
                <label for="menu-toggle-checkbox" class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>

                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php" class="<?php echo ($current_page == 'index.php' ? 'current-page' : ''); ?>">Início</a></li>
                        <li><a href="upload.php" class="<?php echo ($current_page == 'upload.php' ? 'current-page' : ''); ?>">Upload de Fotos</a></li>
                        <li><a href="fotos.php" class="<?php echo ($current_page == 'fotos.php' ? 'current-page' : ''); ?>">Minha Galeria</a></li>

                        <li><a href="sobre.php" class="<?php echo ($current_page == 'sobre.php' ? 'current-page' : ''); ?>">Sobre</a></li>
                        <li><a href="contato.php" class="<?php echo ($current_page == 'contato.php' ? 'current-page' : ''); ?>">Contato</a></li>
                    </ul>
                </nav>
                <div class="user-actions">
                    <?php
                    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                        $nome_usuario = htmlspecialchars($_SESSION['nome_logado'] ?? 'Visitante');
                    ?>
                        <span class="welcome-message">&nbsp;&nbsp;&nbsp; &nbsp;Olá, <?php echo $nome_usuario . " !    "; ?></span>
                        <a style=" border-color: #ffffff; color: white;" href="logout.php" class="button-logout">Sair</a>
                    <?php
                    } else {
                        // Estes links não devem ser exibidos se o usuário está logado
                        // mas manter a lógica aqui por consistência, embora negado.php deva lidar com isso
                    ?>
                        <a href="login.php" class="button-login <?php echo ($current_page == 'login.php' ? 'current-page' : ''); ?>">Entrar</a>
                        <a href="cadastro.php" class="button-register <?php echo ($current_page == 'cadastro.php' ? 'current-page' : ''); ?>">Cadastrar</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </header>

        <main class="gallery-page-container">
            <div class="container">
                <section class="gallery-section">
                    <h1>Minha Galeria de Fotos</h1>
                    <p class="gallery-description">
                        Aqui estão todas as suas fotos. Clique em uma imagem para vê-la em tamanho maior ou para gerenciá-la.
                    </p>

                    <div class="gallery-grid">
                        <?php
                        // Verifica se há fotos para exibir
                        if (!empty($fotos_do_usuario)) {
                            foreach ($fotos_do_usuario as $foto) {
                        ?>
                                <div class="gallery-item">
                                    <img src="<?php echo htmlspecialchars($foto['caminho_arquivo']); ?>" alt="<?php echo htmlspecialchars($foto['titulo'] ?? 'Imagem da Galeria'); ?>" />
                                    <div class="image-overlay">
                                        <h3><?php echo htmlspecialchars($foto['titulo'] ?? 'Sem Título'); ?></h3>
                                        <p><?php echo htmlspecialchars($foto['descricao'] ?? 'Sem descrição.'); ?></p>
                                        <a href="ver_foto.php?id=<?php echo htmlspecialchars($foto['caminho_arquivo']); ?>" class="view-button">Ver/Gerenciar</a>
                                    </div>
                                </div>
                            <?php
                            }
                        } else {
                            // Exibe a mensagem se não houver fotos
                            ?>
                            <div class="no-photos-message">
                                <p><?php echo $mensagem_galeria; ?></p>
                                <p>Que tal <a href="upload.php">enviar sua primeira foto</a>?</p>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                    <div class="pagination">
                        <a href="#" class="active">1</a>
                        <a href="#">2</a>
                        <a href="#">3</a>
                        <a href="#">&raquo;</a>
                    </div>
                </section>
            </div>
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
                <p class="copyright">
                    &copy;
                    <?php echo htmlspecialchars(date('Y')); ?>
                    Sua Galeria Fantástica. Todos os direitos reservados.
                </p>
            </div>
        </footer>

        <script>
            // Script para o menu responsivo
            document.addEventListener('DOMContentLoaded', function() {
                const menuToggleCheckbox = document.getElementById('menu-toggle-checkbox');
                const mainNav = document.querySelector('.main-nav');

                if (menuToggleCheckbox && mainNav) {
                    menuToggleCheckbox.addEventListener('change', function() {
                        if (this.checked) {
                            mainNav.style.maxHeight = mainNav.scrollHeight + "px";
                        } else {
                            mainNav.style.maxHeight = "0";
                        }
                    });

                    document.addEventListener('click', function(event) {
                        if (!mainNav.contains(event.target) && !menuToggleCheckbox.contains(event.target) && menuToggleCheckbox.checked) {
                            menuToggleCheckbox.checked = false;
                            mainNav.style.maxHeight = "0";
                        }
                    });
                }
            });
        </script>
    </body>

    </html>
<?php
} else {
    // Caso não haja uma sessão ativa, redireciona para a página de acesso negado
    header('location:negado.php');
    exit();
}
?>