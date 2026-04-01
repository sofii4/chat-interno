let chartSerie = null;
let chartCategorias = null;
let relatorioAtual = null;

document.addEventListener('DOMContentLoaded', function () {
    const btnCsv = document.getElementById('btn-exportar-csv');
    const btnPdf = document.getElementById('btn-exportar-pdf');

    if (btnCsv) {
        btnCsv.addEventListener('click', exportarCsv);
    }

    if (btnPdf) {
        btnPdf.addEventListener('click', exportarPdf);
    }

    carregarRelatorio();
});

async function carregarRelatorio() {
    try {
        const res = await fetch('/api/chamados/relatorio');
        if (!res.ok) {
            throw new Error('Falha ao carregar relatório');
        }

        const data = await res.json();
        relatorioAtual = data;
        renderKpis(data.resumo || {});
        renderTabelas(data);
        renderGraficos(data);
    } catch (e) {
        console.error(e);
        alert('Não foi possível carregar o relatório de chamados.');
    }
}

function renderKpis(resumo) {
    setText('kpi-total', resumo.total || 0);
    setText('kpi-abertos', resumo.abertos || 0);
    setText('kpi-resolvidos', resumo.resolvidos || 0);
    setText('kpi-cancelados', resumo.cancelados || 0);
    setText('kpi-tempo', formatarDuracaoMinutos(resumo.tempo_medio_minutos || 0));
}

function renderTabelas(data) {
    preencherTabela('tabela-categorias', (data.categorias || []).map(function (item) {
        return `
            <tr>
                <td class="py-2 pr-2 font-medium text-white">${escapeHtml(item.categoria || 'Nao informada')}</td>
                <td class="py-2 text-right text-gray-300">${Number(item.total || 0)}</td>
                <td class="py-2 text-right text-amber-300">${Number(item.abertos || 0)}</td>
                <td class="py-2 text-right text-green-300">${Number(item.resolvidos || 0)}</td>
                <td class="py-2 text-right text-indigo-300">${formatarDuracaoMinutos(item.tempo_medio_minutos || 0)}</td>
            </tr>
        `;
    }));

    preencherTabela('tabela-subcategorias', (data.subcategorias || []).map(function (item) {
        return `
            <tr>
                <td class="py-2 pr-2 text-gray-300">${escapeHtml(item.categoria || 'Nao informada')}</td>
                <td class="py-2 pr-2 font-medium text-white">${escapeHtml(item.subcategoria || 'Nao informada')}</td>
                <td class="py-2 text-right text-gray-300">${Number(item.total || 0)}</td>
                <td class="py-2 text-right text-indigo-300">${formatarDuracaoMinutos(item.tempo_medio_minutos || 0)}</td>
            </tr>
        `;
    }));

    preencherTabela('tabela-solicitantes', (data.solicitantes || []).map(function (item) {
        return `
            <tr>
                <td class="py-2 pr-2 font-medium text-white">${escapeHtml(item.usuario_nome || 'Nao informado')}</td>
                <td class="py-2 text-right text-gray-300">${Number(item.total || 0)}</td>
                <td class="py-2 text-right text-amber-300">${Number(item.abertos || 0)}</td>
                <td class="py-2 text-right text-green-300">${Number(item.resolvidos || 0)}</td>
            </tr>
        `;
    }));

    preencherTabela('tabela-finalizadores', (data.finalizadores || []).map(function (item) {
        return `
            <tr>
                <td class="py-2 pr-2 font-medium text-white">${escapeHtml(item.usuario_nome || 'Nao informado')}</td>
                <td class="py-2 text-right text-green-300">${Number(item.total_resolvidos || 0)}</td>
                <td class="py-2 text-right text-indigo-300">${formatarDuracaoMinutos(item.tempo_medio_minutos || 0)}</td>
            </tr>
        `;
    }));
}

