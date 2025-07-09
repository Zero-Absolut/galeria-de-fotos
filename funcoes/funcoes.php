<?php


// função de validação de cadastro

function ValidaUsuario($nome, $email, $senha, $confirmacao_senha)
{
    // 1. Inicialização dos Arrays
    // Este array armazenará todas as mensagens de erro que surgirem durante a validação.
    $error_cadastro = [];
    // Este array armazenará os dados do usuário APENAS SE TODAS as validações forem bem-sucedidas.
    // É este array que esperamos que seja retornado quando tudo estiver OK.
    $array_dados_cadastrais = [];


    // 2. Validação do Campo 'nome'
    // Verifica se o campo 'nome' está vazio.
    if (empty($nome)) {
        $error_cadastro['nome_vazio'] = "O nome não pode ser vazio.";
    }
    // Verifica se o 'nome' tem menos de 4 caracteres.
    elseif (strlen($nome) < 4) {
        $error_cadastro['nome_pequeno'] = "O nome deve conter pelo menos 4 caracteres.";
    }
    // Se o 'nome' passou em todas as validações anteriores, ele é considerado válido
    // e é adicionado ao array de dados a serem cadastrados.
    else {
        $array_dados_cadastrais['nome'] = $nome;
    }


    // 3. Validação do Campo 'email'
    // Verifica se o campo 'email' está vazio.
    if (empty($email)) {
        $error_cadastro['email_vazio'] = "O e-mail não pode ser vazio.";
    }
    // Verifica se o formato do 'email' é válido usando a função nativa do PHP.
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_cadastro['email_invalido'] = "Formato de e-mail inválido.";
    }
    // Se o 'email' passou em todas as validações, é adicionado ao array de dados.
    else {
        $array_dados_cadastrais['email'] = strtolower($email);
    }


    // 4. Validação da Senha e Confirmação de Senha (Ponto Crucial da Correção)
    // Verifica se o campo 'senha' está vazio.
    if (empty($senha)) {
        $error_cadastro['senha_vazia'] = "A senha não pode ser em branco.";
    }
    // Verifica se a 'senha' tem menos de 8 caracteres OU não contém um caractere especial.
    elseif (strlen($senha) < 8 || !preg_match('/[^a-zA-Z0-9]/', $senha)) {
        $error_cadastro['senha_pequena'] = "A senha deve conter no mínimo oito caracteres e um caractere especial.";
    } else {
        // Validação da Confirmação de Senha
        if (empty($confirmacao_senha)) {
            $error_cadastro['repetir_senha_vazio'] = "A confirmação de senha não pode ser vazia.";
        }
        // Repete a validação de formato/tamanho para a confirmação, garantindo consistência.
        elseif (strlen($confirmacao_senha) < 8 || !preg_match('/[^a-zA-Z0-9]/', $confirmacao_senha)) {
            $error_cadastro['repetir_senha_pequena'] = "A confirmação de senha deve conter no mínimo oito caracteres e um caractere especial.";
        }
        // A condição MAIS IMPORTANTE para a confirmação: verifica se as senhas são diferentes.
        elseif ($confirmacao_senha !== $senha) {
            $error_cadastro['senhas_diferentes'] = "As senhas devem ser as mesmas.";
        }
        // SOMENTE SE A SENHA PRINCIPAL E A CONFIRMAÇÃO DE SENHA PASSARAM EM TODAS AS VALIDAÇÕES ACIMA,
        // então podemos fazer o hash da senha e adicioná-la ao array de dados para cadastro.
        else {
            // Aplica o hash de segurança à senha antes de armazená-la. ESSENCIAL para segurança!
            $array_dados_cadastrais['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }
    }


    // 5. Lógica de Retorno Final (Outro Ponto Crucial da Correção)
    // Este bloco determina qual array será retornado pela função:
    // o array de ERROS OU o array de DADOS VALIDADOS.

    // SE o array '$error_cadastro' NÃO estiver vazio (ou seja, se algum erro foi adicionado),
    // a função deve retornar os erros.
    if (!empty($error_cadastro)) {
        return $error_cadastro;
    }
    // CASO CONTRÁRIO (se '$error_cadastro' estiver vazio, significando que TODAS as validações passaram),
    // a função retorna os dados que estão prontos para serem cadastrados.
    else {
        return $array_dados_cadastrais;
    }
}






//função para fazer o cadastro de usuario 




function InsereCadastro($conn, $nome_validado, $senha_validada, $email_validado)
{

    // Monta a query SQL com placeholders (?) para os valores que serão inseridos
    $sql = "INSERT INTO usuarios (username, usersenha, email) VALUES (?, ?, ?)";

    // Prepara a query usando a conexão com o banco ($conn)
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Associa os valores reais aos placeholders: todos são strings (sss)
        // $username, $senha_hash e $email devem estar definidos antes desta etapa
        mysqli_stmt_bind_param($stmt, "sss", $nome_validado, $senha_validada, $email_validado);

        // Executa a query com os valores passados
        if (mysqli_stmt_execute($stmt)) {

            //fecha conexao -
            mysqli_stmt_close($stmt);
            //retorna a flag caso processo corra bem
            return true;
        } else {


            //retorna a flag com o erro
            return false;
        }

        // Fecha a statement liberando os recursos

    } else {
        // Se a preparação da statement falhou, mostra o erro da conexão
        echo "Erro ao preparar o statement: " . mysqli_error($conn);
    }
}

