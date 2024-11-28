<?php
require_once '../config.php';
verificaLogin();

/**
 * Função para manipular o upload de arquivos
 */
function handleFileUpload($file, $tipo, $pdo) {
    // Verifica se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Erro no upload: ' . $file['error']
        ];
    }

    // Verifica e cria diretórios se necessário
    $uploadDir = $tipo === 'foto' ? '../imgs/' : '../pdfs/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Gera nome único para o arquivo
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nomeArquivo = uniqid($tipo . '_') . '.' . $ext;
    $caminhoCompleto = $uploadDir . $nomeArquivo;

    // Validação por tipo de arquivo
    if ($tipo === 'foto') {
        // Verifica tipo de imagem
        $permitidos = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $permitidos)) {
            return [
                'success' => false,
                'message' => 'Tipo de imagem não permitido. Use JPG, JPEG ou PNG.'
            ];
        }

        // Valida dimensões e tamanho
        $imagemInfo = getimagesize($file['tmp_name']);
        if ($imagemInfo === false) {
            return [
                'success' => false,
                'message' => 'Arquivo não é uma imagem válida.'
            ];
        }

        // Limita tamanho a 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'A imagem deve ter no máximo 5MB.'
            ];
        }

    } elseif ($tipo === 'curriculo') {
        // Verifica se é PDF
        if ($ext !== 'pdf') {
            return [
                'success' => false,
                'message' => 'O currículo deve estar em formato PDF.'
            ];
        }

        // Limita tamanho a 10MB
        if ($file['size'] > 10 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'O PDF deve ter no máximo 10MB.'
            ];
        }
    }

    // Move o arquivo
    if (move_uploaded_file($file['tmp_name'], $caminhoCompleto)) {
        // Verifica se existe arquivo antigo para deletar
        try {
            if ($tipo === 'foto') {
                $stmt = $pdo->prepare("SELECT foto_perfil FROM perfis WHERE usuario_id = ?");
            } else {
                $stmt = $pdo->prepare("SELECT curriculo_pdf FROM perfis WHERE usuario_id = ?");
            }
            
            $stmt->execute([$_SESSION['usuario_id']]);
            $resultado = $stmt->fetch();
            
            if ($resultado) {
                $arquivoAntigo = $resultado[$tipo === 'foto' ? 'foto_perfil' : 'curriculo_pdf'];
                if ($arquivoAntigo && file_exists('../' . $arquivoAntigo)) {
                    unlink('../' . $arquivoAntigo);
                }
            }

            return [
                'success' => true,
                'path' => str_replace('../', '', $caminhoCompleto)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao atualizar registro: ' . $e->getMessage()
            ];
        }
    }

    return [
        'success' => false,
        'message' => 'Erro ao mover o arquivo.'
    ];
}

// Processa upload quando receber uma requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Nenhum arquivo recebido'];

    // Upload de foto
    if (isset($_FILES['foto'])) {
        $response = handleFileUpload($_FILES['foto'], 'foto', $pdo);
    }
    // Upload de currículo
    elseif (isset($_FILES['curriculo'])) {
        $response = handleFileUpload($_FILES['curriculo'], 'curriculo', $pdo);
    }

    // Retorna resposta em JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>