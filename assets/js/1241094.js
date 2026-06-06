
var equipamentos = [
    { estado: "Ativo",         servico: "Urgência", categoria: "Monitorização", criticidade: "Alta",           garantia_fim: "2024-05-10", temDoc: true  },
    { estado: "Em manutenção", servico: "UCI",      categoria: "Suporte de Vida", criticidade: "Suporte de vida", garantia_fim: "2023-12-01", temDoc: false },
    { estado: "Inativo",       servico: "Medicina", categoria: "Diagnóstico",   criticidade: "Média",          garantia_fim: "2025-02-20", temDoc: true  }
];

function contarAtivos() {
    var total = 0;
    for (var i = 0; i < equipamentos.length; i++) {
        if (equipamentos[i].estado === "Ativo") {
            total++;
        }
    }
    return total;
}

function contarManutencao() {
    var total = 0;
    for (var i = 0; i < equipamentos.length; i++) {
        if (equipamentos[i].estado === "Em manutenção") {
            total++;
        }
    }
    return total;
}

function contarInativos() {
    var total = 0;
    for (var i = 0; i < equipamentos.length; i++) {
        if (equipamentos[i].estado === "Inativo") {
            total++;
        }
    }
    return total;
}

function contarGarantiaExpirada() {
    var total = 0;
    var hoje = new Date();
    for (var i = 0; i < equipamentos.length; i++) {
        if (new Date(equipamentos[i].garantia_fim) < hoje) {
            total++;
        }
    }
    return total;
}

function contarSemDoc() {
    var total = 0;
    for (var i = 0; i < equipamentos.length; i++) {
        if (!equipamentos[i].temDoc) {
            total++;
        }
    }
    return total;
}

function contarCriticos() {
    var total = 0;
    for (var i = 0; i < equipamentos.length; i++) {
        if (equipamentos[i].criticidade === "Alta" || equipamentos[i].criticidade === "Suporte de vida") {
            total++;
        }
    }
    return total;
}

function contarExpira30() {
    var total = 0;
    var hoje = new Date();
    var daqui30 = new Date();
    daqui30.setDate(hoje.getDate() + 30);
    for (var i = 0; i < equipamentos.length; i++) {
        var fim = new Date(equipamentos[i].garantia_fim);
        if (fim >= hoje && fim <= daqui30) {
            total++;
        }
    }
    return total;
}

function contarServicos() {
    var servicos = [];
    for (var i = 0; i < equipamentos.length; i++) {
        var encontrado = false;
        for (var j = 0; j < servicos.length; j++) {
            if (servicos[j] === equipamentos[i].servico) {
                encontrado = true;
            }
        }
        if (!encontrado) {
            servicos.push(equipamentos[i].servico);
        }
    }
    return servicos.length;
}

function atualizarCards() {
    var html = "";

    html += "<div class='row g-3'>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#e8f4fd; color:#1a73e8;'><i class='fa-solid fa-boxes-stacked'></i></div>";
    html += "<div class='stat-info'><h6>Total</h6><h3 style='color:#1a73e8;'>" + equipamentos.length + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#e6f9ee; color:#28a745;'><i class='fa-solid fa-circle-check'></i></div>";
    html += "<div class='stat-info'><h6>Ativos</h6><h3 style='color:#28a745;'>" + contarAtivos() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#fff8e1; color:#f59e0b;'><i class='fa-solid fa-wrench'></i></div>";
    html += "<div class='stat-info'><h6>Manutenção</h6><h3 style='color:#f59e0b;'>" + contarManutencao() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#fdecea; color:#dc3545;'><i class='fa-solid fa-circle-xmark'></i></div>";
    html += "<div class='stat-info'><h6>Inativos</h6><h3 style='color:#dc3545;'>" + contarInativos() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#fdecea; color:#dc3545;'><i class='fa-solid fa-shield-halved'></i></div>";
    html += "<div class='stat-info'><h6>Garantia Expirada</h6><h3 style='color:#dc3545;'>" + contarGarantiaExpirada() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#f3f4f6; color:#6b7280;'><i class='fa-solid fa-file-circle-xmark'></i></div>";
    html += "<div class='stat-info'><h6>Sem Documentação</h6><h3 style='color:#6b7280;'>" + contarSemDoc() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#e8f4fd; color:#1a73e8;'><i class='fa-solid fa-hospital'></i></div>";
    html += "<div class='stat-info'><h6>Serviços</h6><h3 style='color:#1a73e8;'>" + contarServicos() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#fdecea; color:#dc3545;'><i class='fa-solid fa-triangle-exclamation'></i></div>";
    html += "<div class='stat-info'><h6>Criticidade Elevada</h6><h3 style='color:#dc3545;'>" + contarCriticos() + "</h3></div></div></div>";

    html += "<div class='col-6 col-md-3'><div class='stat-card'>";
    html += "<div class='stat-icon' style='background:#fff8e1; color:#f59e0b;'><i class='fa-solid fa-clock'></i></div>";
    html += "<div class='stat-info'><h6>Garantia expira em 30 dias</h6><h3 style='color:#f59e0b;'>" + contarExpira30() + "</h3></div></div></div>";

    html += "</div>";

    document.getElementById("cardsDashboard").innerHTML = html;
}

function graficoEstado() {
    new Chart(document.getElementById("graficoEstado"), {
        type: "bar",
        data: {
            labels: ["Ativos", "Manutenção", "Inativos"],
            datasets: [{
                label: "Equipamentos",
                data: [contarAtivos(), contarManutencao(), contarInativos()],
                backgroundColor: ["#28a745", "#ffc107", "#dc3545"],
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 }
                }
            }
        }
    });
}

function graficoServico() {
    var servicos = [];
    var valores = [];

    for (var i = 0; i < equipamentos.length; i++) {
        var serv = equipamentos[i].servico;
        var encontrado = false;
        for (var j = 0; j < servicos.length; j++) {
            if (servicos[j] === serv) {
                valores[j]++;
                encontrado = true;
            }
        }
        if (!encontrado) {
            servicos.push(serv);
            valores.push(1);
        }
    }

    new Chart(document.getElementById("graficoServico"), {
        type: "bar",
        data: {
            labels: servicos,
            datasets: [{
                label: "Equipamentos",
                data: valores,
                backgroundColor: "#198754",
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 }
                }
            }
        }
    });
}

function graficoCategoria() {
    var categorias = [];
    var valores = [];

    for (var i = 0; i < equipamentos.length; i++) {
        var cat = equipamentos[i].categoria;
        var encontrado = false;
        for (var j = 0; j < categorias.length; j++) {
            if (categorias[j] === cat) {
                valores[j]++;
                encontrado = true;
            }
        }
        if (!encontrado) {
            categorias.push(cat);
            valores.push(1);
        }
    }

    new Chart(document.getElementById("graficoCategoria"), {
        type: "pie",
        data: {
            labels: categorias,
            datasets: [{
                data: valores,
                backgroundColor: ["#198754", "#20c997", "#6fddc7", "#a3e4d7", "#d1f2eb"],
                borderWidth: 1,
                borderColor: "#fff"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "right", labels: { boxWidth: 12 } }
            }
        }
    });
}

function iniciarDashboard() {
    atualizarCards();
    graficoEstado();
    graficoServico();
    graficoCategoria();
}

iniciarDashboard();