function renderGraficos(data) {
    const serie = data.serie_30_dias || [];
    const categorias = data.categorias || [];

    const labelsSerie = serie.map(function (item) { return formatarDia(item.dia); });
    const abertosSerie = serie.map(function (item) { return Number(item.abertos || 0); });
    const resolvidosSerie = serie.map(function (item) { return Number(item.resolvidos || 0); });

    if (chartSerie) chartSerie.destroy();
    chartSerie = new Chart(document.getElementById('chart-serie'), {
        type: 'line',
        data: {
            labels: labelsSerie,
            datasets: [
                {
                    label: 'Abertos',
                    data: abertosSerie,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, .15)',
                    fill: true,
                    tension: .35,
                },
                {
                    label: 'Resolvidos',
                    data: resolvidosSerie,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, .12)',
                    fill: true,
                    tension: .35,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#cbd5e1' } }
            },
            scales: {
                x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, .12)' } },
                y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, .12)' }, beginAtZero: true }
            }
        }
    });

    const labelsCategorias = categorias.map(function (item) { return item.categoria || 'Nao informada'; });
    const dadosCategorias = categorias.map(function (item) { return Number(item.total || 0); });

    if (chartCategorias) chartCategorias.destroy();
    chartCategorias = new Chart(document.getElementById('chart-categorias'), {
        type: 'doughnut',
        data: {
            labels: labelsCategorias,
            datasets: [{
                data: dadosCategorias,
                backgroundColor: [
                    '#6366f1', '#f59e0b', '#22c55e', '#14b8a6', '#ef4444', '#3b82f6', '#a855f7', '#64748b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#cbd5e1', boxWidth: 12 } }
            }
        }
    });
}

function preencherTabela(id, rows) {
    const el = document.getElementById(id);
    if (!el) return;

    if (!rows.length) {
        el.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-xs text-gray-500">Sem dados disponíveis</td></tr>';
        return;
    }

    el.innerHTML = rows.join('');
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = String(value);
}

function formatarDia(valor) {
    if (!valor) return '';
    const d = new Date(String(valor) + 'T00:00:00-03:00');
    if (isNaN(d.getTime())) return String(valor);
    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
}

function formatarDuracaoMinutos(minutos) {
    const total = Number(minutos || 0);
    if (!total || total < 1) return '0h';
    const horas = total / 60;
    if (horas < 1) {
        return Math.round(total) + 'min';
    }
    return horas.toFixed(horas >= 10 ? 0 : 1) + 'h';
}

function escapeHtml(valor) {
    return String(valor || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function exportarCsv() {
    window.location.href = '/api/chamados/relatorio/csv';
}

function exportarPdf() {
    if (!relatorioAtual) {
        alert('Aguarde o carregamento do relatório para exportar o PDF.');
        return;
    }

    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert('Biblioteca de PDF indisponível no momento.');
        return;
    }

    const jsPDF = window.jspdf.jsPDF;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
    const resumo = relatorioAtual.resumo || {};

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.text('Relatorio de Chamados', 40, 40);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Gerado em: ' + new Date().toLocaleString('pt-BR'), 40, 58);

    const kpis = [
        ['Total', Number(resumo.total || 0)],
        ['Abertos', Number(resumo.abertos || 0)],
        ['Resolvidos', Number(resumo.resolvidos || 0)],
        ['Cancelados', Number(resumo.cancelados || 0)],
        ['Tempo Medio', formatarDuracaoMinutos(resumo.tempo_medio_minutos || 0)]
    ];

    doc.autoTable({
        startY: 76,
        head: [['Indicador', 'Valor']],
        body: kpis,
        styles: { font: 'helvetica', fontSize: 9 },
        headStyles: { fillColor: [79, 70, 229] }
    });

    const categoriasBody = (relatorioAtual.categorias || []).slice(0, 12).map(function (item) {
        return [
            item.categoria || 'Nao informada',
            Number(item.total || 0),
            Number(item.abertos || 0),
            Number(item.resolvidos || 0),
            formatarDuracaoMinutos(item.tempo_medio_minutos || 0)
        ];
    });

    const subcategoriasBody = (relatorioAtual.subcategorias || []).slice(0, 12).map(function (item) {
        return [
            item.categoria || 'Nao informada',
            item.subcategoria || 'Nao informada',
            Number(item.total || 0),
            formatarDuracaoMinutos(item.tempo_medio_minutos || 0)
        ];
    });

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 14,
        head: [['Categoria', 'Total', 'Abertos', 'Resolvidos', 'Tempo medio']],
        body: categoriasBody.length ? categoriasBody : [['Sem dados', '-', '-', '-', '-']],
        styles: { font: 'helvetica', fontSize: 8 },
        headStyles: { fillColor: [59, 130, 246] }
    });

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 14,
        head: [['Categoria', 'Subcategoria', 'Total', 'Tempo medio']],
        body: subcategoriasBody.length ? subcategoriasBody : [['Sem dados', '-', '-', '-']],
        styles: { font: 'helvetica', fontSize: 8 },
        headStyles: { fillColor: [16, 185, 129] }
    });

    doc.save('relatorio-chamados.pdf');
}
