<?php


// função de validação de cadastro

function ValidaUsuario($nome, $email, $senha, $confirmacao_senha)
{
    // Pra guardar as mensagens de erro que aparecerem
    $error_cadastro = [];
    // Onde a gente guarda os dados do usuário, se tudo der certo
    $array_dados_cadastrais = [];


    // Vê se o nome tá vazio
    if (empty($nome)) {
        $error_cadastro['nome_vazio'] = "O nome não pode ser vazio.";
    }
    // Vê se o nome tem menos de 4 letras
    elseif (strlen($nome) < 4) {
        $error_cadastro['nome_pequeno'] = "O nome deve conter pelo menos 4 caracteres.";
    }
    // Se o nome passou, a gente guarda ele
    else {
        $array_dados_cadastrais['nome'] = $nome;
    }


    // Vê se o e-mail tá vazio
    if (empty($email)) {
        $error_cadastro['email_vazio'] = "O e-mail não pode ser vazio.";
    }
    // Vê se o formato do e-mail tá certo
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_cadastro['email_invalido'] = "Formato de e-mail inválido.";
    }
    // Se o e-mail passou, a gente guarda ele (em minúsculas, pra padronizar)
    else {
        $array_dados_cadastrais['email'] = strtolower($email);
    }


    // Vê a senha e a confirmação
    if (empty($senha)) {
        $error_cadastro['senha_vazia'] = "A senha não pode ser em branco.";
    }
    // Vê se a senha é pequena ou não tem caractere especial
    elseif (strlen($senha) < 8 || !preg_match('/[^a-zA-Z0-9]/', $senha)) {
        $error_cadastro['senha_pequena'] = "A senha deve conter no mínimo oito caracteres e um caractere especial.";
    } else {
        // Agora, a confirmação da senha
        if (empty($confirmacao_senha)) {
            $error_cadastro['repetir_senha_vazio'] = "A confirmação de senha não pode ser vazia.";
        }
        // Vê se a confirmação também é pequena ou não tem caractere especial
        elseif (strlen($confirmacao_senha) < 8 || !preg_match('/[^a-zA-Z0-9]/', $confirmacao_senha)) {
            $error_cadastro['repetir_senha_pequena'] = "A confirmação de senha deve conter no mínimo oito caracteres e um caractere especial.";
        }
        // Vê se as duas senhas são iguais
        elseif ($confirmacao_senha !== $senha) {
            $error_cadastro['senhas_diferentes'] = "As senhas devem ser as mesmas.";
        }
        // Se tudo na senha e confirmação estiver OK, a gente faz o hash e guarda
        else {
            $array_dados_cadastrais['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }
    }


    // Se tiver erro, a gente devolve os erros
    if (!empty($error_cadastro)) {
        return $error_cadastro;
    }
    // Se não tiver erro, devolve os dados prontos pro cadastro
    else {
        return $array_dados_cadastrais;
    }
}


//função para fazer o cadastro de usuario


function InsereCadastro($conn, $nome_validado, $senha_validada, $email_validado)
{

    // Prepara a query SQL pra inserir os dados
    $sql = "INSERT INTO usuarios (username, usersenha, email) VALUES (?, ?, ?)";

    // Prepara a consulta com o banco
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Diz pro banco quais são os dados e o tipo deles (3 strings)
        mysqli_stmt_bind_param($stmt, "sss", $nome_validado, $senha_validada, $email_validado);

        // Tenta executar a inserção
        if (mysqli_stmt_execute($stmt)) {
            // Se deu certo, fecha a conexão e diz que foi true
            mysqli_stmt_close($stmt);
            return true;
        } else {
            // Se deu erro, diz que foi false
            return false;
        }
    } else {
        // Se não deu nem pra preparar a consulta, mostra o erro
        echo "Erro ao preparar o statement: " . mysqli_error($conn);
    }
}

// função para verificar se ja existe um email ou username cadastrado
function VerificaUsernameEmail($conn, $nome, $email)
{

    // Consulta pra ver se o nome de usuário ou e-mail já existem
    $sql = "SELECT username, email FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    // Diz pro banco que vai receber duas strings como parâmetro
    mysqli_stmt_bind_param($stmt, "ss", $nome, $email);

    // Tenta rodar a consulta
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt); // Guarda o resultado
        if (mysqli_stmt_num_rows($stmt) > 0) { // Vê se achou alguma coisa
            mysqli_stmt_close($stmt); // Fecha
            return false; // Já existe
        } else {
            mysqli_stmt_close($stmt); // Fecha
            return true; // Não existe, pode cadastrar
        }
    } else {
        // Se der erro ao acessar o banco
        return "erro ao acessar banco.";
    }
}


// função de login


