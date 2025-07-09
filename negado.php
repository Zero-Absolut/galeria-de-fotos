<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acesso Negado - Sua Galeria Fantástica</title>
  <link rel="icon" type="image/x-icon" href="img_site/logo.png" />
  <link rel="stylesheet" href="css/negado.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap"
    rel="stylesheet" />

</head>

<body>
  <header class="main-header">
    <div class="container">
      <div class="logo">
        <a href="index.php"><img src="img_site/logo.png" alt="Logo da Sua Galeria Fantástica" /></a>
      </div>
    </div>
  </header>

  <main class="access-denied-container">
    <section class="error-card">
      <h1>Acesso Negado!</h1>
      <p>
        Você não tem permissão para acessar esta página. Isso pode acontecer
        se você tentar acessar uma área restrita sem estar logado, ou se suas
        credenciais não forem suficientes.
      </p>
      <a href="loguin.php" class="button-home">Voltar para o Login</a>
      <p style="margin-top: 15px; font-size: 0.9em">
        <a
          href="index.php"
          style="color: var(--secondary-color); text-decoration: none">Ou ir para a Página Inicial</a>
      </p>
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
      <p class="copyright">
        &copy;
        <?php echo htmlspecialchars(date('Y')); ?>
        Sua Galeria Fantástica. Todos os direitos reservados.
      </p>
    </div>
  </footer>
</body>

</html>