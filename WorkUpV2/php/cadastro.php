<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nome = limparInput($_POST['usuario']);
        $email = limparInput($_POST['email']);
        $idade = limparInput($_POST['idade']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        
        // Inicia transação
        $pdo->beginTransaction();
        
        // Verifica se o email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $erro = "Este email já está cadastrado";
        } else {
            // Insere novo usuário
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, idade, senha) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $idade, $senha]);
            
            // Pega o ID do usuário recém criado
            $usuario_id = $pdo->lastInsertId();
            
            // Cria um perfil vazio para o usuário
            $stmt = $pdo->prepare("INSERT INTO perfis (usuario_id, nome_completo, email) VALUES (?, ?, ?)");
            $stmt->execute([$usuario_id, $nome, $email]);
            
            // Confirma as operações
            $pdo->commit();
            
            // Redireciona para o login
            header("Location: login.php");
            exit;
        }
    } catch(PDOException $e) {
        // Em caso de erro, desfaz as alterações
        $pdo->rollBack();
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
     <link rel="stylesheet" href="./css/cadastro.css">
     
    <title>Cadastre-se - WorkUp</title>

</head>
<body>
    <header>
        <a href="index.php" class="logo">WorkUp</a>
        <nav class="nav-links">
            <a href="#">Vagas</a>
            <a href="#">Central de atendimento</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="cadastro-container">
            <h2>Cadastre-se</h2>
            
            <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        required 
                        value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="idade">Idade</label>
                    <input 
                        type="number" 
                        id="idade" 
                        name="idade" 
                        required 
                        min="16" 
                        max="100"
                        value="<?php echo isset($_POST['idade']) ? htmlspecialchars($_POST['idade']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit">Cadastre-se</button>
            </form>
            
            <div class="login-link">
                <p>Já possui uma conta? <a href="login.php">Entre aqui</a></p>
            </div>
        </div>
    </div>

    <footer>
        <p>Copyright © 2024 WorkUp Inc. Todos os direitos reservados. WorkUp Brasil Ltda</p>
    </footer>
</body>
</html>