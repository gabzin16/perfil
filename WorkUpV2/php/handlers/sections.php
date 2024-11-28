<?php
require_once '../config.php';
verificaLogin();

// Verifica se a requisição é POST ou GET
$method = $_SERVER['REQUEST_METHOD'];
$section = $_GET['section'] ?? '';
$action = $_GET['action'] ?? 'add';

// Função para buscar itens de uma seção
function getItems($section, $usuario_id) {
    global $pdo;
    
    try {
        switch($section) {
            case 'escolaridade':
                $stmt = $pdo->prepare("SELECT * FROM escolaridade WHERE usuario_id = ? ORDER BY ano_inicio DESC");
                break;
            case 'competencias':
                $stmt = $pdo->prepare("SELECT * FROM competencias WHERE usuario_id = ? ORDER BY created_at DESC");
                break;
            case 'certificacoes':
                $stmt = $pdo->prepare("SELECT * FROM certificacoes WHERE usuario_id = ? ORDER BY ano_conclusao DESC");
                break;
            case 'idiomas':
                $stmt = $pdo->prepare("SELECT * FROM idiomas WHERE usuario_id = ? ORDER BY created_at DESC");
                break;
            default:
                return ['success' => false, 'message' => 'Seção inválida'];
        }
        
        $stmt->execute([$usuario_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Atualiza o status do perfil como completo
        $stmtUpdate = $pdo->prepare("UPDATE perfis SET perfil_completo = TRUE WHERE usuario_id = ?");
        $stmtUpdate->execute([$usuario_id]);

        return ['success' => true, 'items' => $items];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao buscar itens: ' . $e->getMessage()];
    }
}

// Função para adicionar item
function addItem($section, $data, $usuario_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        switch($section) {
            case 'escolaridade':
                $stmt = $pdo->prepare("INSERT INTO escolaridade (usuario_id, nivel, instituicao, curso, ano_inicio, ano_conclusao, em_andamento) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $usuario_id,
                    $data['nivel'] ?? $data['valor'], // Aceita tanto 'nivel' quanto 'valor'
                    $data['instituicao'] ?? '',
                    $data['curso'] ?? '',
                    $data['ano_inicio'] ?? null,
                    $data['ano_conclusao'] ?? null,
                    isset($data['em_andamento']) ? 1 : 0
                ]);
                break;
                
            case 'competencias':
                $stmt = $pdo->prepare("INSERT INTO competencias (usuario_id, competencia, nivel) VALUES (?, ?, ?)");
                $stmt->execute([
                    $usuario_id,
                    $data['competencia'] ?? $data['valor'], // Aceita tanto 'competencia' quanto 'valor'
                    $data['nivel'] ?? 'Intermediário'
                ]);
                break;
                
            case 'certificacoes':
                $stmt = $pdo->prepare("INSERT INTO certificacoes (usuario_id, nome, instituicao, ano_conclusao) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $usuario_id,
                    $data['nome'] ?? $data['valor'], // Aceita tanto 'nome' quanto 'valor'
                    $data['instituicao'] ?? '',
                    $data['ano_conclusao'] ?? date('Y')
                ]);
                break;
                
            case 'idiomas':
                $stmt = $pdo->prepare("INSERT INTO idiomas (usuario_id, idioma, nivel) VALUES (?, ?, ?)");
                $stmt->execute([
                    $usuario_id,
                    $data['idioma'] ?? $data['valor'], // Aceita tanto 'idioma' quanto 'valor'
                    $data['nivel'] ?? 'Básico'
                ]);
                break;
                
            default:
                return ['success' => false, 'message' => 'Seção inválida'];
        }

        // Atualiza o status do perfil como completo
        $stmtUpdate = $pdo->prepare("UPDATE perfis SET perfil_completo = TRUE WHERE usuario_id = ?");
        $stmtUpdate->execute([$usuario_id]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Item adicionado com sucesso'];
    } catch(PDOException $e) {
        $pdo->rollBack();
        error_log("Erro ao adicionar item: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao adicionar item: ' . $e->getMessage()];
    }
}

// Função para deletar item
function deleteItem($section, $id, $usuario_id) {
    global $pdo;
    
    try {
        switch($section) {
            case 'escolaridade':
                $stmt = $pdo->prepare("DELETE FROM escolaridade WHERE id = ? AND usuario_id = ?");
                break;
            case 'competencias':
                $stmt = $pdo->prepare("DELETE FROM competencias WHERE id = ? AND usuario_id = ?");
                break;
            case 'certificacoes':
                $stmt = $pdo->prepare("DELETE FROM certificacoes WHERE id = ? AND usuario_id = ?");
                break;
            case 'idiomas':
                $stmt = $pdo->prepare("DELETE FROM idiomas WHERE id = ? AND usuario_id = ?");
                break;
            default:
                return ['success' => false, 'message' => 'Seção inválida'];
        }
        
        $stmt->execute([$id, $usuario_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Item excluído com sucesso'];
        } else {
            return ['success' => false, 'message' => 'Item não encontrado ou sem permissão'];
        }
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao excluir item: ' . $e->getMessage()];
    }
}

// Processa a requisição
header('Content-Type: application/json');
error_log("Requisição recebida: " . $method . " - Section: " . $section . " - Action: " . $action);

if ($method === 'GET' && $action === 'list') {
    echo json_encode(getItems($section, $_SESSION['usuario_id']));
} 
elseif ($method === 'POST') {
    $input = file_get_contents('php://input');
    error_log("Dados recebidos: " . $input);
    $data = json_decode($input, true);
    echo json_encode(addItem($section, $data, $_SESSION['usuario_id']));
} 
elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? 0;
    echo json_encode(deleteItem($section, $id, $_SESSION['usuario_id']));
} 
else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não suportado'
    ]);
}
?>