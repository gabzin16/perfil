<?php
require_once '../config.php';
verificaLogin();

function handleProfileUpdate($pdo, $usuario_id, $dados, $arquivos) {
    try {
        // Inicia a transação
        $pdo->beginTransaction();

        // Verifica se já existe um perfil
        $stmt = $pdo->prepare("SELECT id, foto_perfil, curriculo_pdf FROM perfis WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $perfil_existente = $stmt->fetch();

        // Processa uploads se houver
        $uploads = processUploads($arquivos, $perfil_existente);
        if (isset($uploads['erro'])) {
            throw new Exception($uploads['erro']);
        }

        // Remove campos vazios e mescla com uploads
        $dados = array_filter($dados, function($value) {
            return $value !== '' && $value !== null;
        });
        $dados = array_merge($dados, $uploads);

        if ($perfil_existente) {
            // Atualiza perfil existente
            if (!empty($dados)) {
                $campos = [];
                $valores = [];
                
                foreach ($dados as $campo => $valor) {
                    $campos[] = "$campo = ?";
                    $valores[] = $valor;
                }
                
                $valores[] = $usuario_id;
                $sql = "UPDATE perfis SET " . implode(', ', $campos) . ", 
                        perfil_completo = TRUE, 
                        updated_at = NOW() 
                        WHERE usuario_id = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($valores);
            }
        } else {
            // Insere novo perfil
            $dados['usuario_id'] = $usuario_id;
            $dados['perfil_completo'] = true;
            
            $campos = array_keys($dados);
            $placeholders = array_fill(0, count($campos), '?');
            
            $sql = "INSERT INTO perfis (" . implode(', ', $campos) . ") 
                   VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($dados));
        }

        // Confirma as alterações
        $pdo->commit();

        return [
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'uploads' => $uploads,
            'perfil_completo' => true // Garante que o front-end saiba que o perfil está completo
        ];

    } catch (Exception $e) {
        // Desfaz as alterações em caso de erro
        $pdo->rollBack();
        error_log("Erro no profile_handler: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
        ];
    }
}

function processUploads($arquivos, $perfil_existente) {
    $uploads = [];
    
    // Verifica e cria diretórios se necessário
    if (!file_exists('../imgs')) mkdir('../imgs', 0777, true);
    if (!file_exists('../pdfs')) mkdir('../pdfs', 0777, true);

    // Processa foto de perfil
    if (isset($arquivos['foto']) && $arquivos['foto']['error'] === 0) {
        $foto = $arquivos['foto'];
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        
        // Valida extensão
        $permitidos = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $permitidos)) {
            return ['erro' => 'Tipo de imagem não permitido. Use JPG, JPEG ou PNG.'];
        }
        
        // Valida tamanho (5MB)
        if ($foto['size'] > 5 * 1024 * 1024) {
            return ['erro' => 'A imagem deve ter no máximo 5MB.'];
        }
        
        // Valida se é realmente uma imagem
        if (!getimagesize($foto['tmp_name'])) {
            return ['erro' => 'Arquivo não é uma imagem válida.'];
        }
        
        $novo_nome = 'foto_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($foto['tmp_name'], '../imgs/' . $novo_nome)) {
            $uploads['foto_perfil'] = 'imgs/' . $novo_nome;
            
            // Remove foto antiga
            if ($perfil_existente && !empty($perfil_existente['foto_perfil'])) {
                $foto_antiga = '../' . $perfil_existente['foto_perfil'];
                if (file_exists($foto_antiga)) {
                    @unlink($foto_antiga);
                }
            }
        }
    }
    
    // Processa currículo PDF
    if (isset($arquivos['curriculo']) && $arquivos['curriculo']['error'] === 0) {
        $curriculo = $arquivos['curriculo'];
        $ext = strtolower(pathinfo($curriculo['name'], PATHINFO_EXTENSION));
        
        // Valida se é PDF
        if ($ext !== 'pdf') {
            return ['erro' => 'O currículo deve estar em formato PDF.'];
        }
        
        // Valida tamanho (10MB)
        if ($curriculo['size'] > 10 * 1024 * 1024) {
            return ['erro' => 'O PDF deve ter no máximo 10MB.'];
        }
        
        $novo_nome = 'curriculo_' . uniqid() . '.pdf';
        if (move_uploaded_file($curriculo['tmp_name'], '../pdfs/' . $novo_nome)) {
            $uploads['curriculo_pdf'] = 'pdfs/' . $novo_nome;
            
            // Remove currículo antigo
            if ($perfil_existente && !empty($perfil_existente['curriculo_pdf'])) {
                $curriculo_antigo = '../' . $perfil_existente['curriculo_pdf'];
                if (file_exists($curriculo_antigo)) {
                    @unlink($curriculo_antigo);
                }
            }
        }
    }
    
    return $uploads;
}

// Processa requisições
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('memory_limit', '256M');
    
    $resultado = handleProfileUpdate($pdo, $_SESSION['usuario_id'], $_POST, $_FILES);
    
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

// Processa exclusão de arquivos
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $tipo = $_GET['tipo'] ?? '';
    
    try {
        if ($tipo === 'foto' || $tipo === 'curriculo') {
            $campo = $tipo === 'foto' ? 'foto_perfil' : 'curriculo_pdf';
            
            // Busca arquivo atual
            $stmt = $pdo->prepare("SELECT $campo FROM perfis WHERE usuario_id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $arquivo = $stmt->fetchColumn();
            
            // Remove arquivo
            if ($arquivo && file_exists('../' . $arquivo)) {
                unlink('../' . $arquivo);
            }
            
            // Atualiza banco
            $stmt = $pdo->prepare("UPDATE perfis SET $campo = NULL WHERE usuario_id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            
            $resultado = [
                'success' => true,
                'message' => 'Arquivo removido com sucesso'
            ];
        } else {
            $resultado = [
                'success' => false,
                'message' => 'Tipo de arquivo inválido'
            ];
        }
    } catch (Exception $e) {
        error_log("Erro ao remover arquivo: " . $e->getMessage());
        $resultado = [
            'success' => false,
            'message' => 'Erro ao remover arquivo: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}
?>