function LoguinUser($conn, $email_user, $senha_user)
{
    $array_loguin_erros = [];

    // Vê se o email ou a senha estão vazios
    if (empty($email_user)) {
        $array_loguin_erros['email_vazio'] = "O email não pode ser em branco.";
    }

    if (empty($senha_user)) {
        $array_loguin_erros['senha_vazia'] = "A senha não pode ser em branco.";
    }

    // Se tiver erro, a gente já volta com eles
    if (!empty($array_loguin_erros)) {
        return ['success' => false, 'errors' => $array_loguin_erros];
    }

    $email_loguin = strtolower($email_user);
    $senha_loguin = $senha_user;

    // Busca o usuário pelo email
    $sql = "SELECT id, username, usersenha, email, data_cadastro FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $array_loguin_erros['erro_preparacao_sql'] = "Erro na preparação da consulta SQL.";
        return ['success' => false, 'errors' => $array_loguin_erros];
    }

    mysqli_stmt_bind_param($stmt, "s", $email_loguin);

    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);

        // Vê se achou o usuário
        if ($usuario = mysqli_fetch_assoc($resultado)) {
            // Compara a senha digitada com a senha hash do banco
            if (password_verify($senha_loguin, $usuario['usersenha'])) {
                mysqli_stmt_close($stmt); // Fecha a conexão
                return ['success' => true, 'user' => $usuario]; // Login bem-sucedido
            } else {
                $array_loguin_erros['senha_incorreta'] = "Senha incorreta.";
            }
        } else {
            $array_loguin_erros['usuario_nao_encontrado'] = "Email não encontrado.";
        }
    } else {
        $array_loguin_erros['erro_execucao'] = "Erro ao executar a consulta.";
    }

    mysqli_stmt_close($stmt);

    // Se chegou aqui, é porque deu algum erro no login
    return ['success' => false, 'errors' => $array_loguin_erros];
}


// Função pra fazer upload de imagens
function Upload($conn, $imagem, $titlo, $descricao, $id_usuario_logado)
{
    // Caminho que vai ser usado na URL (no navegador)
    $diretorio_publico = "/galeria/uploads/imagens/";

    // Caminho real da pasta no seu servidor
    $diretorio_servidor = $_SERVER['DOCUMENT_ROOT'] . $diretorio_publico;

    // Se a pasta não existe, tenta criar
    if (!is_dir($diretorio_servidor)) {
        if (!mkdir($diretorio_servidor, 0755, true)) {
            return ['status' => false, 'erro' => "Erro interno: Não deu pra criar a pasta de uploads. Vê as permissões aí."];
        }
    }

    // Pega o nome e a extensão originais do arquivo
    $nome_original_do_upload = basename($imagem["name"]);
    $extensao_arquivo = strtolower(pathinfo($nome_original_do_upload, PATHINFO_EXTENSION));

    // Tipos de arquivo que a gente aceita
    $tipos_permitidos = ["jpg", "jpeg", "png", "gif"];

    // Cria um nome único pro arquivo pra não dar conflito
    $novo_nome_arquivo_unico = uniqid('img_', true) . '.' . $extensao_arquivo;

    // Caminho completo onde a imagem vai ficar no servidor
    $caminho_final_servidor = $diretorio_servidor . $novo_nome_arquivo_unico;

    // Caminho que vai pro banco de dados (pra usar na web)
    $caminho_para_db_e_url = $diretorio_publico . $novo_nome_arquivo_unico;


    // Vê se a extensão é permitida
    if (!in_array($extensao_arquivo, $tipos_permitidos)) {
        return ['status' => false, 'erro' => "Erro: Só aceitamos JPG, JPEG, PNG e GIF."];
    }

    // Vê se o tamanho do arquivo não é grande demais (até 6MB)
    $tamanho_permitido = 6 * 1024 * 1024;
    if ($imagem['size'] > $tamanho_permitido) {
        return ['status' => false, 'erro' => "O arquivo é muito grande. O máximo é 6MB."];
    }

    // Tenta mover o arquivo temporário pro lugar certo no servidor
    if (move_uploaded_file($imagem['tmp_name'], $caminho_final_servidor)) {

        // Prepara a query pra salvar as infos da imagem no banco
        $sql = "INSERT INTO imagens 
                (titulo, descricao, nome_arquivo, caminho_arquivo, data_upload, usuario_id) 
                VALUES (?, ?, ?, ?, NOW(), ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Cola os valores na query: 4 strings e 1 inteiro
            mysqli_stmt_bind_param(
                $stmt,
                'ssssi',
                $titlo,
                $descricao,
                $novo_nome_arquivo_unico,
                $caminho_para_db_e_url,
                $id_usuario_logado
            );

            // Se conseguir salvar no banco
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt); // Fecha
                return [
                    'status' => true,
                    'mensagem' => "A imagem foi enviada e registrada com sucesso!",
                ];
            } else {
                // Se der erro ao salvar no banco, a gente apaga a imagem do servidor pra não ficar lixo
                unlink($caminho_final_servidor);
                mysqli_stmt_close($stmt);
                return ['status' => false, 'erro' => "Erro ao guardar informações da imagem no banco: " . mysqli_error($conn)];
            }
        } else {
            // Se der erro ao preparar a consulta SQL, apaga a imagem do servidor também
            unlink($caminho_final_servidor);
            return ['status' => false, 'erro' => "Erro interno: Falha ao preparar a consulta SQL."];
        }
    } else {
        // Se não conseguiu mover o arquivo (problema de permissão na pasta, por exemplo)
        return ['status' => false, 'erro' => "Erro ao salvar a imagem no servidor. Vê as permissões da pasta 'uploads/imagens'."];
    }
}


