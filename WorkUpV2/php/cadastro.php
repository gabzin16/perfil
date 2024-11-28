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
    <title>Cadastre-se - WorkUp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background-color: #FFE814;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: #000;
            text-decoration: none;
            font-weight: bold;
            font-size: 24px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #000;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .cadastro-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #FFE814;
            border: none;
            border-radius: 5px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #FFD700;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: #666;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #eee;
        }
    </style>
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