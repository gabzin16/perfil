<?php
require_once 'config.php';
verificaLogin();

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT u.*, p.* FROM usuarios u 
                       LEFT JOIN perfis p ON u.id = p.usuario_id 
                       WHERE u.id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - WorkUp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background-color: #ffde59;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: #000;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
        }

        .header-nav a {
            color: #000;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            flex: 1;
        }

        /* Profile Section */
        .profile-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        .voltar-btn {
            display: inline-block;
            background: #ffde59;
            padding: 5px 15px;
            border-radius: 15px;
            color: #000;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .profile-header {
            margin-top: 20px;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-photo input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .alterar-foto-btn {
            background: #ffde59;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 150px;
            font-size: 14px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Side Section */
        .side-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .info-card i {
            margin-right: 10px;
            font-size: 20px;
        }

        .info-card .edit-icon {
            margin-left: auto;
        }

        .info-input {
            display: none;
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: -10px;
        }

        .info-input.active {
            display: block;
        }

        .info-input input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .adicionar-btn {
            background: #ffde59;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        /* Currículo Area */
        .curriculo-area {
            background: #ffde59;
            width: 150px;
            height: 150px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-top: 20px;
        }

        .curriculo-area input[type="file"] {
            display: none;
        }

        .curriculo-area label {
            text-align: center;
            font-size: 14px;
            cursor: pointer;
        }

        /* Save Button */
        .save-btn {
            background: #ffde59;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        /* Message Styles */
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        footer {
            background: white;
            padding: 30px 20px;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .footer-links a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-social {
            margin: 20px 0;
        }

        .footer-social span {
            margin-right: 10px;
            color: #666;
        }

        .footer-social a {
            text-decoration: none;
            color: purple;
            margin: 0 5px;
        }

        .footer-copyright {
            color: #666;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            .search-bar {
                max-width: 100%;
                margin: 10px 0;
            }

            .header-nav {
                width: 100%;
                text-align: center;
            }

            .header-nav a {
                margin: 0 10px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">WorkUp</a>
            <div class="search-bar">
                <input type="text" placeholder="Buscar vagas...">
            </div>
            <nav class="header-nav">
                <a href="#">Vagas</a>
                <a href="#">Central de atendimento</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <div class="profile-section">
            <a href="#" class="voltar-btn">Voltar</a>

            <form id="profile-form" method="POST" enctype="multipart/form-data">
                <div class="profile-photo">
                    <img src="<?php echo !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'placeholder.jpg'; ?>" 
                         alt="Foto de perfil" id="photo-preview">
                    <input type="file" name="foto" accept="image/*" id="foto-input">
                </div>
                <button type="button" class="alterar-foto-btn" onclick="document.getElementById('foto-input').click()">
                    Alterar foto
                </button>

                <div class="form-group">
                    <label>Nome completo</label>
                    <input type="text" name="nome_completo" value="<?php echo $usuario['nome_completo'] ?? ''; ?>" placeholder="Nome e sobrenome">
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?php echo $usuario['email'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="telefone" id="telefone" value="<?php echo $usuario['telefone'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="endereco" value="<?php echo $usuario['endereco'] ?? ''; ?>" placeholder="Rua/Logradouro">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Número</label>
                        <input type="text" name="numero" value="<?php echo $usuario['numero'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" name="cep" id="cep" value="<?php echo $usuario['cep'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Complemento</label>
                    <input type="text" name="complemento" value="<?php echo $usuario['complemento'] ?? ''; ?>" placeholder="Ex: Apto, bloco, casa">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" id="estado">
                            <option value="">Selecione</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cidade</label>
                        <select name="cidade" id="cidade">
                            <option value="">Selecione</option>
                        </select>
                    </div>
                </div>

                <div class="curriculo-area">
                    <input type="file" name="curriculo" accept=".pdf" id="curriculo-input">
                    <label for="curriculo-input">
                        <?php if (!empty($usuario['curriculo_pdf'])): ?>
                            Currículo atual: <?php echo basename($usuario['curriculo_pdf']); ?>
                        <?php else: ?>
                            Anexar currículo (PDF)
                        <?php endif; ?>
                    </label>
                </div>

                <button type="submit" id="save-btn" class="save-btn">
                    <?php echo $usuario['perfil_completo'] ? 'Editar' : 'Salvar'; ?>
                </button>
            </form>
        </div>

        <div class="side-section">
            <!-- Escolaridade -->
            <div class="info-card" onclick="toggleForm('escolaridade')">
                <i>🎓</i>
                <span>Escolaridade</span>
                <i class="edit-icon">✏️</i>
            </div>
            <div id="escolaridade-form" class="info-input">
                <input type="text" placeholder="Adicionar escolaridade">
                <button class="adicionar-btn">Adicionar</button>
                <div id="escolaridade-items"></div>
            </div>

            <!-- Competências -->
            <div class="info-card" onclick="toggleForm('competencias')">
                <i>⭐</i>
                <span>Competências</span>
                <i class="edit-icon">✏️</i>
            </div>
            <div id="competencias-form" class="info-input">
                <input type="text" placeholder="Adicionar competência">
                <button class="adicionar-btn">Adicionar</button>
                <div id="competencias-items"></div>
            </div>

            <!-- Certificações -->
            <div class="info-card" onclick="toggleForm('certificacoes')">
                <i>🏅</i>
                <span>Certificações</span>
                <i class="edit-icon">✏️</i>
            </div>
            <div id="certificacoes-form" class="info-input">
                <input type="text" placeholder="Adicionar certificação">
                <button class="adicionar-btn">Adicionar</button>
                <div id="certificacoes-items"></div>
            </div>

            <!-- Idiomas -->
            <div class="info-card" onclick="toggleForm('idiomas')">
                <i>🌎</i>
                <span>Idiomas</span>
                <i class="edit-icon">✏️</i>
            </div>
            <div id="idiomas-form" class="info-input">
                <input type="text" placeholder="Adicionar idioma">
                <button class="adicionar-btn">Adicionar</button>
                <div id="idiomas-items"></div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#">Política de privacidade</a>
                <a href="#">Termos legais</a>
                <a href="#">Mapa do site</a>
            </div>
            <div class="footer-social">
                <span>Nossas redes sociais:</span>
                <a href="#">Instagram</a>
                <a href="#">Facebook</a>
                <a href="#">YouTube</a>
                <a href="#">WhatsApp</a>
            </div>
            <div class="footer-logo">
                <img src="images/workup-logo.png" alt="WorkUp">
            </div>
            <div class="footer-copyright">
                Copyright © 2024 WorkUp Inc. Todos os direitos reservados. WorkUp Brasil Ltda
            </div>
        </div>
    </footer>

    <script src="js/profile.js"></script>

</body>
</html>