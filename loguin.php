<?php
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');



// 1. Definições do tempo de vida da sessão
ini_set('session.gc_maxlifetime', 1800); // 30 minutos
ini_set('session.cookie_lifetime', 1800); // 30 minutos (ou 0 para expirar com o navegador)

// Abrindo a sessão
session_start();

// Inicializa a variável $erros como um array vazio para evitar undefined variable
$erros = [];

// Verifica se a requisição é um POST (envio do formulário de login)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email_validar_loguin = trim($_POST['email'] ?? '');
    $senha_validar_loguin = trim($_POST['password'] ?? '');

    // Criando conexão com o banco de dados
    $conexao = conectarBancoDados();

    if ($conexao) {
        $verifica_loguin = LoguinUser($conexao, $email_validar_loguin, $senha_validar_loguin);

        // A verificação é sempre na chave 'success' do array retornado
        if ($verifica_loguin['success']) {
            // Em caso de sucesso, os dados do usuário estão dentro da chave 'user'
            $usuario_logado = $verifica_loguin['user'];

            // Define as variáveis de sessão para o usuário logado
            $_SESSION['id_logado'] = $usuario_logado['id'];
            $_SESSION['nome_logado'] = $usuario_logado['username'];
            $_SESSION['email_logado'] = $usuario_logado['email'];
            $_SESSION['data_cadastro_logado'] = $usuario_logado['data_cadastro'];
            $_SESSION['logado'] = true;

            // Redireciona para a página principal (index.php)
            header('location:index.php');
            exit(); // Garante que o script pare de executar após o redirecionamento
        } else {
            // Em caso de falha, os erros estão dentro da chave 'errors'
            $erros = $verifica_loguin['errors'];
        }
        // Fechar a conexão com o banco de dados
        mysqli_close($conexao);
    } else {
        // Erro fatal de conexão com o banco de dados
        $erros[] = "Erro fatal: Não foi possível conectar ao banco de dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link rel="stylesheet" href="css/loguin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body class="page-login">
    <div class="background-animation"></div>

    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="img_site/logo.png" alt=""></a>
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

                </ul>
            </nav>
            <div class="user-actions">
                <a href="cadastro.php" class="button-register">Cadastre-se</a>
            </div>
        </div>
    </header>

    <main class="form-page-container">
        <section class="form-card">
            <h2>Bem-vindo de Volta!</h2>
            <?php
            // Exibe os erros apenas se a variável $erros não estiver vazia
            if (!empty($erros)) {
                echo "<div style='color: red; margin-top: 10px;'>";
                echo "Erro(s) no Login:<br>";
                foreach ($erros as $mensagem_erro_texto) {
                    echo "- " . htmlspecialchars($mensagem_erro_texto) . "<br>";
                }
                echo "</div>";
            }
            ?>

            <form action="#" method="POST" class="main-form">
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" required placeholder="seu.email@exemplo.com">
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required placeholder="Sua senha secreta">
                </div>
                <button type="submit" class="submit-button">Entrar</button>
            </form>
            <p class="form-footer-link">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui!</a></p>
            <p class="form-footer-link"><a href="#">Esqueceu sua senha?</a></p>
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
</body>

</html>