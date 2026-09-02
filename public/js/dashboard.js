(function() {
  const dataEl = document.getElementById('dashboardChartData');
  if (!dataEl || typeof Chart === 'undefined') return;

  const data = JSON.parse(dataEl.textContent);

  const colors = {
    accent: '#6366f1',
    accentLight: '#a5b4fc',
    success: '#10b981',
    successLight: '#6ee7b7',
    warning: '#f59e0b',
    warningLight: '#fcd34d',
    info: '#3b82f6',
    infoLight: '#93c5fd',
    slate: '#64748b',
    slateLight: '#cbd5e1',
    danger: '#ef4444',
    dangerLight: '#fca5a5',
    purple: '#8b5cf6',
    teal: '#14b8a6',
  };

  const palette = [
    colors.accent,
    colors.success,
    colors.warning,
    colors.info,
    colors.purple,
    colors.teal,
    colors.danger,
    colors.slate,
  ];

  Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
  Chart.defaults.color = '#64748b';
  Chart.defaults.plugins.legend.display = false;

  const gridColor = 'rgba(226, 232, 240, 0.8)';

  function baseOptions(extra) {
    return {
      responsive: true,
      maintainAspectRatio: false,
      ...extra,
    };
  }

  function hasValues(values) {
    return values && values.some(function(v) { return v > 0; });
  }

  function truncateLabel(label, max) {
    if (label.length <= max) return label;
    return label.substring(0, max - 1) + '…';
  }

  // Visão geral — doughnut
  if (document.getElementById('chartOverview') && hasValues(data.overview.values)) {
    new Chart(document.getElementById('chartOverview'), {
      type: 'doughnut',
      data: {
        labels: data.overview.labels,
        datasets: [{
          data: data.overview.values,
          backgroundColor: [colors.accent, colors.success, colors.warning],
          borderWidth: 0,
          hoverOffset: 6,
        }],
      },
      options: baseOptions({
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
          },
        },
      }),
    });

    const legendEl = document.getElementById('legendOverview');
    if (legendEl) {
      const legendColors = [colors.accent, colors.success, colors.warning];
      legendEl.innerHTML = data.overview.labels.map(function(label, i) {
        return '<span class="chart-legend-item">' +
          '<span class="chart-legend-dot" style="background:' + legendColors[i] + '"></span>' +
          label + ' <strong>' + data.overview.values[i] + '</strong></span>';
      }).join('');
    }
  }

  // Cobertura de vínculos — grouped bar
  if (document.getElementById('chartCobertura')) {
    new Chart(document.getElementById('chartCobertura'), {
      type: 'bar',
      data: {
        labels: data.cobertura.labels,
        datasets: [
          {
            label: 'Produtos',
            data: data.cobertura.produtos,
            backgroundColor: colors.accent,
            borderRadius: 6,
            barPercentage: 0.55,
          },
          {
            label: 'Clientes',
            data: data.cobertura.clientes,
            backgroundColor: colors.success,
            borderRadius: 6,
            barPercentage: 0.55,
          },
        ],
      },
      options: baseOptions({
        scales: {
          x: {
            grid: { display: false },
            border: { display: false },
          },
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: gridColor },
            border: { display: false },
          },
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            align: 'end',
            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' },
          },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
          },
        },
      }),
    });
  }

  // Clientes por tipo — doughnut
  if (document.getElementById('chartTipoPessoa') && hasValues(data.tipoPessoa.values)) {
    new Chart(document.getElementById('chartTipoPessoa'), {
      type: 'doughnut',
      data: {
        labels: data.tipoPessoa.labels,
        datasets: [{
          data: data.tipoPessoa.values,
          backgroundColor: [colors.info, colors.purple, colors.slate],
          borderWidth: 0,
          hoverOffset: 6,
        }],
      },
      options: baseOptions({
        cutout: '65%',
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, padding: 16 },
          },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
          },
        },
      }),
    });
  }

  // Valores por produto — grouped bar
  if (document.getElementById('chartFinanceiro') && data.financeiro.labels.length > 0) {
    const labels = data.financeiro.labels.map(function(l) { return truncateLabel(l, 18); });

    new Chart(document.getElementById('chartFinanceiro'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Preço (R$)',
            data: data.financeiro.precos,
            backgroundColor: colors.accent,
            borderRadius: 6,
            barPercentage: 0.7,
          },
          {
            label: 'Imposto (R$)',
            data: data.financeiro.impostos,
            backgroundColor: colors.warningLight,
            borderRadius: 6,
            barPercentage: 0.7,
          },
        ],
      },
      options: baseOptions({
        scales: {
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: { maxRotation: 45, minRotation: 0 },
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            border: { display: false },
            ticks: {
              callback: function(value) {
                return 'R$ ' + value.toLocaleString('pt-BR');
              },
            },
          },
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            align: 'end',
            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' },
          },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) {
                return ctx.dataset.label + ': R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
              },
            },
          },
        },
      }),
    });
  }

  // Clientes por UF — horizontal bar
  if (document.getElementById('chartEstados') && hasValues(data.estados.values)) {
    new Chart(document.getElementById('chartEstados'), {
      type: 'bar',
      data: {
        labels: data.estados.labels,
        datasets: [{
          data: data.estados.values,
          backgroundColor: palette.slice(0, data.estados.labels.length),
          borderRadius: 6,
          barPercentage: 0.6,
        }],
      },
      options: baseOptions({
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: gridColor },
            border: { display: false },
          },
          y: {
            grid: { display: false },
            border: { display: false },
          },
        },
        plugins: {
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
          },
        },
      }),
    });
  }

  // Produtos mais associados — horizontal bar
  if (document.getElementById('chartProdutosAssoc') && hasValues(data.produtosAssociados.values)) {
    const labels = data.produtosAssociados.labels.map(function(l) { return truncateLabel(l, 28); });

    new Chart(document.getElementById('chartProdutosAssoc'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          data: data.produtosAssociados.values,
          backgroundColor: colors.accent,
          borderRadius: 6,
          barPercentage: 0.55,
        }],
      },
      options: baseOptions({
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: gridColor },
            border: { display: false },
          },
          y: {
            grid: { display: false },
            border: { display: false },
          },
        },
        plugins: {
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) {
                return ctx.parsed.x + ' cliente(s)';
              },
            },
          },
        },
      }),
    });
  }

  // Clientes mais associados — horizontal bar
  if (document.getElementById('chartClientesAssoc') && hasValues(data.clientesAssociados.values)) {
    const labels = data.clientesAssociados.labels.map(function(l) { return truncateLabel(l, 28); });

    new Chart(document.getElementById('chartClientesAssoc'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          data: data.clientesAssociados.values,
          backgroundColor: colors.success,
          borderRadius: 6,
          barPercentage: 0.55,
        }],
      },
      options: baseOptions({
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: gridColor },
            border: { display: false },
          },
          y: {
            grid: { display: false },
            border: { display: false },
          },
        },
        plugins: {
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) {
                return ctx.parsed.x + ' produto(s)';
              },
            },
          },
        },
      }),
    });
  }
})();
