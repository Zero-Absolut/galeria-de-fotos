<?php

session_start();

// --- INCLUSÃO DAS CLASSES PHPMailer ---
// Certifique-se de que os caminhos abaixo estão corretos
// Eles devem apontar para onde você extraiu a pasta 'src' do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
// --- FIM DA INCLUSÃO DAS CLASSES PHPMailer ---

// Obtém o nome do arquivo atual para usar na lógica de "active link"
$current_page = basename($_SERVER['PHP_SELF']);

// Inicia as variáveis de mensagem de status fora do bloco POST para evitar "Undefined variable" se o formulário não for submetido
// Estes valores serão preenchidos caso haja uma mensagem na sessão
$mensagem_status_tipo = '';
$mensagem_status_texto = '';

// Verifica se há uma mensagem de status na sessão para exibir
if (isset($_SESSION['mensagem_status'])) {
    $mensagem_status_tipo = $_SESSION['mensagem_status']['tipo'];
    $mensagem_status_texto = $_SESSION['mensagem_status']['texto'];
    // Limpa a mensagem da sessão para que não apareça novamente após o refresh
    unset($_SESSION['mensagem_status']);
}

// Verifica se o usuário está logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Captura e sanitiza os dados do formulário
        $nome = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $assunto = htmlspecialchars(trim($_POST['subject']));
        $mensagem = htmlspecialchars(trim($_POST['message']));

        // --- INÍCIO DA LÓGICA DE ENVIO DE E-MAIL COM PHPMailer ---
        $mail = new PHPMailer(true); // 'true' habilita exceções para tratamento de erros detalhados

        try {
            // Configurações do Servidor SMTP (Gmail)
            $mail->isSMTP();                                            // Usar SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Servidor SMTP do Gmail
            $mail->SMTPAuth   = true;                                   // Habilitar autenticação SMTP
            $mail->Username   = 'emailparaenviophp@gmail.com';            // <-- SEU E-MAIL DO GMAIL AQUI
            $mail->Password   = 'mxps xmfh uzbp sxdl';              // <-- SUA SENHA DE APLICATIVO DO GOOGLE AQUI
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Habilitar criptografia TLS
            $mail->Port       = 587;                                    // Porta TLS para Gmail

            // Configurações para caracteres especiais (UTF-8)
            $mail->CharSet = 'UTF-8';

            // Remetente (quem está enviando o e-mail)
            // IMPORTANTE: Para o Gmail, o e-mail setFrom DEVE ser o mesmo do Username
            $mail->setFrom('emailparaenviophp@gmail.com', $nome);
            $mail->addReplyTo($email, $nome); // E-mail de resposta (do formulário)

            // Destinatário (para onde o e-mail será enviado)
            $mail->addAddress('mateusbrito@outlook.com', 'Mateus Brito'); // <-- E-MAIL DE DESTINO AQUI

            // Conteúdo do E-mail
            $mail->isHTML(true);                                  // Definir formato do e-mail como HTML
            $mail->Subject = 'Contato pelo Site: ' . $assunto;
            $mail->Body    = "
                <html>
                <head>
                    <title>Nova Mensagem de Contato</title>
                </head>
                <body>
                    <h2>Detalhes da Mensagem:</h2>
                    <p><strong>Nome:</strong> {$nome}</p>
                    <p><strong>E-mail:</strong> {$email}</p>
                    <p><strong>Assunto:</strong> {$assunto}</p>
                    <p><strong>Mensagem:</strong><br>" . nl2br($mensagem) . "</p>
                </body>
                </html>
            ";
            $mail->AltBody = "Nova Mensagem de Contato:\n\nNome: {$nome}\nE-mail: {$email}\nAssunto: {$assunto}\nMensagem:\n{$mensagem}"; // Conteúdo para clientes de e-mail que não suportam HTML

            $mail->send();
            $_SESSION['mensagem_status'] = [
                'tipo' => 'sucesso',
                'texto' => 'Mensagem enviada com sucesso! Em breve entrarei em contato.'
            ];
        } catch (Exception $e) {
            // Captura o erro detalhado do PHPMailer
            $_SESSION['mensagem_status'] = [
                'tipo' => 'erro',
                'texto' => "Ops! Não foi possível enviar sua mensagem. Erro: {$mail->ErrorInfo}"
            ];
            // Para depuração, você pode ativar: error_log("PHPMailer Erro: " . $e->getMessage());
        }
        // --- FIM DA LÓGICA DE ENVIO DE E-MAIL COM PHPMailer ---

        // Redireciona para a própria página de contato para evitar reenvio do formulário ao recarregar
        header('Location: contato.php');
        exit(); // Garante que o script pare após o redirecionamento
    }
} else {
    // Se não estiver logado, redireciona para a página de acesso negado
    header('location:negado.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/contato.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilo básico para a caixa de mensagem */
        #statusMessage {
            padding: 10px 20px;
            margin: 20px auto;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            max-width: 600px;
            display: none;
            /* Inicia oculto */
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        /* Cores para sucesso e erro */
        #statusMessage.sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        #statusMessage.erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #statusMessage.show {
            opacity: 1;
        }
    </style>
</head>

<body class="page-contato">
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
                    <li><a href="index.php" class="<?php echo ($current_page == 'index.php' ? 'current-page' : ''); ?>">Início</a></li>
                    <?php
                    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                    ?>
                        <li><a href="upload.php" class="<?php echo ($current_page == 'upload.php' ? 'current-page' : ''); ?>">Upload de Fotos</a></li>
                        <li><a href="fotos.php" class="<?php echo ($current_page == 'fotos.php' ? 'current-page' : ''); ?>">Minha Galeria</a></li>
                    <?php
                    }
                    ?>
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
                    <a style="border-color: #ffffff; color: white;" href="logout.php" class="button-logout">Sair</a>
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

    <main class="page-content-container">
        <section class="contact-card">
            <h2>Entre em Contato</h2>
            <div class="contact-intro">
                <p>Tem alguma pergunta, feedback, ou gostaria de discutir uma oportunidade de projeto? Minha jornada de 12 anos na infraestrutura me deu uma base sólida, mas é no desenvolvimento que minha paixão se acende. Se você tem um projeto em mente, uma vaga para desenvolvedor, ou simplesmente quer trocar ideias sobre como a tecnologia pode transformar, sinta-se à vontade para usar o formulário abaixo ou me contatar diretamente. Adoraria ouvir de você e explorar como minhas habilidades podem agregar valor!</p>
            </div>

            <div id="statusMessage"></div>
            <form action="contato.php" method="POST" class="main-form">
                <div class="form-group">
                    <label for="name">Seu Nome:</label>
                    <input type="text" name="name" id="name" required placeholder="Seu nome completo" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Seu E-mail:</label>
                    <input type="email" name="email" id="email" required placeholder="seu.email@exemplo.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="subject">Assunto:</label>
                    <input type="text" name="subject" id="subject" placeholder="Assunto da mensagem" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="message">Sua Mensagem:</label>
                    <textarea name="message" id="message" rows="6" required placeholder="Escreva sua mensagem aqui..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="submit-button">Enviar Mensagem</button>
            </form>

            <div class="contact-info">
                <h3>Ou Me Encontre em:</h3>
                <p><strong>E-mail:</strong> <a href="mailto:seu.email@exemplo.com">mateusbrito@outlook.com</a></p>
                <div class="social-contact-links">
                    <a href="http://linkedin.com/in/mateus-fbs" target="_blank" aria-label="LinkedIn"><img src="img_site/linkedin2.png" alt="LinkedIn"></a>
                    <a href="https://github.com/Zero-Absolut" target="_blank" aria-label="GitHub"><img src="img_site/github.png" alt="GitHub"></a>
                </div>
            </div>
        </section>
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
            <p class="copyright">&copy; <?php echo htmlspecialchars(date('Y')); ?> Sua Galeria Fantástica. Todos os direitos reservados.</p>
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

            // Script para exibir a mensagem de status (sucesso/erro)
            <?php if (!empty($mensagem_status_texto)): ?> // Alterado para usar as variáveis do PHP
                const statusMessageDiv = document.getElementById('statusMessage');
                const mensagemTipo = <?php echo json_encode($mensagem_status_tipo); ?>;
                const mensagemTexto = <?php echo json_encode($mensagem_status_texto); ?>;

                statusMessageDiv.textContent = mensagemTexto;
                statusMessageDiv.classList.add(mensagemTipo);

                statusMessageDiv.style.display = 'block';
                // Força reflow para garantir a transição de opacidade
                statusMessageDiv.offsetHeight;
                statusMessageDiv.classList.add('show');

                setTimeout(() => {
                    statusMessageDiv.classList.remove('show');
                    statusMessageDiv.addEventListener('transitionend', function handler() {
                        statusMessageDiv.removeEventListener('transitionend', handler);
                        statusMessageDiv.style.display = 'none';
                        statusMessageDiv.classList.remove(mensagemTipo);
                    });
                }, 5000); // Esconde após 5 segundos
            <?php endif; ?>
        });
    </script>
</body>

</html>