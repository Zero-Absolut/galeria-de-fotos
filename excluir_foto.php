<?php
include_once('funcoes/conexao.php');
include_once('funcoes/funcoes.php');
// 1. Definições do tempo de vida da sessão
ini_set('session.gc_maxlifetime', 1800); // 30 minutos
ini_set('session.cookie_lifetime', 1800); // 30 minutos (ou 0 para expirar com o navegador)
// iniciando a sessao
session_start();
//verificando se existe sessao ativa
if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {

    //resgatando id do usuario que ira deletar a foto
    $id_usuario = trim($_SESSION['id_logado']);
    // iniciando conexao com a base
    $conexao = conectarBancoDados();

    //verificando o metodo de envio 
    if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

        // resgatando id da foto que sera excluida
        $id_excluir = trim($_GET['id']);

        $excluir_ok = ExcluirFotos($conexao, $id_usuario, $id_excluir);
        print_r($excluir_ok);
        if ($excluir_ok['status'] === true) {
            header('location:index.php');
            exit();
        } // caso nao exista redireciona para acesso negado
    } else {
        header('location:negado.php');
        exit();
    }
} else {
    header('location:negado.php');
    exit();
}