// função pra buscar fotos no banco
function BuscaFoto($conn, $id)
{
    $retorno = [
        'status' => false,
        'dados' => [], // Aqui vão as fotos, se encontrar
        'erro' => ''
    ];

    // Query pra buscar as fotos de um usuário específico
    $sql = "SELECT * FROM imagens WHERE usuario_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Vê se preparou a query direitinho
    if ($stmt === false) {
        error_log("Erro ao preparar a instrução: " . mysqli_error($conn));
        $retorno['erro'] = "Erro interno do servidor ao preparar a busca.";
        return $retorno;
    }

    // Cola o ID do usuário na query (é um inteiro)
    $bind_success = mysqli_stmt_bind_param($stmt, 'i', $id);

    // Vê se conseguiu colar o parâmetro
    if ($bind_success === false) {
        error_log("Erro ao vincular parâmetros: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        $retorno['erro'] = "Erro interno do servidor ao vincular dados.";
        return $retorno;
    }

    // Executa a busca
    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);

        // Se achou resultados
        if ($resultado) {
            $fotos_encontradas = []; // Array pra guardar as fotos

            // Pega cada foto e adiciona no array
            while ($foto = mysqli_fetch_assoc($resultado)) {
                $fotos_encontradas[] = $foto;
            }

            $retorno['status'] = true;
            $retorno['dados'] = $fotos_encontradas; // Aqui estão todas as fotos

            mysqli_free_result($resultado); // Libera a memória do resultado
        } else {
            error_log("Erro ao obter resultado da consulta: " . mysqli_stmt_error($stmt));
            $retorno['erro'] = "Erro interno: falha ao processar resultados.";
        }
    } else {
        // Se deu erro na execução
        error_log("Erro na execução da consulta: " . mysqli_stmt_error($stmt));
        $retorno['erro'] = "Erro ao tentar fazer busca.";
    }

    mysqli_stmt_close($stmt); // Sempre fecha a conexão no final
    return $retorno;
}


