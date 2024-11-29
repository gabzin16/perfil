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

    <!-- CSS -->
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="../css/header-styles.css">

    <title>Login - WorkUp</title>

</head>
<body>

<!-- HEADER -->
<header class="header-content">
        <div class="logo-header">
            <a href="../html/home.html">
                <img src="../assets/logo/WorkUP_black.svg" alt="Logo WorkUp">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="../html/vagas.html">Vagas</a></li>
                <li><a href="../phpperguntas_frequentes.php">Central de atendimento</a></li>
                <a href="./logout.php"><i  class="fa-solid fa-arrow-right-from-bracket" style="color: #000000;"></i></a>
            </ul>
        </nav>
    </header>
    <br>

    <!-- ______________________________________________________ -->
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