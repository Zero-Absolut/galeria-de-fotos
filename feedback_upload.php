<?php
// abre a sessao e verifica se existe alguma ativa
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado']) {


    // captura o nome do usuario logado
    $nome_usuario = $_SESSION['nome_logado'];

?>

    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status do Upload - Sua Galeria Fantástica</title>
        <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
        <link rel="stylesheet" href="css/index.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            /* Estilos específicos para a página de feedback */
            .feedback-section {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: calc(100vh - 150px);
                /* Ajuste conforme altura do header/footer */
                text-align: center;
                padding: 40px 20px;
                background-color: var(--color-background-dark);
                /* Usando variável do seu CSS, se existir */
                color: var(--color-light);
                /* Cor do texto, se existir */
            }

            .feedback-section h1 {
                font-family: 'Playfair Display', serif;
                font-size: 3em;
                margin-bottom: 20px;
                color: var(--color-primary);
                /* Ex: #FFD700 ou #6A5ACD, dependendo da sua paleta */
            }

            .feedback-section p {
                font-family: 'Open Sans', sans-serif;
                font-size: 1.2em;
                max-width: 600px;
                margin-bottom: 30px;
                line-height: 1.6;
            }

            .feedback-section .button-group a {
                display: inline-block;
                padding: 12px 25px;
                margin: 0 10px;
                border-radius: 5px;
                text-decoration: none;
                font-family: 'Open Sans', sans-serif;
                font-weight: 600;
                transition: background-color 0.3s ease, transform 0.2s ease;
                white-space: nowrap;
                /* Garante que os botões não quebrem a linha */
            }

            .feedback-section .button-group .button-home {
                background-color: var(--color-accent);
                /* Usar uma cor de destaque do seu CSS */
                color: var(--color-dark);
                /* Cor do texto contrastante */
                border: 2px solid var(--color-accent);
            }

            .feedback-section .button-group .button-home:hover {
                background-color: var(--color-accent-dark);
                /* Uma versão mais escura do destaque */
                transform: translateY(-2px);
            }

            .feedback-section .button-group .button-upload-again {
                background-color: transparent;
                color: var(--color-primary);
                /* Ou outra cor que se destaque no fundo escuro */
                border: 2px solid var(--color-primary);
            }

            .feedback-section .button-group .button-upload-again:hover {
                background-color: var(--color-primary);
                color: var(--color-background-dark);
                transform: translateY(-2px);
            }

            /* Cores de exemplo (ajuste para as variáveis do seu `index.css`) */
            :root {
                --color-primary: #6A5ACD;
                /* Roxo principal */
                --color-secondary: #FFD700;
                /* Amarelo dourado */
                --color-background-dark: #222;
                /* Fundo escuro */
                --color-background-light: #f4f4f4;
                /* Fundo claro */
                --color-light: #ffffff;
                /* Texto claro */
                --color-dark: #333333;
                /* Texto escuro */
                --color-accent: #6A5ACD;
                /* Cor de destaque para botões */
                --color-accent-dark: #5A4CAD;
                /* Versão mais escura da cor de destaque */
            }

            /* Mensagens de status (exemplos visuais, idealmente controladas por PHP) */
            .feedback-section.success .message {
                color: var(--color-secondary);
            }

            .feedback-section.error .message {
                color: #e74c3c;
            }

            /* Vermelho para erro */
        </style>
    </head>

    <body>
        <div class="background-animation"></div>

        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="index.php"><img src="img_site/logo.png" alt="Logo Sua Galeria Fantástica"></a>
                </div>

                <input type="checkbox" id="menu-toggle-checkbox" class="menu-toggle-checkbox" aria-label="Abrir e fechar menu de navegação">
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
                        <li><a href="ajuda.php">Sobre</a></li>

                    </ul>
                </nav>
                <div class="user-actions">
                    <?php
                    // Lógica PHP para alternar o conteúdo do cabeçalho
                    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                        $nome_usuario = htmlspecialchars($_SESSION['nome_logado'] ?? 'Visitante');
                    ?>
                        <span class="welcome-message">Olá, <?php echo htmlspecialchars($nome_usuario) . " ! "; ?></span>
                        <a style="text-decoration: none; color: white;" href="logout.php" class="button-logout">Sair</a>
                    <?php
                    } else {
                    ?>
                        <a href="loguin.php" class="button-login">Entrar</a>
                        <a href="cadastro.php" class="button-register">Cadastrar</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </header>

        <main>
            <section class="feedback-section">
                <div class="container">
                    <h1 class="message">Upload Concluído!</h1>
                    <p>Sua imagem foi enviada com sucesso para a galeria.</p>
                    <?php
                    if (isset($_SESSION['upload_status'])) {
                        if ($_SESSION['upload_status']['status']) {
                            // Mensagem de sucesso
                            echo '<div class="message success-message">';
                            echo $_SESSION['upload_status']['mensagem'];
                            echo '</div>';
                        } else {
                            // Mensagem de erro
                            echo '<div class="message error-message">';
                            echo $_SESSION['upload_status']['mensagem'];
                            echo '</div>';
                        }
                        // Limpar a mensagem da sessão após exibir
                        unset($_SESSION['upload_status']);
                    }
                    ?>





                    <div class="button-group">
                        <a href="index.php" class="button-home">Voltar para o Início</a>
                        <a href="upload.php" class="button-upload-again">Fazer Outro Upload</a>
                    </div>
                </div>
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
                <p class="copyright">&copy; <?php echo htmlspecialchars(date('Y'));   ?> Sua Galeria Fantástica. Todos os direitos reservados.</p>
            </div>
        </footer>
    </body>

    </html>

    //fim html
<?php
} else {
    header('location:negado.php');
}
?>