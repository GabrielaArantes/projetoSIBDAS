<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = false;

// Valores por defeito
$defaults = [
    'hero_titulo' => 'Gestão inteligente do inventário hospitalar',
    'hero_subtitulo' => 'Organização, controlo e eficiência num único sistema.',
    'sobre_titulo' => 'Sobre o MedStock',
    'sobre_descricao' => 'O MedStock é um sistema inovador para a gestão do inventário de equipamentos hospitalares.',
    'sobre_van1' => 'Otimização dos processos internos e redução de falhas humanas.',
    'sobre_van2' => 'Maior segurança operacional através de registos consistentes.',
    'sobre_van3' => 'Transparência total sobre o ciclo de vida dos equipamentos.',
    'sobre_van4' => 'Melhoria da tomada de decisão com dados atualizados.',
    'sobre_van5' => 'Redução de custos associados a perdas e duplicação de material.',
    'fun_titulo' => 'Funcionalidades',
    'fun_descricao' => 'O MedStock oferece um conjunto de funcionalidades essenciais para garantir uma gestão eficiente do inventário hospitalar.',
    'fun1' => 'Registro inteligente',
    'fun2' => 'Gestão de fornecedores e categorias',
    'fun3' => 'Localização e estado dos dispositivos',
    'fun4' => 'Alertas de manutenção e avarias',
    'footer_morada' => 'Morada',
    'footer_cod_postal' => 'codigopostal, Porto',
    'footer_horario_semana' => '2ª a 6ª Feira: 7h - 21h',
    'footer_horario_sabado' => 'Sábado e Feriados: 9h - 15h',
    'footer_horario_domingo' => 'Domingos: Encerrados',
    'footer_email' => 'geral@medstock.pt',
    'footer_telefone' => '+351 9xx xxx xxx',
];

$conteudos = $defaults;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Carregar valores da BD
    $rows = $ligacao->query("SELECT chave, valor FROM gestao_site")->fetchAll(PDO::FETCH_OBJ);
    foreach ($rows as $row) {
        $conteudos[$row->chave] = $row->valor;
    }

    // Guardar se POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['secao'])) {
        $campos = [];

        if ($_POST['secao'] === 'hero') {
            $campos = ['hero_titulo', 'hero_subtitulo'];
        } elseif ($_POST['secao'] === 'sobre') {
            $campos = ['sobre_titulo', 'sobre_descricao', 'sobre_van1', 'sobre_van2', 'sobre_van3', 'sobre_van4', 'sobre_van5'];
        } elseif ($_POST['secao'] === 'fun') {
            $campos = ['fun_titulo', 'fun_descricao', 'fun1', 'fun2', 'fun3', 'fun4'];
        } elseif ($_POST['secao'] === 'footer') {
            $campos = ['footer_morada', 'footer_cod_postal', 'footer_horario_semana', 'footer_horario_sabado', 'footer_horario_domingo', 'footer_email', 'footer_telefone'];
        }

        foreach ($campos as $chave) {
            if (isset($_POST[$chave])) {
                $stmt = $ligacao->prepare("INSERT INTO gestao_site (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?");
                $stmt->execute([$chave, $_POST[$chave], $_POST[$chave]]);
                $conteudos[$chave] = $_POST[$chave];
            }
        }

        $sucesso = true;
    }

    $ligacao = null;
} catch (PDOException $err) {
    // erro silencioso
}