// função para verificar se ja existe um email ou username cadastrado
function VerificaUsernameEmail($conn, $nome, $email)
{

    // query de consulta no banco para verificar se possui registro
    $sql = "SELECT username, email FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    // parametros de segurança o ss diz que vai receber duas strings como parametro
    mysqli_stmt_bind_param($stmt, "ss", $nome, $email);
    //verificanado se houve erro ao acessar banco 

    if (mysqli_stmt_execute($stmt)) {


        mysqli_stmt_store_result($stmt); // Armazena o resultado da consulta
        if (mysqli_stmt_num_rows($stmt) > 0) { // Verifica se há  linha retornada
            // -----------------------------------------------------------

            mysqli_stmt_close($stmt); // Fecha o statement
            return false; // Retorna false: Usuário ou e-mail JÁ EXISTE
        } else {
            mysqli_stmt_close($stmt); // Fecha o statement
            return true; // Retorna true: Usuário e e-mail NÃO EXISTEM, PODE CADASTRAR
        }
    } else {
        // Lidar com o erro de execução, você já tem essa parte
        return "erro ao acessar banco.";
    }
}


// função de loguin 


function LoguinUser($conn, $email_user, $senha_user)
{
    $array_loguin_erros = [];

    // 1. Validação inicial de campos vazios
    if (empty($email_user)) {
        $array_loguin_erros['email_vazio'] = "O email não pode ser em branco.";
    }

    if (empty($senha_user)) {
        $array_loguin_erros['senha_vazia'] = "A senha não pode ser em branco.";
    }

    // Se já existem erros de campos vazios, retorna o array com os erros
    if (!empty($array_loguin_erros)) {
        // Retorna a estrutura consistente de falha
        return ['success' => false, 'errors' => $array_loguin_erros];
    }

    $email_loguin = strtolower($email_user);
    $senha_loguin = $senha_user;

    $sql = "SELECT id, username, usersenha, email, data_cadastro FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $array_loguin_erros['erro_preparacao_sql'] = "Erro na preparação da consulta SQL.";
        return ['success' => false, 'errors' => $array_loguin_erros];
    }

    mysqli_stmt_bind_param($stmt, "s", $email_loguin);

    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);

        if ($usuario = mysqli_fetch_assoc($resultado)) {
            if (password_verify($senha_loguin, $usuario['usersenha'])) {


                // Retorna a estrutura consistente de sucesso
                //fechando conexao com banco
                mysqli_stmt_close($stmt);
                return ['success' => true, 'user' => $usuario]; // Encapsula o usuário sob a chave 'user'

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

    // Se a função chegou até aqui, houve um erro.
    return ['success' => false, 'errors' => $array_loguin_erros];
}


// A função Upload recebe a conexão com o banco de dados,
// o array do arquivo de imagem (do $_FILES), o título, a descrição,
// e o ID do usuário logado.
// Ela retorna um array com 'status' (true para sucesso, false para erro)
// e uma mensagem ou erro detalhado.
function Upload($conn, $imagem, $titlo, $descricao, $id_usuario_logado)
{

    // --- 1. Definição e Criação dos Caminhos de Diretório ---
    // É crucial ter dois tipos de caminhos:
    // a) Caminho PÚBLICO (URL): Usado para acessar a imagem via navegador (no <img src="">)
    //    e é o que será salvo no banco de dados. Sempre termine com uma barra (/).
    $diretorio_publico = "/galeria/uploads/imagens/";

    // b) Caminho ABSOLUTO no SERVIDOR: Usado pelas funções de arquivo do PHP (move_uploaded_file, unlink, mkdir).
    //    $_SERVER['DOCUMENT_ROOT'] aponta para a raiz do seu site no sistema de arquivos do servidor.
    $diretorio_servidor = $_SERVER['DOCUMENT_ROOT'] . $diretorio_publico;

    // Garante que o diretório de destino exista. Se não existir, tenta criá-lo.
    // O '0755' são as permissões (leitura/escrita/execução para o dono, leitura/execução para grupo/outros).
    // 'true' permite criar diretórios aninhados (ex: /uploads/imagens/2025/).
    if (!is_dir($diretorio_servidor)) {
        if (!mkdir($diretorio_servidor, 0755, true)) {
            // Se a criação da pasta falhar (geralmente por permissões), retorna um erro.
            return ['status' => false, 'erro' => "Erro interno: Não foi possível criar o diretório de uploads. Verifique as permissões de pasta no servidor."];
        }
    }

    // --- 2. Obtenção do Nome Original e Extensão ---
    // Pega apenas o nome do arquivo original enviado pelo usuário (ex: "minha_foto.jpg").
    // Isso é seguro porque não inclui o caminho completo do cliente.
    $nome_original_do_upload = basename($imagem["name"]);

    // Obtém a extensão do arquivo a partir do nome original.
    // 'pathinfo()' é a função correta para isso, NÃO 'phpinfo()'.
    // 'PATHINFO_EXTENSION' retorna apenas a extensão (ex: "jpg").
    // 'strtolower()' converte para minúsculas para padronizar a validação.
    $extensao_arquivo = strtolower(pathinfo($nome_original_do_upload, PATHINFO_EXTENSION));

    // Tipos de arquivos permitidos para upload.
    $tipos_permitidos = ["jpg", "jpeg", "png", "gif"];

    // --- 3. Geração de um Nome de Arquivo Único e Definição de Caminhos Finais ---
    // ESTE É UM PASSO CRÍTICO PARA SEGURANÇA E ORGANIZAÇÃO:
    // Gera um nome de arquivo único para evitar colisões (dois usuários enviando "foto.jpg")
    // e para prevenir ataques de sobrescrita de arquivos.
    // 'uniqid()' cria um ID único baseado no tempo. 'true' adiciona mais entropia para maior unicidade.
    $novo_nome_arquivo_unico = uniqid('img_', true) . '.' . $extensao_arquivo;

    // Define o caminho COMPLETO no sistema de arquivos do servidor para onde o arquivo será movido.
    // Esta variável será usada por 'move_uploaded_file()' e 'unlink()'.
    $caminho_final_servidor = $diretorio_servidor . $novo_nome_arquivo_unico;

    // Define o caminho PÚBLICO (URL) que será salvo no banco de dados.
    // Este é o caminho que a tag <img src=""> usará.
    $caminho_para_db_e_url = $diretorio_publico . $novo_nome_arquivo_unico;

    // --- 4. Validação da Extensão do Arquivo ---
    // Verifica se a extensão obtida está na lista de tipos permitidos.
    if (!in_array($extensao_arquivo, $tipos_permitidos)) {
        return ['status' => false, 'erro' => "Erro: Apenas arquivos JPG, JPEG, PNG e GIF são permitidos."];
    }

    // --- 5. Validação do Tamanho Máximo do Arquivo (6MB) ---
    $tamanho_permitido = 6 * 1024 * 1024; // 6 Megabytes em bytes.

    // ERRO CORRIGIDO AQUI: O acesso ao tamanho deve ser $imagem['size'], não $imagem['image']['size'].
    // Lembre-se que '$imagem' já é o sub-array do $_FILES['nome_do_input'].
    if ($imagem['size'] > $tamanho_permitido) {
        return ['status' => false, 'erro' => "O arquivo é muito grande. Tamanho máximo permitido é 6MB."];
    }


    // --- 7. Tentativa de Mover o Arquivo para o Destino Final ---
    // ERRO CORRIGIDO AQUI: O primeiro parâmetro deve ser o caminho temporário ($imagem['tmp_name']).
    // O segundo parâmetro é o caminho ABSOLUTO final no servidor ($caminho_final_servidor).
    // Se o movimento for bem-sucedido, o arquivo agora está permanentemente no seu servidor.
    if (move_uploaded_file($imagem['tmp_name'], $caminho_final_servidor)) {

        // --- 8. Preparação e Execução da Inserção no Banco de Dados ---
        $sql = "INSERT INTO imagens 
                (titulo, descricao, nome_arquivo, caminho_arquivo, data_upload, usuario_id) 
                VALUES (?, ?, ?, ?, NOW(), ?)";

        $stmt = mysqli_prepare($conn, $sql); // Prepara a consulta para evitar SQL Injection.

        if ($stmt) {
            // --- 9. Ligação de Parâmetros (TODOS OS ERROS CORRIGIDOS AQUI!) ---
            // 'ssssi': Define os tipos de dados para cada placeholder '?':
            // 's' = string (para titulo, descricao, nome_arquivo, caminho_arquivo)
            // 'i' = integer (para usuario_id)
            // A ORDEM das variáveis deve CORRESPONDER EXATAMENTE à ordem dos '?' na sua query SQL.
            // Aqui, estamos assumindo que '$titlo' é o parâmetro de entrada para o título.
            mysqli_stmt_bind_param(
                $stmt,
                'ssssi',
                $titlo,                     // 1º ? -> titulo (string)
                $descricao,                 // 2º ? -> descricao (string)
                $novo_nome_arquivo_unico,   // 3º ? -> nome_arquivo (o nome único gerado, string)
                $caminho_para_db_e_url,     // 4º ? -> caminho_arquivo (o caminho público/URL, string)
                $id_usuario_logado          // 5º ? -> usuario_id (inteiro)
            );

            // Tenta executar a consulta preparada.
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt); // Fecha o statement para liberar recursos.
                // Retorno de sucesso.
                return [
                    'status' => true,
                    'mensagem' => "A imagem foi enviada e registrada com sucesso!",

                ];
            } else {
                // --- 10. Limpeza de Arquivo Órfão em Caso de Falha no Banco de Dados ---
                // Se o arquivo foi movido para o servidor, mas a inserção no DB falhou,
                // devemos DELETAR o arquivo físico para evitar "lixo" e inconsistência.
                unlink($caminho_final_servidor);
                mysqli_stmt_close($stmt);
                return ['status' => false, 'erro' => "Erro ao guardar informações da imagem no banco de dados: " . mysqli_error($conn)];
            }
        } else {
            // --- 11. Limpeza de Arquivo Órfão em Caso de Falha na Preparação do SQL ---
            // Se o 'mysqli_prepare' falhar (ex: sintaxe SQL errada, problema na conexão),
            // o arquivo já pode ter sido movido. Também deletamos ele.
            unlink($caminho_final_servidor);
            return ['status' => false, 'erro' => "Erro interno: Falha ao preparar a consulta SQL para o banco de dados."];
        }
    } else {
        // --- 12. Erro ao Mover o Arquivo Físico ---
        // Este 'else' captura falhas no 'move_uploaded_file()'.
        // Geralmente ocorre por problemas de permissão de escrita na pasta de destino no servidor.
        return ['status' => false, 'erro' => "Erro ao salvar a imagem no servidor. Verifique as permissões da pasta 'uploads/imagens'."];
    }
}


