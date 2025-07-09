<?php
// sobre.php
// É crucial iniciar a sessão no topo de qualquer página que utilize sessões.
// Isso permite que o PHP acesse ou modifique a superglobal $_SESSION.
session_start();

// Obtém o nome do arquivo atual para usar na lógica de "active link"
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Sua Galeria Fantástica</title>
    <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sobre.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="page-sobre">

    <div class="background-animation"></div>

    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="img_site/logo.png" alt="Logo Sua Galeria Fantástica">
                </a>
            </div>
            <input type="checkbox" id="menu-toggle" class="menu-toggle-checkbox">
            <label for="menu-toggle" class="menu-toggle" aria-label="Abrir e fechar menu de navegação"> <span></span>
                <span></span>
                <span></span>
            </label>
            <nav class="main-nav">
                <ul>
                    <?php
                    // Lógica para alterar os links de navegação baseado no estado de login
                    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                        // Links para usuário logado
                        echo '<li><a href="index.php" class="' . ($current_page == 'index.php' ? 'current-page' : '') . '">Início</a></li>';
                        echo '<li><a href="upload.php" class="' . ($current_page == 'upload.php' ? 'current-page' : '') . '">Upload de Fotos</a></li>';
                        echo '<li><a href="fotos.php" class="' . ($current_page == 'fotos.php' ? 'current-page' : '') . '">Minha Galeria</a></li>';

                        echo '<li><a href="sobre.php" class="' . ($current_page == 'sobre.php' ? 'current-page' : '') . '">Sobre</a></li>';
                        echo '<li><a href="contato.php" class="' . ($current_page == 'contato.php' ? 'current-page' : '') . '">Contato</a></li>';
                    } else {
                        // Links para usuário NÃO logado (landing page)
                        echo '<li><a href="index.php" class="' . ($current_page == 'index.php' ? 'current-page' : '') . '">Início</a></li>';
                        echo '<li><a href="sobre.php" class="' . ($current_page == 'sobre.php' ? 'current-page' : '') . '">Sobre</a></li>';
                        echo '<li><a href="contato.php" class="' . ($current_page == 'contato.php' ? 'current-page' : '') . '">Contato</a></li>';
                        echo '<li><a href="ajuda.php" class="' . ($current_page == 'ajuda.php' ? 'current-page' : '') . '">Ajuda</a></li>'; // Adicionei 'Ajuda' aqui, se for uma página para não logados
                    }
                    ?>
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
                ?>
                    <a href="login.php" class="button-login <?php echo ($current_page == 'login.php' ? 'current-page' : ''); ?>">Entrar</a> <a href="cadastro.php" class="button-register <?php echo ($current_page == 'cadastro.php' ? 'current-page' : ''); ?>">Cadastrar</a>
                <?php
                }
                ?>
            </div>
        </div>
    </header>

    <main class="page-content-container">
        <section class="about-card">
            <h2>Minha Jornada: Da Infraestrutura ao Desenvolvimento</h2>
            <div class="about-content">
                <p>Olá! Seja bem-vindo(a) à página que conta um pouco da minha trajetória profissional. Meu nome é Mateus Felipe Brito Silva, e esta "Sua Galeria Fantástica" é mais do que um projeto de portfólio; é a materialização da minha paixão e dedicação em construir soluções tecnológicas.</p>

                <p>Minha jornada no mundo da tecnologia começou há aproximadamente 12 anos na área de infraestrutura de TI. Durante essa década, atuei com sistemas, redes e hardware, consolidando uma base sólida de conhecimento sobre como a tecnologia opera "por trás das cenas". Essa experiência me proporcionou uma compreensão aprofundada dos fundamentos da computação e da importância de sistemas robustos e eficientes.</p>

                <p>No entanto, sempre houve um chamado mais forte: o desejo de criar, codificar e desenvolver. A capacidade de transformar ideias em aplicações funcionais, de resolver problemas através da lógica de programação e de ver um produto digital tomar forma sempre me fascinou. Esse fascínio me levou a embarcar em uma transição de carreira, mergulhando de cabeça no universo do desenvolvimento.</p>

                <p>Nos últimos tempos, tenho me dedicado intensamente a estudar e aprimorar minhas habilidades em desenvolvimento. Tenho investido horas diárias em cursos, tutoriais e, principalmente, na construção de projetos práticos de portfólio, como esta galeria, que demonstram minha capacidade de aplicar os conhecimentos adquiridos. Meu objetivo é transformar essa paixão em uma nova carreira, buscando uma oportunidade para atuar como desenvolvedor e contribuir com equipes inovadoras.</p>

                <p>Acredito que a minha experiência prévia em infraestrutura me confere uma perspectiva única no desenvolvimento. Entender o "como" as aplicações interagem com o ambiente em que vivem, a importância da performance, segurança e escalabilidade, é um diferencial valioso que trago para cada linha de código que escrevo.</p>

                <p>Estou em Sabará, Minas Gerais, e pronto para novos desafios. Se você busca um profissional dedicado, com base sólida em tecnologia e uma fome insaciável por aprender e construir, vamos conversar! Esta galeria é apenas uma amostra do que estou aprendendo e construindo.</p>

                <p class="signature">Com entusiasmo e dedicação,<br>Mateus Brito</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-content">
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
        <div class="copyright">
            &copy; <?php echo htmlspecialchars(date('Y')); ?> Sua Galeria Fantástica. Todos os direitos reservados.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggleCheckbox = document.getElementById('menu-toggle');
            const mainNav = document.querySelector('.main-nav');

            menuToggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    mainNav.style.maxHeight = mainNav.scrollHeight + "px";
                } else {
                    mainNav.style.maxHeight = "0";
                }
            });

            document.addEventListener('click', function(event) {
                if (!mainNav.contains(event.target) && !menuToggleCheckbox.contains(event.target)) {
                    menuToggleCheckbox.checked = false;
                    mainNav.style.maxHeight = "0";
                }
            });
        });
    </script>
</body>

</html>