<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Data Pemilih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <style>
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
        }

        .chart-box {
            position: relative;
            width: 300px;
            height: 300px;
        }

        .chart-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h3 class="text-center">Statistik Data Pemilih</h3>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter-kecamatan">Kecamatan</label>
                <select id="filter-kecamatan" class="form-select">
                    <option value="">Semua Kecamatan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-nagari">Nagari</label>
                <select id="filter-nagari" class="form-select" disabled>
                    <option value="">Semua Nagari</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-paslon">Paslon</label>
                <select id="filter-paslon" class="form-select">
                    <option value="">Semua Paslon</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-kategori">Kategori</label>
                <select id="filter-kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                </select>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <label for="kecamatanChart">Kecamatan</label>
                <canvas id="kecamatanChart"></canvas>
                <div id="kecamatanTotal" class="chart-center"></div>
            </div>

            <div class="chart-box">
                <label for="nagariChart">Nagari</label>
                <canvas id="nagariChart"></canvas>
                <div id="nagariTotal" class="chart-center"></div>
            </div>

            <div class="chart-box">
                <label for="paslonChart">Paslon</label>
                <canvas id="paslonChart"></canvas>
                <div id="paslonTotal" class="chart-center"></div>
            </div>

            <div class="chart-box">
                <label for="slugChart">Kategori</label>
                <canvas id="slugChart"></canvas>
                <div id="slugTotal" class="chart-center"></div>
            </div>
        </div>

        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kecamatan</th>
                    <th>Nagari</th>
                    <th>Nama</th>
                    <th>Paslon</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                function fetchKecamatan() {
                    fetch('/fetch-kecamatan')
                        .then(response => response.json())
                        .then(data => {
                            const kecamatanSelect = document.getElementById('filter-kecamatan');
                            kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                            data.forEach(kecamatan => {
                                kecamatanSelect.innerHTML +=
                                    `<option value="${kecamatan.id}">${kecamatan.name}</option>`;
                            });
                            kecamatanSelect.disabled = false;
                        })
                        .catch(error => console.error('Error fetching kecamatan:', error));
                }

                function fetchNagari(kecamatanId) {
                    fetch(`/fetch-nagari/${kecamatanId}`)
                        .then(response => response.json())
                        .then(data => {
                            const nagariSelect = document.getElementById('filter-nagari');
                            nagariSelect.innerHTML = '<option value="">Semua Nagari</option>';
                            data.forEach(nagari => {
                                nagariSelect.innerHTML +=
                                    `<option value="${nagari.id}">${nagari.name}</option>`;
                            });
                            nagariSelect.disabled = false;
                        })
                        .catch(error => console.error('Error fetching nagari:', error));
                }

                function fetchPaslon() {
                    fetch('/fetch-paslon')
                        .then(response => response.json())
                        .then(data => {
                            const paslonSelect = document.getElementById('filter-paslon');
                            paslonSelect.innerHTML = '<option value="">Semua Paslon</option>';
                            data.forEach(paslon => {
                                paslonSelect.innerHTML +=
                                    `<option value="${paslon.id}">${paslon.nama_calon}</option>`;
                            });
                        })
                        .catch(error => console.error('Error fetching paslon:', error));
                }

                function fetchKategori() {
                    fetch('/fetch-kategori')
                        .then(response => response.json())
                        .then(data => {
                            const kategoriSelect = document.getElementById('filter-kategori');
                            kategoriSelect.innerHTML = '<option value="">Semua Kategori</option>';
                            data.forEach(kategori => {
                                kategoriSelect.innerHTML +=
                                    `<option value="${kategori.id}">${kategori.nama}</option>`;
                            });
                        })
                        .catch(error => console.error('Error fetching kategori:', error));
                }

                function fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId) {
                    let url = `/fetch-statistics`;
                    if (kecamatanId || nagariId || paslonId || kategoriId) {
                        url += `?kecamatan_id=${kecamatanId || ''}&nagari_id=${nagariId || ''}&paslon_id=${paslonId || ''}&kategori_id=${kategoriId || ''}`;
                    }
console.log(url);
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            createDonutChart(
                                'kecamatanChart',
                                Object.values(data.kecamatanCount),
                                Object.keys(data.kecamatanCount),
                                ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                                'kecamatanTotal'
                            );

                            createDonutChart(
                                'nagariChart',
                                Object.values(data.nagariCount),
                                Object.keys(data.nagariCount),
                                ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                                'nagariTotal'
                            );

                            createDonutChart(
                                'paslonChart',
                                Object.values(data.paslonCount),
                                Object.keys(data.paslonCount),
                                ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                                'paslonTotal'
                            );

                            createDonutChart(
                                'slugChart',
                                Object.values(data.slugCount),
                                Object.keys(data.slugCount),
                                ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                                'slugTotal'
                            );
                        })
                        .catch(error => console.error('Error fetching statistics data:', error));
                }

                let charts = {}; // Object to keep track of existing charts

                function createDonutChart(chartId, dataCounts, dataLabels, backgroundColors, totalId) {
                    const ctx = document.getElementById(chartId).getContext('2d');
                    const totalCount = dataCounts.reduce((a, b) => a + b, 0);

                    if (!ctx) {
                        console.error(`Unable to get context for chart ${chartId}`);
                        return;
                    }

                    if (charts[chartId]) {
                        charts[chartId].destroy();
                    }

                    charts[chartId] = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: dataLabels,
                            datasets: [{
                                data: dataCounts,
                                backgroundColor: backgroundColors,
                                hoverBackgroundColor: backgroundColors
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    usePointStyle: true,
                                    enabled: true,
                                    callbacks: {
                                        title: function(tooltipItems) {
                                            return tooltipItems[0].label;
                                        },
                                        label: function(tooltipItem) {
                                            const label = tooltipItem.label || '';
                                            const value = tooltipItem.raw || '';
                                            return `${label}: ${value}`;
                                        }
                                    }
                                },
                                legend: {
                                    display: false // Hide legend if not needed
                                },

                                datalabels: {
                                    display: true,
                                    formatter: (value, context) => {
                                        const label = context.chart.data.labels[context.dataIndex];
                                        const formattedVal = Intl.NumberFormat('en-US', {
                                            minimumFractionDigits: 2,
                                        }).format(value);
                                        return `${label}: ${formattedVal}`;
                                    },
                                    color: '#fff',
                                    backgroundColor: '#404040',
                                },
                            },
                            cutout: '50%', // Size of the donut hole
                            animation: {
                                animateRotate: true,
                                animateScale: true
                            }
                        }
                    });

                    // Ensure the chart is updated
                    const chart = charts[chartId];
                    chart.update(); // Ensure the chart is updated
                    chart.tooltip.setActiveElements(
                        chart.getDatasetMeta(0).data.map((dataPoint, index) => ({
                            datasetIndex: 0,
                            index: index
                        }))
                    );
                    chart.tooltip.update();
                    chart.draw();

                    document.getElementById(totalId).textContent = `Total: ${totalCount}`;
                }

                document.getElementById('filter-kecamatan').addEventListener('change', function() {
                    const kecamatanId = this.value;
                    const nagariId = document.getElementById('filter-nagari').value;
                    const paslonId = document.getElementById('filter-paslon').value;
                    const kategoriId = document.getElementById('filter-kategori').value;
                    fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId);
                    fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId); // Fetch data for the table
                    if (kecamatanId) {
                        fetchNagari(kecamatanId);
                    } else {
                        document.getElementById('filter-nagari').disabled = true;
                    }
                });

                document.getElementById('filter-nagari').addEventListener('change', function() {
                    const nagariId = this.value;
                    const kecamatanId = document.getElementById('filter-kecamatan').value;
                    const paslonId = document.getElementById('filter-paslon').value;
                    const kategoriId = document.getElementById('filter-kategori').value;
                    fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId);
                    fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId);
                });

                document.getElementById('filter-paslon').addEventListener('change', function() {
                    const paslonId = this.value;
                    const kecamatanId = document.getElementById('filter-kecamatan').value;
                    const nagariId = document.getElementById('filter-nagari').value;
                    const kategoriId = document.getElementById('filter-kategori').value;
                    fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId);
                    fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId);
                });

                document.getElementById('filter-kategori').addEventListener('change', function() {
                    const kategoriId = this.value;
                    const kecamatanId = document.getElementById('filter-kecamatan').value;
                    const nagariId = document.getElementById('filter-nagari').value;
                    const paslonId = document.getElementById('filter-paslon').value;
                    fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId);
                    fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId);
                });

                // Inisialisasi DataTables
                const table = $('#dataTable').DataTable({
                    "pageLength": 10
                });

                function fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId) {
                    let url = `/fetch-datatabel`;
                    if (kecamatanId || nagariId || paslonId || kategoriId) {
                        url += `?kecamatan_id=${kecamatanId || ''}&nagari_id=${nagariId || ''}&paslon_id=${paslonId || ''}&kategori_id=${kategoriId || ''}`;
                    }
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                console.log('Tidak ada data yang tersedia');
                                updateTable([]); // Update table with no data
                                return;
                            }
                            updateTable(data);
                        })
                        .catch(error => {
                            console.error('Error fetching survey data:', error);
                        });
                }

                function updateTable(data) {
                    table.clear();
                    if (data.length === 0) {
                        table.row.add(['-', '-', '-', '-', '-', '-']);
                    } else {
                        data.forEach((item, index) => {
                            table.row.add([
                                index + 1,
                                item.kecamatan_name || '-',
                                item.nagari_name || '-',
                                item.nama || '-',
                                item.nama_calon || '-',
                                item.nama_kategori || '-'
                            ]);
                        });
                    }
                    table.draw();
                }

                fetchPaslon();
                fetchKategori();
                fetchKecamatan();
                fetchStatistics();
                fetchSurveyData();
                setInterval(() => {
                    const kecamatanId = document.getElementById('filter-kecamatan').value;
                    const nagariId = document.getElementById('filter-nagari').value;
                    const paslonId = document.getElementById('filter-paslon').value;
                    const kategoriId = document.getElementById('filter-kategori').value;
                    fetchSurveyData(kecamatanId, nagariId, paslonId, kategoriId);
                    fetchKecamatan(kecamatanId, nagariId);
                    fetchStatistics(kecamatanId, nagariId, paslonId, kategoriId);
                }, 10000);
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js">
        </script>

    </div>
</body>

</html>
