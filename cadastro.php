<?php
//incluindo a conexao 
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
//verificando o metodo do form

$flag_controle_cadastro = null;
$array_mensagem_cadastro = [];

//vericando se e o metodo post
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //pegando os dados enviados pelo usuario do form
    $nome_user_validar = trim($_POST['username']);
    $email_user_validar = trim(($_POST['email']));
    $password_user_validar = trim($_POST['password']);
    $confirm_password_user_validar = trim($_POST['confirm_password']);

    //passando os dados do form para fução 
    $dados_validados = ValidaUsuario(
        $nome_user_validar,
        $email_user_validar,
        $password_user_validar,
        $confirm_password_user_validar
    );

    //vericando qual foi o retorno da função 
    if (isset($dados_validados['nome']) && isset($dados_validados['email']) && isset($dados_validados['senha'])) {

        //resgatando os valores do formulario validados caso seja o retorno do array com os dados

        $nome_validado = $dados_validados['nome'];
        $email_validado = $dados_validados['email'];
        $senha_validada = $dados_validados['senha'];
        $conexao = conectarBancoDados();


        //verificando se a username ou email cadastrado 
        if (VerificaUsernameEmail($conexao, $nome_validado, $email_validado)) {

            $flag_controle_cadastro = InsereCadastro($conexao, $nome_validado, $senha_validada, $email_validado);



            if ($flag_controle_cadastro) {
                $array_mensagem_cadastro['cadastrado'] = "Usuario cadastrado com sucesso!!";

                header('location:loguin.php');
                exit();
            }
        } else {
            $array_mensagem_cadastro['user_existe'] = "Usuario já cadastrado!!";
        }
    } else {


        foreach ($dados_validados as $chave_erro => $nome_erro) {
            $array_mensagem_cadastro[] = $nome_erro;
        }
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se na Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link rel="stylesheet" href="css/cadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>



<!-- Modal para exibir as mensagens do cadastro -->
<?php if (!empty($array_mensagem_cadastro)): ?>
    <div class="modal show" id="mensagemModal">
    <?php else: ?>
        <div class="modal" id="mensagemModal">
        <?php endif; ?>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Aviso</h3>
            </div>
            <div class="modal-body">
                <?php foreach ($array_mensagem_cadastro as $mensagem): ?>
                    <p><?= htmlspecialchars($mensagem) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button onclick="fecharModal()" class="submit-button">Fechar</button>
            </div>
        </div>
        </div>

        <script>
            // Função para fechar o modal
            function fecharModal() {
                const modal = document.getElementById('mensagemModal');
                if (modal) {
                    modal.classList.remove('show'); // Remove a classe 'show' para esconder o modal
                }
            }

            // Fecha o modal ao pressionar a tecla ESC
            window.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    fecharModal();
                }
            });

            // Fecha o modal automaticamente após 5 segundos
            setTimeout(function() {
                fecharModal();
            }, 5000); // Tempo de 5 segundos, ajustável
        </script>







        <body class="page-cadastro">
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
                        <a href="loguin.php" class="button-login">Entrar</a>

                    </div>
                </div>
            </header>

            <main class="form-page-container">
                <section class="form-card">
                    <h2>Crie Sua Conta</h2>
                    <form action="#" method="POST" class="main-form">
                        <div class="form-group">
                            <label for="username">Nome de Usuário:</label>
                            <input type="text" name="username" id="username" required placeholder="Escolha um nome de usuário único">
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail:</label>
                            <input type="email" name="email" id="email" required placeholder="seu.email@exemplo.com">
                        </div>
                        <div class="form-group">
                            <label for="password">Senha:</label>
                            <input type="password" name="password" id="password" required placeholder="Mínimo de 8 caracteres">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirme a Senha:</label>
                            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Redigite sua senha">
                        </div>
                        <button type="submit" class="submit-button">Cadastrar</button>
                    </form>
                    <p class="form-footer-link">Já tem uma conta? <a href="loguin.php">Faça Login aqui!</a></p>
                </section>
            </main>

            <footer>
                <div class="container">
                    <div class="footer-content">
                        <div class="footer-links">
                            <a href="sobre.php">Sobre Nós</a>
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