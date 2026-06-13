<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Fornecedor</h1>

        <form class="shadow p-4 rounded" style="max-width: 800px;">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa</label>
                    <input type="text" class="form-control" placeholder="Ex: Medstock Portugal">
                </div>

                <div class="col">
                    <label class="form-label">NIF</label>
                    <input type="number" class="form-control" placeholder="Ex: 501234657">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" placeholder="Ex: 912 345 678">
                </div>

            <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Ex: suporte@medstock.pt">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" placeholder="Ex:Rua da Saúde 120, 4200-450 Porto">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" placeholder="Ex: https://www.medstock.com">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" placeholder="Ex: Gabriela Arantes">
                </div>
                
                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" placeholder="Ex: 934 567 890">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor</label>
                <select class="form-select">
                    <option value="">Selecione...</option>
                    <option>Fabricante</option>
                    <option>Distribuidor / Fornecedor Comercial</option>
                    <option>Assistência Técnica</option>
                    <option>Fornecedor de Consumíveis</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar Fornecedor
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>