
function obterEquipamentos() {
    return [
        { estado: "Ativo", servico: "Urgência", categoria: "Monitorização", criticidade: "Alta", garantia_fim: "2024-05-10", temDoc: true },
        { estado: "Em manutenção", servico: "UCI", categoria: "Suporte de Vida", criticidade: "Suporte de vida", garantia_fim: "2023-12-01", temDoc: false },
        { estado: "Inativo", servico: "Medicina", categoria: "Diagnóstico", criticidade: "Média", garantia_fim: "2025-02-20", temDoc: true }
    ];
}

function contarAtivos(lista) { return lista.filter(e => e.estado === "Ativo").length; }
function contarManutencao(lista) { return lista.filter(e => e.estado === "Em manutenção").length; }
function contarInativos(lista) { return lista.filter(e => e.estado === "Inativo").length; }
function contarGarantiaExpirada(lista) { return lista.filter(e => new Date(e.garantia_fim) < new Date()).length; }
function contarSemDoc(lista) { return lista.filter(e => !e.temDoc).length; }
function contarServicos(lista) { return new Set(lista.map(e => e.servico)).size; }
function contarCriticos(lista) { return lista.filter(e => e.criticidade === "Alta" || e.criticidade === "Suporte de vida").length; }

function contarExpira30(lista) {
    const hoje = new Date();
    const daqui30 = new Date();
    daqui30.setDate(hoje.getDate() + 30);

    return lista.filter(e => {
        const fim = new Date(e.garantia_fim);
        return fim >= hoje && fim <= daqui30;
    }).length;
}

function atualizarCards(lista) {
    document.getElementById("cardsDashboard").innerHTML = `
        <div class="row g-3">

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e8f4fd; color:#1a73e8;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Total</h6>
                        <h3 style="color:#1a73e8;">${lista.length}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e6f9ee; color:#28a745;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Ativos</h6>
                        <h3 style="color:#28a745;">${contarAtivos(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff8e1; color:#f59e0b;">
                        <i class="fa-solid fa-wrench"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Manutenção</h6>
                        <h3 style="color:#f59e0b;">${contarManutencao(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fdecea; color:#dc3545;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Inativos</h6>
                        <h3 style="color:#dc3545;">${contarInativos(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fdecea; color:#dc3545;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Garantia Expirada</h6>
                        <h3 style="color:#dc3545;">${contarGarantiaExpirada(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f3f4f6; color:#6b7280;">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Sem Documentação</h6>
                        <h3 style="color:#6b7280;">${contarSemDoc(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e8f4fd; color:#1a73e8;">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Serviços</h6>
                        <h3 style="color:#1a73e8;">${contarServicos(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fdecea; color:#dc3545;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Criticidade Elevada</h6>
                        <h3 style="color:#dc3545;">${contarCriticos(lista)}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff8e1; color:#f59e0b;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Garantia expira em 30 dias</h6>
                        <h3 style="color:#f59e0b;">${contarExpira30(lista)}</h3>
                    </div>
                </div>
            </div>

        </div>
    `;
}

function graficoEstado(lista) {
    new Chart(document.getElementById("graficoEstado"), {
        type: "bar",
        data: {
            labels: ["Ativos", "Manutenção", "Inativos"],
            datasets: [{
                label: "Equipamentos",
                data: [
                    contarAtivos(lista),
                    contarManutencao(lista),
                    contarInativos(lista)
                ],
                backgroundColor: ["#28a745", "#ffc107", "#dc3545"],
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
}


function graficoServico(lista) {
    const servicos = [...new Set(lista.map(e => e.servico))];
    const valores = servicos.map(s => lista.filter(e => e.servico === s).length);

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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}


function graficoCategoria(lista) {
    const categorias = [...new Set(lista.map(e => e.categoria))];
    const valores = categorias.map(c => lista.filter(e => e.categoria === c).length);

    new Chart(document.getElementById("graficoCategoria"), {
        type: "pie",
        data: {
            labels: categorias,
            datasets: [{
                data: valores,
                backgroundColor: [
                    "#198754",
                    "#20c997",
                    "#6fddc7",
                    "#a3e4d7",
                    "#d1f2eb"
                ],
                borderWidth: 1,
                borderColor: "#fff"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: { boxWidth: 12 }
                }
            }
        }
    });
}

function iniciarDashboard() {
    const lista = obterEquipamentos();
    atualizarCards(lista);
    graficoEstado(lista);
    graficoServico(lista);
    graficoCategoria(lista);
}

iniciarDashboard();