// função buscar fotos na base


function BuscaFoto($conn, $id)
{
    $retorno = [
        'status' => false,
        'dados' => [], // Inicializado como um array vazio para armazenar MÚLTIPLAS fotos
        'erro' => ''
    ];

    $sql = "SELECT * FROM imagens WHERE usuario_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    // Verifica se a preparação da instrução SQL foi bem-sucedida
    if ($stmt === false) {
        // Loga o erro real para depuração (não exibir para o usuário final em produção)
        error_log("Erro ao preparar a instrução: " . mysqli_error($conn));
        $retorno['erro'] = "Erro interno do servidor ao preparar a busca.";
        return $retorno;
    }

    // Vincula o parâmetro. 'i' é para INT
    $bind_success = mysqli_stmt_bind_param($stmt, 'i', $id);

    // Verifica se a vinculação dos parâmetros foi bem-sucedida
    if ($bind_success === false) {
        error_log("Erro ao vincular parâmetros: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt); // Fecha o statement mesmo em caso de falha na vinculação
        $retorno['erro'] = "Erro interno do servidor ao vincular dados.";
        return $retorno;
    }

    // Executa a instrução preparada
    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);

        // Verifica se houve um conjunto de resultados válido
        if ($resultado) {
            $fotos_encontradas = []; // Array para coletar todas as fotos

            // Percorre CADA LINHA do conjunto de resultados
            while ($foto = mysqli_fetch_assoc($resultado)) {
                $fotos_encontradas[] = $foto; // Adiciona cada linha (que é uma foto) ao array
            }

            $retorno['status'] = true;
            $retorno['dados'] = $fotos_encontradas; // O array 'dados' agora contém TODAS as fotos

            mysqli_free_result($resultado); // Libera a memória do conjunto de resultados
        } else {
            // Este bloco é mais provável para erros se a consulta não for um SELECT ou em caso de erro na obtenção do resultado
            error_log("Erro ao obter resultado da consulta: " . mysqli_stmt_error($stmt));
            $retorno['erro'] = "Erro interno: falha ao processar resultados.";
        }
    } else {
        // Erro na execução da instrução
        error_log("Erro na execução da consulta: " . mysqli_stmt_error($stmt));
        $retorno['erro'] = "Erro ao tentar fazer busca.";
    }

    mysqli_stmt_close($stmt); // Sempre feche o statement no final, independentemente do sucesso da execução

    return $retorno;
}




