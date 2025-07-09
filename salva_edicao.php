<?php
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
session_start();


if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {


        $id_user_upadate = trim($_SESSION['id_logado']);
        $id_foto_update = trim($_POST['id']);
        $titulo_upade = trim($_POST['titulo']);
        $descricao_upade = trim($_POST['descricao']);



        $conexao = conectarBancoDados();
        $resultado_update = UpdateFotos($conexao, $id_foto_update, $titulo_upade, $descricao_upade);
        print_r($resultado_update);
        if ($resultado_update['status'] === true) {
            mysqli_close($conexao);
            echo "teste";

            header('location:index.php');
            exit();
        } else {
            $_SESSION['erro_edita_foto'] = $resultado_update;
        }
    }
} else {
    header('location:negado.php');
    exit();
}
