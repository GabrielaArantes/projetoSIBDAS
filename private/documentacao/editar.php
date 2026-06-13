<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedStock - Editar Documentação</title>

    <link rel="stylesheet" href="../../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/1241094.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <script src="../../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" href="../../assets/img/logHospital.png">
</head>

<body class="pagprivada">

    <aside class="sidebar">
        <nav>
            <a href="../dashboard/dashboard.html"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="../equipamentos/listar.html"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>
            <a href="../fornecedores/listar.html"><i class="fa-solid fa-truck"></i> Fornecedores</a>
            <a href="../localizacao/listar.html"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="../garantcontrato/listar.html"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
            <a href="listar.html" class="active"><i class="fa-solid fa-folder-open"></i> Documentação</a>
            <a href="../gestaoconteudo/gestao.html"><i class="fa-solid fa-pen-to-square"></i> Gestão de Conteúdos Públicos</a>
        </nav>
    </aside>

    <header class="topbar">
        <div class="logo-topbar">
            <img src="../../assets/img/logHospital.png" alt="Logo MedStock">
            <h1>MedStock</h1>
        </div>

        <div class="user-button">
            <i class="fa-regular fa-user"></i>
            <span>Utilizador</span>
            <i class="fa-solid fa-chevron-down seta"></i>

            <ul class="user-dropdown">
                <li><a href="#">Mudar password</a></li>
                <li><a href="../public/login.html">Sair</a></li>
            </ul>
        </div>
    </header>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Documentação</h1>

            <a href="listar.html" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form id="formEditarDoc" enctype="multipart/form-data">

                <input type="hidden" name="id" id="doc_id">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento *</label>
                    <input type="text" class="form-control" name="tipo" id="tipo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento *</label>
                    <input type="text" class="form-control" name="nome" id="nome" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento *</label>
                    <input type="date" class="form-control" name="data" id="data" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="date" class="form-control" name="validade" id="validade">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado *</label>
                    <select class="form-select" name="equipamento" id="equipamento" required>
                        <option value="">Selecione...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ficheiro Atual</label>
                    <p id="ficheiroAtual" class="text-muted"></p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Substituir Ficheiro (opcional)</label>
                    <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
                    </button>
                </div>

            </form>

        </div>

    </main>

</body>

</html>
