~
<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>


<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes do Fornecedor</h1>

            <a href="editar.html" class="btn btn-warning">
                <i class="fa-solid fa-pen"></i> Editar
            </a>
        </div>

        <!-- CARTÃO DE INFORMAÇÕES -->
        <div class="shadow p-4 rounded mb-4" style="max-width: 900px;">

            <h4 class="mb-3">Informações Gerais</h4>

            <div class="row mb-3">
                <div class="col">
                    <strong>Nome da Empresa:</strong>
                    <p></p>
                </div>

                <div class="col">
                    <strong>NIF:</strong>
                    <p></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <strong>Telefone:</strong>
                    <p></p>
                </div>

                <div class="col">
                    <strong>Email:</strong>
                    <p></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Morada:</strong>
                <p></p>
            </div>

            <div class="mb-3">
                <strong>Website:</strong>
                <p></p>
            </div>

            <hr>

            <h4 class="mb-3">Contacto</h4>

            <div class="row mb-3">
                <div class="col">
                    <strong>Pessoa de Contacto:</strong>
                    <p></p>
                </div>

                <div class="col">
                    <strong>Telefone da Pessoa de Contacto:</strong>
                    <p></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Tipo de Fornecedor:</strong>
                <p></p>
            </div>

            <div class="mb-3">
                <strong>Observações:</strong>
                <p></p>
            </div>

        </div>

        <div class="shadow p-4 rounded" style="max-width: 900px;">
            <h4 class="mb-3">Equipamentos Associados</h4>

            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Nome do Equipamento</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <a href="../equipamentos/detalhes.html" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>

                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="listar.html" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>