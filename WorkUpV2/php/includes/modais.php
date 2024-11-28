<?php
// Este arquivo contém todos os modais utilizados no sistema
?>

<!-- Modal de Escolaridade -->
<div id="modalEscolaridade" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Adicionar Escolaridade</h2>
        <form id="formEscolaridade">
            <div class="form-group">
                <label for="nivel">Nível</label>
                <select name="nivel" id="nivel" required>
                    <option value="">Selecione</option>
                    <option value="Ensino Fundamental">Ensino Fundamental</option>
                    <option value="Ensino Médio">Ensino Médio</option>
                    <option value="Ensino Técnico">Ensino Técnico</option>
                    <option value="Graduação">Graduação</option>
                    <option value="Pós-graduação">Pós-graduação</option>
                    <option value="Mestrado">Mestrado</option>
                    <option value="Doutorado">Doutorado</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="instituicao">Instituição</label>
                <input type="text" name="instituicao" id="instituicao" required>
            </div>
            
            <div class="form-group">
                <label for="curso">Curso</label>
                <input type="text" name="curso" id="curso" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ano_inicio">Ano Início</label>
                    <input type="number" name="ano_inicio" id="ano_inicio" required min="1950" max="<?php echo date('Y'); ?>">
                </div>
                <div class="form-group">
                    <label for="ano_conclusao">Ano Conclusão</label>
                    <input type="number" name="ano_conclusao" id="ano_conclusao" min="1950" max="<?php echo date('Y'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="em_andamento" id="em_andamento">
                    Em andamento
                </label>
            </div>

            <button type="submit" class="btn-save">Salvar</button>
        </form>
    </div>
</div>

<!-- Modal de Competências -->
<div id="modalCompetencias" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Adicionar Competência</h2>
        <form id="formCompetencias">
            <div class="form-group">
                <label for="competencia">Competência</label>
                <input type="text" name="competencia" id="competencia" required>
            </div>
            
            <div class="form-group">
                <label for="nivel_competencia">Nível</label>
                <select name="nivel" id="nivel_competencia" required>
                    <option value="">Selecione</option>
                    <option value="Básico">Básico</option>
                    <option value="Intermediário">Intermediário</option>
                    <option value="Avançado">Avançado</option>
                    <option value="Especialista">Especialista</option>
                </select>
            </div>

            <button type="submit" class="btn-save">Salvar</button>
        </form>
    </div>
</div>

<!-- Modal de Certificações -->
<div id="modalCertificacoes" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Adicionar Certificação</h2>
        <form id="formCertificacoes">
            <div class="form-group">
                <label for="nome_certificacao">Nome da Certificação</label>
                <input type="text" name="nome" id="nome_certificacao" required>
            </div>
            
            <div class="form-group">
                <label for="instituicao_certificacao">Instituição</label>
                <input type="text" name="instituicao" id="instituicao_certificacao" required>
            </div>
            
            <div class="form-group">
                <label for="ano_conclusao_certificacao">Ano de Conclusão</label>
                <input type="number" name="ano_conclusao" id="ano_conclusao_certificacao" required 
                       min="1950" max="<?php echo date('Y'); ?>">
            </div>
            
            <div class="form-group">
                <label for="link_verificacao">Link de Verificação (opcional)</label>
                <input type="url" name="link_verificacao" id="link_verificacao" 
                       placeholder="https://exemplo.com/verificar">
            </div>

            <button type="submit" class="btn-save">Salvar</button>
        </form>
    </div>
</div>

<!-- Modal de Idiomas -->
<div id="modalIdiomas" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Adicionar Idioma</h2>
        <form id="formIdiomas">
            <div class="form-group">
                <label for="idioma">Idioma</label>
                <select name="idioma" id="idioma" required>
                    <option value="">Selecione</option>
                    <option value="Português">Português</option>
                    <option value="Inglês">Inglês</option>
                    <option value="Espanhol">Espanhol</option>
                    <option value="Francês">Francês</option>
                    <option value="Alemão">Alemão</option>
                    <option value="Italiano">Italiano</option>
                    <option value="Japonês">Japonês</option>
                    <option value="Mandarim">Mandarim</option>
                    <option value="Russo">Russo</option>
                    <option value="Árabe">Árabe</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="nivel_idioma">Nível</label>
                <select name="nivel" id="nivel_idioma" required>
                    <option value="">Selecione</option>
                    <option value="Básico">Básico</option>
                    <option value="Intermediário">Intermediário</option>
                    <option value="Avançado">Avançado</option>
                    <option value="Fluente">Fluente</option>
                    <option value="Nativo">Nativo</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="certificacao_idioma">Certificação (opcional)</label>
                <input type="text" name="certificacao" id="certificacao_idioma" 
                       placeholder="Ex: TOEFL, DELE, DELF">
            </div>

            <button type="submit" class="btn-save">Salvar</button>
        </form>
    </div>
</div>

<style>
/* Estilos dos Modais */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: #fff;
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #666;
}

.close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="url"],
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

.btn-save {
    width: 100%;
    padding: 10px;
    background-color: #FFE814;
    border: none;
    border-radius: 4px;
    color: #000;
    font-weight: bold;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.btn-save:hover {
    background-color: #FFD700;
}

/* Responsividade */
@media (max-width: 600px) {
    .modal-content {
        margin: 20px;
        width: auto;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>