function PesquisaFoto($conn, $id_user, $termo_busca)
{
    // A gente vai retornar isso no final
    $retorno = [
        'status' => false,
        'erro' => '',
        'dados' => []
    ];

    // Query pra buscar fotos por título ou descrição pra um usuário
    $sql = "SELECT * FROM imagens WHERE usuario_id = ? AND (titulo LIKE ? OR descricao LIKE ? )";

    $stmt = mysqli_prepare($conn, $sql);

    // Vê se a query foi preparada certinho
    if ($stmt === false) {
        error_log("Erro ao preparar a instrução SQL: " . mysqli_error($conn));
        $retorno['erro'] = "Erro interno do servidor ao preparar a busca.";
        return $retorno;
    }

    // Prepara o termo de busca pra usar com LIKE (adiciona os '%')
    $termo_busca_like = '%' . $termo_busca . '%';

    // Cola os parâmetros na query: o ID do usuário (inteiro) e o termo de busca (string, duas vezes)
    $bind = mysqli_stmt_bind_param($stmt, 'iss', $id_user, $termo_busca_like, $termo_busca_like);

    // Vê se conseguiu colar os parâmetros
    if ($bind === false) {
        error_log("Erro ao vincular parâmetros na busca: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        $retorno['erro'] = "Erro interno do servidor ao vincular dados para a busca.";
        return $retorno;
    }

    // Roda a busca
    if (mysqli_execute($stmt)) {
        $resultado_query = mysqli_stmt_get_result($stmt);

        // Se conseguiu pegar os resultados
        if ($resultado_query) {
            $fotos_encontradas = [];

            // Pega cada foto que achou e guarda no array
            while ($foto = mysqli_fetch_assoc($resultado_query)) {
                $fotos_encontradas[] = $foto;
            }

            mysqli_free_result($resultado_query); // Libera a memória

            // Se achou alguma foto
            if (!empty($fotos_encontradas)) {
                $retorno['status'] = true;
                $retorno['dados'] = $fotos_encontradas;
            } else {
                $retorno['status'] = false;
                $retorno['erro'] = "Nenhuma foto encontrada para o que você digitou.";
            }
        } else {
            error_log("Erro ao obter o resultado da busca: " . mysqli_stmt_error($stmt));
            $retorno['erro'] = "Erro ao processar os resultados da busca.";
        }
    } else {
        // Se deu erro ao executar a query
        error_log("Erro ao executar a instrução SQL de busca: " . mysqli_stmt_error($stmt));
        $retorno['erro'] = "Erro interno do servidor ao executar a busca.";
    }

    mysqli_stmt_close($stmt); // Fecha
    return $retorno;
}


// função pra excluir fotos
function ExcluirFotos($conn, $id_usuario, $id_excluir)
{
    $resultado_excluir = [
        'status' => false,
        'erro' => "",
        'dados' => ""
    ];

    // Primeiro, a gente busca a foto pra pegar o caminho dela
    $sql = "SELECT * FROM imagens WHERE id = ? AND usuario_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    $bind = mysqli_stmt_bind_param($stmt, 'ii', $id_excluir, $id_usuario);

    if ($bind === false) {
        $resultado_excluir['status'] = false;
        $resultado_excluir['erro'] = "Erro ao preparar a conexão.";
        return $resultado_excluir;
    }

    if (mysqli_execute($stmt)) {
        $resultado_busca = mysqli_stmt_get_result($stmt);
        // Vê se a foto existe e pertence ao usuário
        if ($resultado_busca && mysqli_num_rows($resultado_busca) > 0) {
            $foto_excluir = mysqli_fetch_assoc($resultado_busca);
            mysqli_free_result($resultado_busca); // Libera memória

            // Monta o caminho completo da foto no servidor
            $caminho_exluir = $_SERVER['DOCUMENT_ROOT'] . $foto_excluir['caminho_arquivo'];

            // Vê se o arquivo existe no servidor
            if (file_exists($caminho_exluir)) {
                // Tenta apagar a foto do servidor
                if (unlink($caminho_exluir)) {
                    // Agora, apaga o registro da foto do banco de dados
                    $sql_delete = "DELETE FROM imagens WHERE id = ? AND usuario_id = ?";
                    $stmt_delete = mysqli_prepare($conn, $sql_delete);
                    $bind = mysqli_stmt_bind_param($stmt_delete, 'ii', $id_excluir, $id_usuario);

                    if ($bind === false) {
                        mysqli_stmt_close($stmt_delete);
                        $resultado_excluir['status'] = false;
                        $resultado_excluir['erro'] = "Erro ao preparar a conexão.";
                        return $resultado_excluir;
                    }
                    // Se conseguiu apagar do banco
                    if (mysqli_execute($stmt_delete)) {
                        mysqli_stmt_close($stmt_delete);
                        $resultado_excluir['status'] = true;
                        $resultado_excluir['dados'] = "foto excluída.";
                    } else {
                        mysqli_stmt_close($stmt_delete);
                        $resultado_excluir['erro'] = "erro ao excluir foto do banco.";
                    }
                } else {
                    $resultado_excluir['erro'] = "Erro ao apagar a foto do servidor.";
                }
            } else {
                // Se a foto não foi encontrada no servidor, mas existe no banco
                // Podemos tentar remover só do banco ou retornar um erro específico.
                // Por segurança, vamos apenas indicar que não achou o arquivo físico.
                $resultado_excluir['erro'] = "Foto não encontrada no servidor.";
                $resultado_excluir['dados'] = $foto_excluir;
            }
        } else {
            $resultado_excluir['erro'] = "Foto não encontrada ou você não tem permissão para excluí-la.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $resultado_excluir['erro'] = "Erro ao buscar a foto para exclusão.";
    }

    return $resultado_excluir;
}


function UpdateFotos($conn, $id_update, $title_update, $description_update)
{
    $resultado_update = [
        'status' => false,
        'erro' => "",
        'dados' => ""
    ];

    // Query pra atualizar título e descrição da foto
    $sql = "UPDATE imagens SET titulo = ?, descricao = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Cola os parâmetros: título (string), descrição (string) e ID (inteiro)
    $bind = mysqli_stmt_bind_param($stmt, 'ssi', $title_update, $description_update, $id_update);
    if ($bind === false) {
        $resultado_update['status'] = false;
        $resultado_update['erro'] = "Erro ao preparar a conexão.";
        return $resultado_update;
    }

    // Se conseguir executar a atualização
    if (mysqli_stmt_execute($stmt)) {
        $resultado_update['status'] = true;
        $resultado_update['dados'] = "Alterado com sucesso.";
    } else {
        $resultado_update['status'] = false;
        $resultado_update['erro'] = "Erro ao atualizar no banco de dados.";
    }
    mysqli_stmt_close($stmt); // Fecha
    return $resultado_update;
}
