<?php
require_once __DIR__ . '/../config/config.php';
?>

<?php
$conteudos = [
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

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = $ligacao->query("SELECT chave, valor FROM gestao_site")->fetchAll(PDO::FETCH_OBJ);
    foreach ($rows as $row) {
        $conteudos[$row->chave] = $row->valor;
    }
    $ligacao = null;
} catch (PDOException $err) {
    // usa os valores por defeito
}

function c($conteudos, $chave) {
    return htmlspecialchars($conteudos[$chave] ?? '');
}

session_start();
$contacto_sucesso = $_SESSION['contacto_sucesso'] ?? '';
$contacto_erros = $_SESSION['contacto_erros'] ?? [];
unset($_SESSION['contacto_sucesso'], $_SESSION['contacto_erros']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Gestão do inventário de equipamentos hospitalares</title>
    <meta name="description" content="Sistema para gestão inteligente do inventário de equipamentos hospitalares">
    <link rel="icon" type="image/png" href="../assets/img/logHospital.png">
    <link rel="stylesheet" href="../assets/css/1241094.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <img src="../assets/img/logHospital.png" alt="Logo do sistema">
            <h1><?php echo APP_NAME; ?></h1>
        </div>
        <nav class="menu">
            <a href="#inicio">Início</a>
            <a href="#sobre">Sobre</a>
            <a href="#funcionalidades">Funcionalidades</a>
            <a href="#contacto">Contacto</a>
            <a href="login.php" class="btn-login">Iniciar Sessão</a>
        </nav>
    </header>

    <main>

        <div id="inicio" class="imagem">
            <img src="../assets/img/maqhospitalar.png" alt="Equipamentos Hospitalares">
            <div class="intro">
                <h2><?= c($conteudos, 'hero_titulo') ?></h2>
                <p><?= c($conteudos, 'hero_subtitulo') ?></p>
            </div>
        </div>

        <section id="sobre">
            <div class="sobre-container">
                <div class="sobre-content">
                    <h2><?= c($conteudos, 'sobre_titulo') ?></h2>
                    <p><?= c($conteudos, 'sobre_descricao') ?></p>
                </div>
                <div class="sobre-vantagens">
                    <h3>Vantagens do MedStock</h3>
                    <ul>
                        <li><?= c($conteudos, 'sobre_van1') ?></li>
                        <li><?= c($conteudos, 'sobre_van2') ?></li>
                        <li><?= c($conteudos, 'sobre_van3') ?></li>
                        <li><?= c($conteudos, 'sobre_van4') ?></li>
                        <li><?= c($conteudos, 'sobre_van5') ?></li>
                    </ul>
                </div>
            </div>
        </section>

        <hr class="separador">

        <section id="funcionalidades">
            <div class="funcontainer">
                <div class="funcontent">
                    <h2><?= c($conteudos, 'fun_titulo') ?></h2>
                    <p><?= c($conteudos, 'fun_descricao') ?></p>
                </div>
                <div class="funlista">
                    <h3>Funcionalidades Principais</h3>
                    <div class="funicon">
                        <img src="../assets/img/iconebloco.png" alt="Registro">
                        <p><?= c($conteudos, 'fun1') ?></p>
                    </div>
                    <div class="funicon">
                        <img src="../assets/img/iconelogis.png" alt="Gestão de fornecedores">
                        <p><?= c($conteudos, 'fun2') ?></p>
                    </div>
                    <div class="funicon">
                        <img src="../assets/img/iconeloc.png" alt="Localização">
                        <p><?= c($conteudos, 'fun3') ?></p>
                    </div>
                    <div class="funicon">
                        <img src="../assets/img/iconealerta.png" alt="Alertas">
                        <p><?= c($conteudos, 'fun4') ?></p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="separador">

        <section id="contacto">
            <div class="contactocontainer">
                <div class="contactoscontainer">
                    <h2>Contacto</h2>
                    <p>Preencha os seus dados abaixo para que possamos entrar em contacto consigo.</p>
                </div>

                <?php if (!empty($contacto_sucesso)) : ?>
                    <p class="text-success"><?= htmlspecialchars($contacto_sucesso) ?></p>
                <?php endif; ?>

                <?php if (!empty($contacto_erros)) : ?>
                    <ul class="text-danger">
                        <?php foreach ($contacto_erros as $erro) : ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form class="contactosform" action="processa_contacto.php" method="POST">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <label for="telemovel">Telemóvel:</label>
                    <input type="tel" id="telemovel" name="telemovel" value="<?= htmlspecialchars($_POST['telemovel'] ?? '') ?>" required>
                    <label for="mensagem">Mensagem:</label>
                    <textarea id="mensagem" name="mensagem" rows="4" required><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </section>

    </main>

    <footer class="footercontainer">
        <div class="footersection">
            <strong>LOCALIZAÇÃO</strong>
            <p><?= c($conteudos, 'footer_morada') ?></p>
            <p><?= c($conteudos, 'footer_cod_postal') ?></p>
            <p>Portugal</p>
        </div>
        <div class="footersection">
            <strong>HORÁRIO</strong>
            <p><?= c($conteudos, 'footer_horario_semana') ?></p>
            <p><?= c($conteudos, 'footer_horario_sabado') ?></p>
            <p><?= c($conteudos, 'footer_horario_domingo') ?></p>
        </div>
        <div class="footersection">
            <strong>CONTACTOS</strong>
            <p>Email: <?= c($conteudos, 'footer_email') ?></p>
            <p>Telefone: <?= c($conteudos, 'footer_telefone') ?></p>
        </div>
    </footer>

</body>
</html>