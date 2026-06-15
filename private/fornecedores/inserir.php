<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Fornecedor</h1>

        <form class="shadow p-4 rounded" style="max-width: 800px;" method="POST" action="#">

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