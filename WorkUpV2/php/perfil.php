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

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS -->
    <link rel="stylesheet" href="./css/perfil.css">

    <link rel="stylesheet" href="../css/header-styles.css">

    <title>Perfil - WorkUp</title>
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

<!-- _________________________________ -->
    <div class="main-container">
        <div class="profile-section">
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
                <br>
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
                <i class="fas fa-graduation-cap fa-xl"></i>
                <span>Escolaridade</span>
                <i class="edit-icon fa-solid fa-pen-clip fa-lg"></i>
            </div>
            <div id="escolaridade-form" class="info-input">
                <input type="text" placeholder="Adicionar escolaridade">
                <button class="adicionar-btn">Adicionar</button>
                <div id="escolaridade-items"></div>
            </div>

            <!-- Competências -->
            <div class="info-card" onclick="toggleForm('competencias')">
                <i class="fas fa-star fa-xl"></i>
                <span>Competências</span>
                <i class="edit-icon fa-solid fa-pen-clip fa-lg"></i>
            </div>
            <div id="competencias-form" class="info-input">
                <input type="text" placeholder="Adicionar competência">
                <button class="adicionar-btn">Adicionar</button>
                <div id="competencias-items"></div>
            </div>

            <!-- Certificações -->
            <div class="info-card" onclick="toggleForm('certificacoes')">
                <i class="fas fa-certificate fa-xl"></i>
                <span>Certificações</span>
                <i class="edit-icon fa-solid fa-pen-clip fa-lg"></i>
            </div>
            <div id="certificacoes-form" class="info-input">
                <input type="text" placeholder="Adicionar certificação">
                <button class="adicionar-btn">Adicionar</button>
                <div id="certificacoes-items"></div>
            </div>

            <!-- Idiomas -->
            <div class="info-card" onclick="toggleForm('idiomas')">
                <i class="fas fa-globe-americas fa-xl"></i>
                <span>Idiomas</span>
                <i class="edit-icon fa-solid fa-pen-clip fa-lgn"></i>
            </div>
            <div id="idiomas-form" class="info-input">
                <input type="text" placeholder="Adicionar idioma">
                <button class="adicionar-btn">Adicionar</button>
                <div id="idiomas-items"></div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <ul>
                    <li><a href="./politica.html">Política de privacidade</a></li>
                    <li><a href="./perguntas_frequentes.html">Central de atendimento</a></li>
                    <li><a href="./sobre.html">Sobre nós</a></li>
                </ul>
            </div>
      
            <div class="footer-logo">
                <img src="../assets/logo/w_transp.svg" alt="Logo W" />
            </div>
      
            <div class="footer-social">
                <p>Nossas redes sociais:</p>
                <ul class="social-icons">
                    <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-whatsapp"></i></a></li>
                </ul>
            </div>
        </div>
    
        <div class="footer-bottom">
            <p>Copyright © 2024 WorkUp Inc. Todos os direitos reservados. WorkUp Brasil Ltda</p>
        </div>
    </footer>

    <script src="js/profile.js"></script>

</body>
</html>