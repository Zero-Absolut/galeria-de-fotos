<?php

include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');

session_start();

// Obtém o nome do arquivo atual para usar na lógica de "active link"
$current_page = basename($_SERVER['PHP_SELF']);

// Verificando se tem uma sessão ativa
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

    // Pegando os valores da sessão
    $id_upload = htmlspecialchars($_SESSION['id_logado'] ?? ''); // Adicionado ?? ''
    $nome_usuario = htmlspecialchars($_SESSION['nome_logado'] ?? ''); // Adicionado ?? ''
    $email_upload = htmlspecialchars($_SESSION['email_logado'] ?? ''); // Adicionado ?? ''

    // Inicializa a variável para erros de upload de arquivo
    $upload_erro_file = '';

    // Verificando se o método é POST (ou seja, foi feito o envio do formulário)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $titulo_imagem = $_POST['title'] ?? ''; // Adicionado ?? ''
        $descricao_imagem = $_POST['descricao'] ?? ''; // Adicionado ?? ''

        // Verificando se a imagem foi enviada e sem erros
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $conexao = conectarBancoDados();

            if ($conexao) { // Verifica se a conexão foi bem-sucedida
                $verificacao_upload = Upload($conexao, $_FILES['image'], $titulo_imagem, $descricao_imagem, $id_upload);

                if ($verificacao_upload['status']) {
                    $_SESSION['upload_status'] = [
                        'status' => true,
                        'mensagem' => $verificacao_upload['mensagem']
                    ];
                    header('location:feedback_upload.php');
                    exit();
                } else {
                    $_SESSION['upload_status'] = [
                        'status' => false,
                        'mensagem' => $verificacao_upload['erro']
                    ];
                }
                mysqli_close($conexao); // Fecha a conexão
            } else {
                $_SESSION['upload_status'] = [
                    'status' => false,
                    'mensagem' => 'Erro: Não foi possível conectar ao banco de dados para o upload.'
                ];
            }
        } else {
            // Se houve um erro no upload do arquivo (diferente de "nenhum arquivo selecionado")
            switch ($_FILES["image"]["error"] ?? UPLOAD_ERR_NO_FILE) { // Adicionado ?? UPLOAD_ERR_NO_FILE para caso $_FILES['image'] não esteja setado
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $_SESSION['upload_status'] = [
                        'status' => false,
                        'mensagem' => "Erro: O arquivo enviado excede o tamanho máximo permitido pelo servidor."
                    ];
                    break;
                case UPLOAD_ERR_NO_FILE:
                    // Isso pode ocorrer se o campo de arquivo estiver vazio no formulário
                    $_SESSION['upload_status'] = [
                        'status' => false,
                        'mensagem' => "Erro: Nenhum arquivo de imagem foi selecionado para upload."
                    ];
                    break;
                default:
                    $_SESSION['upload_status'] = [
                        'status' => false,
                        'mensagem' => "Erro desconhecido durante o upload do arquivo. Código: " . ($_FILES["image"]["error"] ?? 'N/A')
                    ];
                    break;
            }
            // Redireciona para feedback_upload.php mesmo com erro para exibir a mensagem
            header('location:feedback_upload.php');
            exit();
        }
    }
    // A parte do HTML deve estar fora do 'if ($_SERVER['REQUEST_METHOD'] == 'POST')' para que seja sempre exibida
?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Upload - Sua Galeria Fantástica</title>
        <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
        <link rel="stylesheet" href="css/index.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/upload.css">
    </head>

    <body>
        <div class="background-animation"></div>

        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="index.php"><img src="img_site/logo.png" alt="Sua Galeria Fantástica"></a>
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
                        $nome_usuario_exibir = htmlspecialchars($_SESSION['nome_logado'] ?? 'Visitante');
                    ?>
                        <span class="welcome-message">Olá, <?php echo $nome_usuario_exibir . " !    "; ?></span>
                        <a style=" border-color: #ffffff; color: white;" href="logout.php" class="button-logout">Sair</a>
                    <?php
                    } else {
                    ?>
                        <a href="login.php" class="button-login <?php echo ($current_page == 'login.php' ? 'current-page' : ''); ?>">Entrar</a>
                        <a href="cadastro.php" class="button-register <?php echo ($current_page == 'cadastro.php' ? 'current-page' : ''); ?>">Cadastrar</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </header>

        <main>
            <section class="upload-section">
                <div class="container">
                    <h2>Envie Suas Imagens</h2>
                    <p class="upload-intro">Compartilhe seus momentos conosco! Selecione uma imagem do seu dispositivo para adicionar à sua galeria.</p>

                    <div class="upload-form-container">
                        <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
                            <div class="form-group file-input-group">
                                <label for="imageUpload" class="upload-button">
                                    <span class="upload-icon">⬆️</span> Escolher Imagem
                                </label>
                                <input type="file" id="imageUpload" name="image" accept="image/*" required>
                                <span id="fileName" class="file-name">Nenhum arquivo selecionado</span>
                            </div>

                            <div class="form-group">
                                <label for="title">Título da Imagem:</label>
                                <input type="text" id="title" name="title" placeholder="Ex: Pôr do Sol na Praia" required maxlength="100">
                            </div>

                            <div class="form-group">
                                <label for="description">Descrição (opcional):</label>
                                <textarea id="description" name="descricao" rows="4" placeholder="Ex: Uma tarde inesquecível com cores vibrantes no céu." maxlength="500"></textarea>
                            </div>

                            <button type="submit" class="submit-upload-button">
                                <span class="submit-icon">✨</span> Fazer Upload
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="#">Sobre Nós</a>
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

        <script>
            // Script para mostrar o nome do arquivo selecionado
            document.addEventListener('DOMContentLoaded', function() {
                const imageUpload = document.getElementById('imageUpload');
                const fileNameSpan = document.getElementById('fileName');

                if (imageUpload && fileNameSpan) {
                    imageUpload.addEventListener('change', function() {
                        if (this.files && this.files.length > 0) {
                            fileNameSpan.textContent = this.files[0].name;
                        } else {
                            fileNameSpan.textContent = 'Nenhum arquivo selecionado';
                        }
                    });
                }

                // Script para o menu responsivo
                const menuToggleCheckbox = document.getElementById('menu-toggle-checkbox'); // Alterado para o ID correto
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