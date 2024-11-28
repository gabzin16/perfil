<?php
// Reportar todos os erros exceto notices
error_reporting(E_ALL & ~E_NOTICE);

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'workup_db');

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão
session_start();

// Estabelece a conexão com o banco de dados
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Função para verificar se o usuário está logado
function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Função para limpar inputs
function limparInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para verificar se é uma requisição AJAX
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Função para verificar e criar diretórios necessários
function verificaDiretorios() {
    $diretorios = ['imgs', 'pdfs'];
    
    foreach ($diretorios as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            
            // Cria arquivo .htaccess para proteção
            $htaccess = $dir . '/.htaccess';
            if ($dir === 'imgs') {
                file_put_contents($htaccess, 
                    "# Permitir apenas imagens\n" .
                    "<FilesMatch \"\.(?i:(jpg|jpeg|png|gif))$\">\n" .
                    "    Order Allow,Deny\n" .
                    "    Allow from all\n" .
                    "</FilesMatch>"
                );
            } elseif ($dir === 'pdfs') {
                file_put_contents($htaccess,
                    "# Permitir apenas PDFs\n" .
                    "<FilesMatch \"\.(?i:pdf)$\">\n" .
                    "    Order Allow,Deny\n" .
                    "    Allow from all\n" .
                    "</FilesMatch>"
                );
            }
        }
    }
}

// Chama a função para verificar diretórios
verificaDiretorios();

// Configurações de upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf']);

// Função para validar upload
function validaUpload($file, $tipo) {
    // Verifica se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['erro' => 'Erro no upload do arquivo.'];
    }

    // Verifica tamanho
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['erro' => 'Arquivo muito grande. Tamanho máximo: 5MB.'];
    }

    // Verifica tipo
    if ($tipo === 'imagem' && !in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        return ['erro' => 'Tipo de imagem não permitido. Use JPG, PNG ou GIF.'];
    }

    if ($tipo === 'documento' && !in_array($file['type'], ALLOWED_DOC_TYPES)) {
        return ['erro' => 'Apenas arquivos PDF são permitidos.'];
    }

    return ['sucesso' => true];
}

// Função para gerar resposta JSON
function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Função para log de erros personalizado
function logError($message, $file = null, $line = null) {
    $logFile = __DIR__ . '/error.log';
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message";
    
    if ($file && $line) {
        $logMessage .= " in $file on line $line";
    }
    
    error_log($logMessage . PHP_EOL, 3, $logFile);
}

// Define caracteres UTF-8 para a conexão
header('Content-Type: text/html; charset=utf-8');
?>