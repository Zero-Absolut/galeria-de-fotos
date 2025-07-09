 <?php
    // abrindo a sessao 
    session_start();
    // limpando o array da sessao
    $_SESSION = $array = [];
    // destruibdo ela completamente 
    session_destroy();
    //redirecionando a index
    header('location:index.php');
    exit();
    ?>