<?php
// incluindo arquivos e abrindo a sessao
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
session_start();

//verificando se existe sessao ativa
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    //verificando qual a requisição
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // capturando os valores de sessao e da requisição
        $id_user_upadate = trim($_SESSION['id_logado']);
        $id_foto_update = trim($_POST['id']);
        $titulo_upade = trim($_POST['titulo']);
        $descricao_upade = trim($_POST['descricao']);


        // cria a conexao com banco 
        $conexao = conectarBancoDados();

        //chama a função updatefotos
        $resultado_update = UpdateFotos($conexao, $id_foto_update, $titulo_upade, $descricao_upade);
        // verifica o retorno da função 
        if ($resultado_update['status'] === true) {
            //fecha conexao
            mysqli_close($conexao);

            // apos redireciona para index
            header('location:index.php');
            exit();
        } else {
            $_SESSION['erro_edita_foto'] = $resultado_update;
        }
    }
} else { //caso nao exista sessao redireciona para negado.php
    header('location:negado.php');
    exit();
}
