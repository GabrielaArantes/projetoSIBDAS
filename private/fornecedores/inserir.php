<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome_empresa     = $_POST["nome_empresa"]      ?? "";
    $nif              = $_POST["nif"]               ?? "";
    $telefone         = $_POST["telefone"]          ?? "";
    $email            = $_POST["email"]             ?? "";
    $morada           = $_POST["morada"]            ?? "";
    $website          = $_POST["website"]           ?? "";
    $pessoa_contacto  = $_POST["pessoa_contacto"]   ?? "";
    $tel_contacto     = $_POST["telefone_contacto"] ?? "";
    $tipo_fornecedor  = $_POST["tipo_fornecedor"]   ?? "";
    $observacoes      = $_POST["observacoes"]       ?? "";

    echo "<p><strong>Dados recebidos:</strong> Nome: $nome_empresa | NIF: $nif | Telefone: $telefone | Email: $email | Tipo: $tipo_fornecedor</p>";

}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Fornecedor</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 800px;" method="POST" action="inserir.php">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome_empresa" placeholder="Ex: Medstock Portugal" required>
                </div>
                <div class="col">
                    <label class="form-label">NIF <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="nif" placeholder="Ex: 501234657" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telefone" placeholder="Ex: 912 345 678" required>
                </div>
                <div class="col">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="Ex: suporte@medstock.pt" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" name="morada" placeholder="Ex: Rua da Saúde 120, 4200-450 Porto">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" name="website" placeholder="Ex: https://www.medstock.com">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="pessoa_contacto" placeholder="Ex: Gabriela Arantes">
                </div>
                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="telefone_contacto" placeholder="Ex: 934 567 890">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo_fornecedor" required>
                    <option value="">Selecione...</option>
                    <option>Fabricante</option>
                    <option>Distribuidor / Fornecedor Comercial</option>
                    <option>Assistência Técnica</option>
                    <option>Fornecedor de Consumíveis</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4" name="observacoes"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar Fornecedor
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>