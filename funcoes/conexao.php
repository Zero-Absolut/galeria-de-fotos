<?php

function conectarBancoDados()
{
    $conexao = new mysqli("localhost", "root", "", "gelaria");
    if ($conexao->connect_error) {
        die("Erro: " . $conexao->connect_error);
    }

    return $conexao;
}
