<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome_empresa    = $_POST["nome_empresa"]      ?? "";
    $nif             = $_POST["nif"]               ?? "";
    $telefone        = $_POST["telefone"]          ?? "";
    $email           = $_POST["email"]             ?? "";
    $morada          = $_POST["morada"]            ?? "";
    $website         = $_POST["website"]           ?? "";
    $pessoa_contacto = $_POST["pessoa_contacto"]   ?? "";
    $tel_contacto    = $_POST["telefone_contacto"] ?? "";
    $tipo_fornecedor = $_POST["tipo_fornecedor"]   ?? "";
    $observacoes     = $_POST["observacoes"]       ?? "";

    $erros = [];
    $erro_sistema = "";

    $nome_empresa    = trim($nome_empresa);
    $nif             = trim($nif);
    $telefone        = trim($telefone);
    $email           = trim($email);
    $tipo_fornecedor = trim($tipo_fornecedor);

    if (empty($nome_empresa))
        $erros[] = "O Nome da Empresa é obrigatório.";

    if (empty($nif)) {
        $erros[] = "O NIF é obrigatório.";
    } elseif (!preg_match('/^\d{9}$/', $nif)) {
        $erros[] = "O NIF deve ter exatamente 9 dígitos.";
    }

    if (empty($telefone)) {
        $erros[] = "O Telefone é obrigatório.";
    } elseif (!preg_match('/^[29]\d{8}$/', $telefone)) {
        $erros[] = "O Telefone deve ter 9 dígitos e começar por 9 ou 2.";
    }

    if (empty($email)) {
        $erros[] = "O Email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido.";
    }

    if (empty($tipo_fornecedor))
        $erros[] = "O Tipo de Fornecedor é obrigatório.";

    if (!empty($tel_contacto) && !preg_match('/^[29]\d{8}$/', trim($tel_contacto)))
        $erros[] = "O Telefone da Pessoa de Contacto deve ter 9 dígitos e começar por 9 ou 2.";

    if (empty($erros)) {
        $nome_empresa    = ucwords(strtolower($nome_empresa));
        $email           = strtolower($email);
        $pessoa_contacto = ucwords(strtolower($pessoa_contacto));
    }

}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Fornecedor</h1>

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

        <form class="shadow p-4 rounded" style="max-width: 800px;" method="POST" action="inserir.php" novalidate>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome_empresa"
                        value="<?= htmlspecialchars($_POST['nome_empresa'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">NIF <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nif"
                        value="<?= htmlspecialchars($_POST['nif'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telefone"
                        value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" name="morada"
                    value="<?= htmlspecialchars($_POST['morada'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" name="website"
                    value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="pessoa_contacto"
                        value="<?= htmlspecialchars($_POST['pessoa_contacto'] ?? '') ?>">
                </div>
                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="telefone_contacto"
                        value="<?= htmlspecialchars($_POST['telefone_contacto'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo_fornecedor" required>
                    <option value="">Selecione...</option>
                    <?php foreach (['Fabricante', 'Distribuidor / Fornecedor Comercial', 'Assistência Técnica', 'Fornecedor de Consumíveis'] as $op) : ?>
                        <option value="<?= $op ?>" <?= (($_POST['tipo_fornecedor'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4" name="observacoes"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
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