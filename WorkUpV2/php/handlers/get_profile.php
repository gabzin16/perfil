<?php
require_once '../config.php';
verificaLogin();

// Inicia a transação
try {
    $pdo->beginTransaction();

    // Busca dados completos do usuário
    $stmt = $pdo->prepare("
        SELECT 
            u.*,
            p.*,
            u.email as email_login,
            p.email as email_perfil
        FROM usuarios u
        LEFT JOIN perfis p ON u.id = p.usuario_id
        WHERE u.id = ?
    ");
    
    $stmt->execute([$_SESSION['usuario_id']]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se não houver um perfil, cria um
    if (!$stmt->rowCount() || !$dados['perfil_completo']) {
        $stmtCheck = $pdo->prepare("SELECT id FROM perfis WHERE usuario_id = ?");
        $stmtCheck->execute([$_SESSION['usuario_id']]);
        
        if (!$stmtCheck->rowCount()) {
            $stmtInsert = $pdo->prepare("
                INSERT INTO perfis (usuario_id, nome_completo, email) 
                VALUES (?, ?, ?)
            ");
            $stmtInsert->execute([
                $_SESSION['usuario_id'],
                $dados['nome'] ?? null,
                $dados['email_login'] ?? null
            ]);
            
            // Busca os dados novamente
            $stmt->execute([$_SESSION['usuario_id']]);
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Busca escolaridade
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nivel,
            instituicao,
            curso,
            ano_inicio,
            ano_conclusao,
            em_andamento
        FROM escolaridade 
        WHERE usuario_id = ? 
        ORDER BY 
            CASE 
                WHEN em_andamento = 1 THEN 1 
                ELSE 0 
            END DESC,
            ano_inicio DESC,
            id DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $escolaridade = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca competências
    $stmt = $pdo->prepare("
        SELECT 
            id,
            competencia,
            nivel
        FROM competencias 
        WHERE usuario_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca certificações
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nome,
            instituicao,
            ano_conclusao,
            link_verificacao
        FROM certificacoes 
        WHERE usuario_id = ? 
        ORDER BY ano_conclusao DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $certificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca idiomas
    $stmt = $pdo->prepare("
        SELECT 
            id,
            idioma,
            nivel,
            certificacao
        FROM idiomas 
        WHERE usuario_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $idiomas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Confirma a transação
    $pdo->commit();

    // Verifica arquivos
    if (!empty($dados['foto_perfil']) && !file_exists('../' . $dados['foto_perfil'])) {
        $dados['foto_perfil'] = null;
    }
    if (!empty($dados['curriculo_pdf']) && !file_exists('../' . $dados['curriculo_pdf'])) {
        $dados['curriculo_pdf'] = null;
    }

    // Formata datas
    if (!empty($dados['data_nascimento'])) {
        $dados['data_nascimento'] = date('Y-m-d', strtotime($dados['data_nascimento']));
    }

    // Remove campos sensíveis
    unset($dados['senha']);
    unset($dados['created_at']);
    unset($dados['updated_at']);

    // Monta resposta
    $response = [
        'success' => true,
        'profile' => [
            'id' => $dados['id'],
            'nome' => $dados['nome'],
            'nome_completo' => $dados['nome_completo'],
            'email' => $dados['email_perfil'] ?? $dados['email_login'],
            'idade' => $dados['idade'],
            'telefone' => $dados['telefone'],
            'endereco' => $dados['endereco'],
            'numero' => $dados['numero'],
            'complemento' => $dados['complemento'],
            'cep' => $dados['cep'],
            'estado' => $dados['estado'],
            'cidade' => $dados['cidade'],
            'data_nascimento' => $dados['data_nascimento'],
            'genero' => $dados['genero'],
            'estado_civil' => $dados['estado_civil'],
            'orientacao_sexual' => $dados['orientacao_sexual'],
            'necessidades_especiais' => $dados['necessidades_especiais'],
            'area_atuacao' => $dados['area_atuacao'],
            'foto_perfil' => $dados['foto_perfil'] ?? '/images/default-profile.png',
            'curriculo_pdf' => $dados['curriculo_pdf'],
            'perfil_completo' => (bool)$dados['perfil_completo']
        ],
        'escolaridade' => $escolaridade,
        'competencias' => $competencias,
        'certificacoes' => $certificacoes,
        'idiomas' => $idiomas
    ];

} catch(PDOException $e) {
    // Em caso de erro, desfaz a transação
    $pdo->rollBack();

    $response = [
        'success' => false,
        'message' => 'Erro ao buscar dados do perfil',
        'error' => $e->getMessage()
    ];

    // Log do erro
    error_log("Erro em get_profile.php: " . $e->getMessage());
}

// Retorna a resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);
?>