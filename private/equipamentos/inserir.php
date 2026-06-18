<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_interno    = $_POST["codigo_interno"]          ?? "";
    $designacao        = $_POST["designacao"]              ?? "";
    $categoria         = $_POST["categoria"]              ?? "";
    $marca             = $_POST["marca"]                  ?? "";
    $modelo            = $_POST["modelo"]                 ?? "";
    $numero_serie      = $_POST["numero_serie"]           ?? "";
    $fabricante        = $_POST["fabricante"]             ?? "";
    $data_aquisicao    = $_POST["data_aquisicao"]         ?? "";
    $ano_fabrico       = $_POST["ano_fabrico"]            ?? "";
    $custo_aquisicao   = $_POST["custo_aquisicao"]        ?? "";
    $tipo_entrada      = $_POST["tipo_entrada"]           ?? "";
    $estado            = $_POST["estado"]                 ?? "";
    $criticidade       = $_POST["criticidade"]            ?? "";
    $observacoes       = $_POST["observacoes"]            ?? "";
    $local_edificio    = $_POST["local_edificio"]         ?? "";
    $local_piso        = $_POST["local_piso"]             ?? "";
    $local_servico     = $_POST["local_servico"]          ?? "";
    $local_sala        = $_POST["local_sala"]             ?? "";
    $fornecedor_nome   = $_POST["fornecedor_nome"]        ?? "";
    $fornecedor_email  = $_POST["fornecedor_email"]       ?? "";
    $fornecedor_tel    = $_POST["fornecedor_telefone"]    ?? "";
    $fornecedor_morada = $_POST["fornecedor_morada"]      ?? "";
    $garantia_inicio   = $_POST["garantia_inicio"]        ?? "";
    $garantia_fim      = $_POST["garantia_fim"]           ?? "";
    $garantia_tipo     = $_POST["garantia_tipo"]          ?? "";
    $garantia_entidade = $_POST["garantia_entidade"]      ?? "";
    $garantia_period   = $_POST["garantia_periodicidade"] ?? "";
    $garantia_obs      = $_POST["garantia_observacoes"]   ?? "";

    $erros = [];
    $erro_sistema = "";

    $codigo_interno  = trim($codigo_interno);
    $designacao      = trim($designacao);
    $marca           = trim($marca);
    $modelo          = trim($modelo);
    $numero_serie    = trim($numero_serie);
    $fabricante      = trim($fabricante);
    $data_aquisicao  = trim($data_aquisicao);
    $ano_fabrico     = trim($ano_fabrico);
    $custo_aquisicao = trim($custo_aquisicao);

    if (empty($codigo_interno))
        $erros[] = "O Código Interno é obrigatório.";

    if (empty($designacao))
        $erros[] = "A Designação do equipamento é obrigatória.";
    elseif (preg_match('/\d/', $designacao))
        $erros[] = "A Designação não pode conter números.";

    if (empty($categoria))    $erros[] = "A Categoria é obrigatória.";
    if (empty($marca))        $erros[] = "A Marca é obrigatória.";
    if (empty($modelo))       $erros[] = "O Modelo é obrigatório.";
    if (empty($numero_serie)) $erros[] = "O Número de Série é obrigatório.";
    if (empty($fabricante))   $erros[] = "O Fabricante é obrigatório.";

    if (empty($data_aquisicao)) {
        $erros[] = "A Data de Aquisição é obrigatória.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_aquisicao)) {
        $erros[] = "Formato de Data de Aquisição inválido. Use AAAA-MM-DD.";
    } else {
        $partes = explode('-', $data_aquisicao);
        if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0]))
            $erros[] = "Data de Aquisição inválida.";
    }

    if (empty($ano_fabrico)) {
        $erros[] = "O Ano de Fabrico é obrigatório.";
    } elseif (!preg_match('/^\d{4}$/', $ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > 2100) {
        $erros[] = "Ano de Fabrico inválido.";
    }

    if ($custo_aquisicao === '') {
        $erros[] = "O Custo de Aquisição é obrigatório.";
    } elseif (!is_numeric($custo_aquisicao) || (float)$custo_aquisicao < 0) {
        $erros[] = "O Custo de Aquisição deve ser um número positivo.";
    }

    if (empty($tipo_entrada)) $erros[] = "O Tipo de Entrada é obrigatório.";
    if (empty($estado))       $erros[] = "O Estado atual é obrigatório.";
    if (empty($criticidade))  $erros[] = "A Criticidade é obrigatória.";

    if (!empty($fornecedor_email) && !filter_var($fornecedor_email, FILTER_VALIDATE_EMAIL))
        $erros[] = "O email do fornecedor não é válido.";

    if (!empty($fornecedor_tel) && !preg_match('/^[29]\d{8}$/', $fornecedor_tel))
        $erros[] = "O telefone do fornecedor deve ter 9 dígitos e começar por 9 ou 2.";

    if (!empty($garantia_inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_inicio))
        $erros[] = "Formato de data de início de garantia inválido. Use AAAA-MM-DD.";

    if (!empty($garantia_fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_fim))
        $erros[] = "Formato de data de fim de garantia inválido. Use AAAA-MM-DD.";

}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Inserir Equipamento</h1>
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <?php if (!empty($erros)) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Foram encontrados os seguintes erros:</strong>
                <ul class="mb-0">
                    <?php foreach ($erros as $erro) : ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro_sistema)) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Erro:</strong>
                <p><?= htmlspecialchars($erro_sistema) ?></p>
            </div>
        <?php endif; ?>

        <form action="inserir.php" method="POST" enctype="multipart/form-data" novalidate class="shadow p-4 rounded bg-white"
            style="max-width: 900px; margin: auto;">

            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados"
                        type="button" role="tab">
                        Dados do Equipamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedor-tab" data-bs-toggle="tab" data-bs-target="#fornecedor"
                        type="button" role="tab">
                        Fornecedor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao"
                        type="button" role="tab">
                        Localização
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantia-tab" data-bs-toggle="tab" data-bs-target="#garantia"
                        type="button" role="tab">
                        Garantia / Contrato
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button"
                        role="tab">
                        Documentação
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="equipTabsContent">

                <div class="tab-pane fade show active" id="dados" role="tabpanel">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código Interno de Inventário</label>
                        <input type="text" name="codigo_interno" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Designação do Equipamento</label>
                        <input type="text" name="designacao" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoria / Grupo</label>
                        <select name="categoria" class="form-select" required>
                            <option value="">Selecione a categoria</option>
                            <option value="Monitorização">Monitorização</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                            <option value="Terapia">Terapia</option>
                            <option value="Diagnóstico">Diagnóstico</option>
                            <option value="Laboratório">Laboratório</option>
                            <option value="Esterilização">Esterilização</option>
                            <option value="Reabilitação">Reabilitação</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Marca</label>
                        <input type="text" name="marca" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Modelo</label>
                        <input type="text" name="modelo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Número de Série</label>
                        <input type="text" name="numero_serie" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fabricante</label>
                        <input type="text" name="fabricante" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Aquisição</label>
                        <input type="date" name="data_aquisicao" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ano de Fabrico</label>
                        <input type="number" name="ano_fabrico" class="form-control" min="1900" max="2100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                        <input type="number" name="custo_aquisicao" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Entrada</label>
                        <select name="tipo_entrada" class="form-select" required>
                            <option value="">Selecione o tipo</option>
                            <option value="Compra">Compra</option>
                            <option value="Doação">Doação</option>
                            <option value="Aluguer">Aluguer</option>
                            <option value="Empréstimo">Empréstimo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado Atual</label>
                        <select name="estado" class="form-select" required>
                            <option value="">Selecione o estado</option>
                            <option value="Ativo">Ativo</option>
                            <option value="Em manutenção">Em manutenção</option>
                            <option value="Inativo">Inativo</option>
                            <option value="Em calibração">Em calibração</option>
                            <option value="Em quarentena">Em quarentena</option>
                            <option value="Abatido">Abatido</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Criticidade</label>
                        <select name="criticidade" class="form-select" required>
                            <option value="">Selecione a criticidade</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="4"></textarea>
                    </div>

                </div>

                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Fornecedor associado</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do fornecedor</label>
                            <input type="text" class="form-control" name="fornecedor_nome">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="fornecedor_email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Telefone</label>
                            <input type="tel" class="form-control" name="fornecedor_telefone" pattern="[0-9]{9}" maxlength="9" placeholder="Ex: 912345678">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Morada</label>
                            <input type="text" class="form-control" name="fornecedor_morada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="3" name="fornecedor_observacoes"></textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Localização do equipamento</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Edifício</label>
                            <input type="text" class="form-control" name="local_edificio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Piso</label>
                            <input type="text" class="form-control" name="local_piso">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Serviço / Departamento</label>
                            <input type="text" class="form-control" name="local_servico">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" class="form-control" name="local_sala">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Garantia / Contrato associado</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de início da garantia</label>
                            <input type="date" class="form-control" name="garantia_inicio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de fim da garantia</label>
                            <input type="date" class="form-control" name="garantia_fim">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de contrato</label>
                            <select class="form-select" name="garantia_tipo">
                                <option value="">Selecione...</option>
                                <option>Garantia</option>
                                <option>Contrato de Manutenção</option>
                                <option>Assistência Técnica</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Entidade responsável</label>
                            <input type="text" class="form-control" name="garantia_entidade">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Periodicidade</label>
                            <select class="form-select" name="garantia_periodicidade">
                                <option value="">Selecione...</option>
                                <option>Mensal</option>
                                <option>Trimestral</option>
                                <option>Semestral</option>
                                <option>Anual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="4" name="garantia_observacoes"></textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Documentação associada</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de documento</label>
                            <select class="form-select" name="doc_tipo">
                                <option value="">Selecione...</option>
                                <option>Manual</option>
                                <option>Certificado</option>
                                <option>Relatório Técnico</option>
                                <option>Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <input type="text" class="form-control" name="doc_descricao">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ficheiros</label>
                            <input type="file" class="form-control" name="documentos[]" multiple>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="3" name="doc_observacoes"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-success w-100 mt-4">
                <i class="fa-solid fa-check me-2"></i>Guardar Equipamento
            </button>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>