function v($conteudos, $chave) {
    return htmlspecialchars($conteudos[$chave] ?? '');
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo p-4">

        <h1 class="mb-4">Gestão de Conteúdos Públicos</h1>

        <?php if ($sucesso) : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check me-2"></i> Conteúdo atualizado com sucesso!
            </div>
        <?php endif; ?>

        <div class="accordion" id="accordionConteudos">

            <!-- HERO -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHero">
                        <i class="fa-solid fa-image me-2"></i> Secção Início
                    </button>
                </h2>
                <div id="collapseHero" class="accordion-collapse collapse show" data-bs-parent="#accordionConteudos">
                    <div class="accordion-body">
                        <form method="POST">
                            <input type="hidden" name="secao" value="hero">
                            <div class="mb-3">
                                <label class="form-label">Título principal</label>
                                <input type="text" class="form-control" name="hero_titulo" value="<?= v($conteudos, 'hero_titulo') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtítulo</label>
                                <input type="text" class="form-control" name="hero_subtitulo" value="<?= v($conteudos, 'hero_subtitulo') ?>">
                            </div>
                            <button type="submit" class="btn-guardar">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SOBRE -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSobre">
                        <i class="fa-solid fa-circle-info me-2"></i> Secção Sobre
                    </button>
                </h2>
                <div id="collapseSobre" class="accordion-collapse collapse" data-bs-parent="#accordionConteudos">
                    <div class="accordion-body">
                        <form method="POST">
                            <input type="hidden" name="secao" value="sobre">
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="sobre_titulo" value="<?= v($conteudos, 'sobre_titulo') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="sobre_descricao" rows="4"><?= v($conteudos, 'sobre_descricao') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vantagem 1</label>
                                <input type="text" class="form-control" name="sobre_van1" value="<?= v($conteudos, 'sobre_van1') ?>">
                                <label class="form-label mt-2">Vantagem 2</label>
                                <input type="text" class="form-control" name="sobre_van2" value="<?= v($conteudos, 'sobre_van2') ?>">
                                <label class="form-label mt-2">Vantagem 3</label>
                                <input type="text" class="form-control" name="sobre_van3" value="<?= v($conteudos, 'sobre_van3') ?>">
                                <label class="form-label mt-2">Vantagem 4</label>
                                <input type="text" class="form-control" name="sobre_van4" value="<?= v($conteudos, 'sobre_van4') ?>">
                                <label class="form-label mt-2">Vantagem 5</label>
                                <input type="text" class="form-control" name="sobre_van5" value="<?= v($conteudos, 'sobre_van5') ?>">
                            </div>
                            <button type="submit" class="btn-guardar">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FUNCIONALIDADES -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFun">
                        <i class="fa-solid fa-star me-2"></i> Secção Funcionalidades
                    </button>
                </h2>
                <div id="collapseFun" class="accordion-collapse collapse" data-bs-parent="#accordionConteudos">
                    <div class="accordion-body">
                        <form method="POST">
                            <input type="hidden" name="secao" value="fun">
                            <div class="mb-3">
                                <label class="form-label">Título da secção</label>
                                <input type="text" class="form-control" name="fun_titulo" value="<?= v($conteudos, 'fun_titulo') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="fun_descricao" rows="3"><?= v($conteudos, 'fun_descricao') ?></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade 1</label>
                                    <input type="text" class="form-control" name="fun1" value="<?= v($conteudos, 'fun1') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade 2</label>
                                    <input type="text" class="form-control" name="fun2" value="<?= v($conteudos, 'fun2') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade 3</label>
                                    <input type="text" class="form-control" name="fun3" value="<?= v($conteudos, 'fun3') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade 4</label>
                                    <input type="text" class="form-control" name="fun4" value="<?= v($conteudos, 'fun4') ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn-guardar mt-3">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFooter">
                        <i class="fa-solid fa-address-card me-2"></i> Rodapé (Contactos, Horários e Localização)
                    </button>
                </h2>
                <div id="collapseFooter" class="accordion-collapse collapse" data-bs-parent="#accordionConteudos">
                    <div class="accordion-body">
                        <form method="POST">
                            <input type="hidden" name="secao" value="footer">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Morada</label>
                                    <input type="text" class="form-control" name="footer_morada" value="<?= v($conteudos, 'footer_morada') ?>">
                                    <label class="form-label mt-2">Código Postal</label>
                                    <input type="text" class="form-control" name="footer_cod_postal" value="<?= v($conteudos, 'footer_cod_postal') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Horário semana (2ª a 6ª)</label>
                                    <input type="text" class="form-control" name="footer_horario_semana" value="<?= v($conteudos, 'footer_horario_semana') ?>">
                                    <label class="form-label mt-2">Horário sábado/feriados</label>
                                    <input type="text" class="form-control" name="footer_horario_sabado" value="<?= v($conteudos, 'footer_horario_sabado') ?>">
                                    <label class="form-label mt-2">Horário domingos</label>
                                    <input type="text" class="form-control" name="footer_horario_domingo" value="<?= v($conteudos, 'footer_horario_domingo') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="footer_email" value="<?= v($conteudos, 'footer_email') ?>">
                                    <label class="form-label mt-2">Telefone</label>
                                    <input type="text" class="form-control" name="footer_telefone" value="<?= v($conteudos, 'footer_telefone') ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn-guardar mt-3">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>