function PesquisaFoto($conn, $id_user, $termo_busca)
{
    // Inicializa o array de retorno 
    $retorno = [
        'status' => false,
        'erro' => '',
        'dados' => []
    ];


    $sql = "SELECT * FROM imagens WHERE usuario_id = ? AND (titulo LIKE ? OR descricao LIKE ? )";

    $stmt = mysqli_prepare($conn, $sql);

    // Verifica se a preparação da instrução falhou
    if ($stmt === false) {
        error_log("Erro ao preparar a instrução SQL: " . mysqli_error($conn));
        $retorno['erro'] = "Erro interno do servidor ao preparar a busca.";
        return $retorno;
    }

    //  Preparar o termo de busca para usar com LIKE, adicionando os curingas '%'.
    $termo_busca_like = '%' . $termo_busca . '%';

    // Vincular os parâmetros à instrução preparada.
    //    'isss' define os tipos dos parâmetros:
    //    'i' para $id_user (inteiro)
    //    's' para $termo_busca_like (string), repetido para cada coluna que será pesquisada com LIKE.
    $bind = mysqli_stmt_bind_param($stmt, 'iss', $id_user, $termo_busca_like, $termo_busca_like);

    // Verifica se a vinculação dos parâmetros falhou
    if ($bind === false) {
        error_log("Erro ao vincular parâmetros na busca: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt); // Fechar o statement para liberar recursos.
        $retorno['erro'] = "Erro interno do servidor ao vincular dados para a busca.";
        return $retorno;
    }

    // 4. Executar a instrução preparada.
    if (mysqli_execute($stmt)) {
        $resultado_query = mysqli_stmt_get_result($stmt); // Obtém o objeto mysqli_result

        // Verifica se a obtenção dos resultados foi bem-sucedida
        if ($resultado_query) {
            // Inicializa um array vazio para armazenar todos os resultados encontrados.
            $fotos_encontradas = [];

            // Loop para buscar todas as linhas e adicioná-las ao array.
            while ($foto = mysqli_fetch_assoc($resultado_query)) {
                $fotos_encontradas[] = $foto; // Adiciona cada foto ao array
            }

            mysqli_free_result($resultado_query); // Libera a memória do resultado.

            // Verifica se alguma foto foi encontrada
            if (!empty($fotos_encontradas)) {
                $retorno['status'] = true;
                $retorno['dados'] = $fotos_encontradas;
            } else {
                $retorno['status'] = false;
                $retorno['erro'] = "Nenhuma foto encontrada para o termo de busca.";
            }
        } else {
            // Este caso geralmente indica um problema sério após a execução, mas antes de pegar os resultados.
            error_log("Erro ao obter o resultado da busca: " . mysqli_stmt_error($stmt));
            $retorno['erro'] = "Erro ao processar os resultados da busca.";
        }
    } else {
        // Erro na execução da instrução SQL.
        error_log("Erro ao executar a instrução SQL de busca: " . mysqli_stmt_error($stmt));
        $retorno['erro'] = "Erro interno do servidor ao executar a busca.";
    }

    mysqli_stmt_close($stmt); // fechando stmt

    return $retorno; // Retorna o array de status, erro e dados.
}


// função para excluir fotos


function ExcluirFotos($conn, $id_usuario, $id_excluir)
{
    $resultado_excluir = [
        'status' => false,
        'erro' => "",
        'dados' => ""
    ];

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
        //verifica se existe resgitro
        if ($resultado_busca && mysqli_num_rows($resultado_busca) > 0) {

            //associa a pesquisa a variavel 
            $foto_excluir = mysqli_fetch_assoc($resultado_busca);

            //liberando espaço
            mysqli_free_result($resultado_busca);

            // Monta o caminho absoluto completo da imagem no servidor para exclusão
            $caminho_exluir = $_SERVER['DOCUMENT_ROOT'] // Retorna o caminho absoluto da 
                //raiz pública do servidor (ex: C:/xampp/htdocs)
                . $foto_excluir['caminho_arquivo']; // Adiciona o caminho relativo salvo no banco de dados
            // (ex: /galeria/uploads/imagens/arquivo.png)





            // verifica se o caminho do servidor existe 
            if (file_exists($caminho_exluir)) {

                // se existir tenta excluir a foto
                if (unlink($caminho_exluir)) {
                    // sql para excluir a foto do banco de dados e todos os registros dela 
                    $sql_delete = "DELETE FROM imagens WHERE id = ? AND usuario_id = ?";

                    //preparando stmt de delete
                    $stmt_delete = mysqli_prepare($conn, $sql_delete);

                    // atribuindo os parsmetros de consulta
                    $bind = mysqli_stmt_bind_param($stmt_delete, 'ii', $id_excluir, $id_usuario);

                    //verifica se o prepare ocorreou bem
                    if ($bind === false) {
                        mysqli_stmt_close($stmt_delete);
                        $resultado_excluir['status'] = false;
                        $resultado_excluir['erro'] = "Erro ao preparar a conexão.";

                        return $resultado_excluir;
                    }
                    //verifica se a execulsao vai ocorrer 
                    if (mysqli_execute($stmt_delete)) {
                        mysqli_stmt_close($stmt_delete);
                        // atribui o array de resultados 
                        $resultado_excluir['status'] = true;
                        $resultado_excluir['dados'] = "foto excluida.";
                    } else {
                        mysqli_stmt_close($stmt_delete);
                        $resultado_excluir['erro'] = "erro ao excluir foto.";
                    }
                } else {
                    $resultado_excluir['erro'] = "Erro ao deletar foto.";
                }
            } else {
                $resultado_excluir['erro'] = "Foto não encontrada.";
                $resultado_excluir['dados'] = $foto_excluir;
            }
        }
        mysqli_stmt_close($stmt);
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

    $sql = "UPDATE imagens SET titulo = ?, descricao = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    $bind = mysqli_stmt_bind_param($stmt, 'ssi', $title_update, $description_update, $id_update);
    if ($bind === false) {
        $resultado_update['status'] = false;
        $resultado_update['erro'] = "Erro ao preparar a conexão.";
        return $resultado_update;
    }
    if (mysqli_stmt_execute($stmt)) {
        $resultado_update['status'] = true;
        $resultado_update['dados'] = "Alterado com sucesso.";
    } else {
        $resultado_update['status'] = false;
        $resultado_update['erro'] = "Erro ao inserir no banco de dados";
    }
    mysqli_stmt_close($stmt);
    return $resultado_update;
}
