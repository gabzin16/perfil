<?php
require_once 'config.php';

// Verifica se já está logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: perfil.php");
    exit;
}

// Processa o login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = limparInput($_POST['email']);
    $senha = $_POST['senha'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($row = $stmt->fetch()) {
            if (password_verify($senha, $row['senha'])) {
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['usuario_nome'] = $row['nome'];
                header("Location: perfil.php");
                exit;
            } else {
                $erro = "Senha incorreta";
            }
        } else {
            $erro = "Usuário não encontrado";
        }
    } catch(PDOException $e) {
        $erro = "Erro ao realizar login: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WorkUp</title>
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

        .login-container {
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
            margin-bottom: 15px;
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

        .register-link {
            text-align: center;
            font-size: 14px;
        }

        .register-link a {
            color: #666;
            text-decoration: none;
        }

        .register-link a:hover {
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

        /* Responsividade */
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
                padding: 20px;
            }

            header {
                padding: 10px;
                flex-direction: column;
                gap: 10px;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
            }
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
        <div class="login-container">
            <h2>Entrar</h2>
            
            <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
            
            <form method="POST" action="">
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
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit">Entrar</button>
                
                <div class="register-link">
                    <p>Não possui uma conta? <a href="cadastro.php">Se inscreva agora!</a></p>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>Copyright © 2024 WorkUp Inc. Todos os direitos reservados. WorkUp Brasil Ltda</p>
    </footer>
</body>
</html>