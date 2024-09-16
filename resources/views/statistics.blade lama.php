<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Wilayah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <style>
        canvas {
            max-width: 400px;
            max-height: 400px;
        }
        .progress-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .progress-bar-wrapper {
            flex-grow: 1;
            margin-right: 10px;
        }
        .img-wrapper {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .percentage {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <h3 class="text-center">Statistik Data Wilayah</h3>

        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filter-kecamatan" class="form-select">
                    <option value="">Semua Kecamatan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter-kelurahan" class="form-select" disabled>
                    <option value="">Semua Kelurahan</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-2 col-sm-2">
                <h4>Statistik Wilayah</h4>
                <canvas id="wilayahChart"></canvas>
            </div>
            <div class="col-md-2 col-sm-2">
                <h4>Statistik Paslon</h4>
                <canvas id="gambarIdChart"></canvas>
            </div>
            <div class="col-md-2 col-sm-3">
                <h4>Kategori Pemilih</h4>
                <canvas id="kategoriChart"></canvas>
            </div>
            <div class="col-md-2 col-sm-4">
                <div id="progress-bars-container"></div>
            </div>
        </div>

        <!-- Add the progress container -->
        <div class="row mb-3">
            <div class="col-12">
                <div id="progress-container" class="progress" style="display: none;">
                    <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>
        </div>

        <h4 class="mt-5">Data Wilayah</h4>
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kecamatan</th>
                    <th>Kelurahan</th>
                    <th>Nama</th>
                    <th>Paslon</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let wilayahChart, gambarIdChart, kategoriChart;

            // Inisialisasi DataTables
            const table = $('#dataTable').DataTable({
                "pageLength": 10
            });

            loadKecamatan(1312);

            document.getElementById('filter-kecamatan').addEventListener('change', function() {
                const kecamatan = this.value;
                resetSelects(['filter-kelurahan']);
                if (kecamatan) loadKelurahan(kecamatan);
                fetchSurveyData();
            });

            document.getElementById('filter-kelurahan').addEventListener('change', fetchSurveyData);

            function fetchSurveyData() {
                const kecamatan = document.getElementById('filter-kecamatan').value;
                const kelurahan = document.getElementById('filter-kelurahan').value;

                fetch(`/fetch-statistics?kecamatan=${kecamatan || ''}&kelurahan=${kelurahan || ''}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            console.log('Tidak ada data yang tersedia');
                            return;
                        }
                        updateProgressBars(data);
                        updateTable(data);
                        updateCharts(data);
                    })
                    .catch(error => {
                        console.error('Error fetching survey data:', error);
                    });
            }

            function updateProgressBars(data) {
                const totalVotes = data.length;
                const counts = {};

                data.forEach(item => {
                    const calon = item.nama_calon || 'Tidak diketahui';
                    counts[calon] = (counts[calon] || 0) + 1;
                });

                const progressBarsContainer = document.getElementById('progress-bars-container');
                progressBarsContainer.innerHTML = '';

                Object.keys(counts).forEach(calon => {
                    const voteCount = counts[calon];
                    const percentage = ((voteCount / totalVotes) * 100).toFixed(2);

                    const progressContainer = document.createElement('div');
                    progressContainer.classList.add('progress-container');

                    const imgWrapper = document.createElement('div');
                    imgWrapper.classList.add('img-wrapper');

                    const img = document.createElement('img');
                    img.src = getImageSource(calon);
                    img.alt = calon;
                    imgWrapper.appendChild(img);

                    const progressBarWrapper = document.createElement('div');
                    progressBarWrapper.classList.add('progress-bar-wrapper');

                    const progress = document.createElement('div');
                    progress.classList.add('progress');

                    const progressBar = document.createElement('div');
                    progressBar.classList.add('progress-bar', 'bg-success');
                    progressBar.style.width = `${percentage}%`;

                    progress.appendChild(progressBar);
                    progressBarWrapper.appendChild(progress);

                    const percentageLabel = document.createElement('div');
                    percentageLabel.classList.add('percentage');
                    percentageLabel.textContent = `${percentage}%`;

                    progressContainer.appendChild(imgWrapper);
                    progressContainer.appendChild(progressBarWrapper);
                    progressContainer.appendChild(percentageLabel);
                    progressBarsContainer.appendChild(progressContainer);
                });
            }

            function getImageSource(calon) {
                if (calon === 'Hamsuardi dan Kusnadi') {
                    return '{{ asset('images/ok/HK.png') }}';
                } else if (calon === 'Yulianto dan M. Ihpan') {
                    return '{{ asset('images/ok/YM.png') }}';
                } else if (calon === 'Tuanku Jailani dan Syamsul Bahri') {
                    return '{{ asset('images/ok/TS.png') }}';
                } else if (calon === 'Daliyus K. dan Heri Miheldi') {
                    return '{{ asset('images/ok/DH.png') }}';
                } else {
                    return 'https://via.placeholder.com/50';
                }
            }

            function groupByKelurahan(kelurahanData) {
    return kelurahanData.reduce((acc, item) => {
        if (item.kelurahan_name && item.count) {
            if (!acc[item.kelurahan_name]) {
                acc[item.kelurahan_name] = 0;
            }
            acc[item.kelurahan_name] += parseInt(item.count, 10) || 0; // Ensure count is a number
        }
        return acc;
    }, {});
}

function showPopup(calon, voteCount, percentage, kelurahanData, kategoriData) {
    // Group and count kelurahan data
    const groupedKelurahan = groupByKelurahan(kelurahanData);

    // Debugging: Log grouped data
    console.log('Grouped Kelurahan Data:', groupedKelurahan);

    // Convert grouped data to an array of objects
    const kelurahanListItems = Object.keys(groupedKelurahan).map(name => {
        return { kelurahan_name: name, count: groupedKelurahan[name] };
    });

    // Debugging: Log kelurahan list items
    console.log('Kelurahan List Items:', kelurahanListItems);

    // Create HTML for kelurahan list
    const kelurahanList = kelurahanListItems.map(item => `<li>${item.kelurahan_name}: ${item.count}</li>`).join('');

    // Debugging: Log generated HTML for kelurahan list
    console.log('Kelurahan List HTML:', kelurahanList);

    // Create HTML for kategori list
    const kategoriList = kategoriData.map(item => {
        if (item.nama_kategori && item.count) {
            return `<li>${item.nama_kategori}: ${item.count}</li>`;
        } else {
            console.warn('Invalid kategori item:', item);
            return '';
        }
    }).join('');

    // Debugging: Log generated HTML for kategori list
    console.log('Kategori List HTML:', kategoriList);

    // Create popup content
    const popupContent = `
        <div>
            <h5>${calon}</h5>
            <p>Jumlah Suara: ${voteCount}</p>
            <p>Persentase: ${percentage}%</p>
        </div>
        <div>
            <h6>Data Kelurahan Pemilih:</h6>
            <ul>${kelurahanList}</ul>
        </div>
        <div>
            <h6>Kategori Pemilih:</h6>
            <ul>${kategoriList}</ul>
        </div>
    `;

    // Create and style the popup
    const popup = document.createElement('div');
    popup.classList.add('popup');
    popup.innerHTML = popupContent;
    document.body.appendChild(popup);
    popup.style.position = 'fixed';
    popup.style.top = '50%';
    popup.style.left = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
    popup.style.backgroundColor = 'white';
    popup.style.padding = '20px';
    popup.style.boxShadow = '0 0 10px rgba(0,0,0,0.5)';
    popup.style.zIndex = '1000';

    // Add event listener to remove popup on click
    popup.addEventListener('click', () => {
        document.body.removeChild(popup);
    });
}



            function updateTable(data) {
                table.clear();
                data.forEach((item, index) => {
                    table.row.add([
                        index + 1,
                        item.kecamatan_name || '-',
                        item.kelurahan_name || '-',
                        item.nama || '-',
                        item.nama_calon || '-',
                        item.nama_kategori || '-'
                    ]);
                });
                table.draw();
            }

            function updateCharts(data) {
                const wilayahData = prepareChartData(data, 'kecamatan_name', 'Distribusi Wilayah');

                if (wilayahChart) wilayahChart.destroy();
                wilayahChart = new Chart(document.getElementById('wilayahChart').getContext('2d'), {
                    type: 'pie',
                    data: wilayahData,
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                display: true,
                                color: '#fff',
                                formatter: (value, context) => context.chart.data.labels[context.dataIndex],
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const label = wilayahData.labels[index];
                                const voteCount = wilayahData.datasets[0].data[index];
                                const percentage = ((voteCount / data.length) * 100).toFixed(2);
                                const kelurahanData = data.filter(item => item.kecamatan_name === label)
                                    .map(item => ({ kelurahan_name: item.kelurahan_name, count: item.count }));
                                const kategoriData = data.filter(item => item.kecamatan_name === label)
                                    .map(item => ({ nama_kategori: item.nama_kategori, count: item.count }));
                                showPopup(label, voteCount, percentage, kelurahanData, kategoriData);
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });


            }

            function prepareChartData(data, key, label) {
                const counts = {};
                data.forEach(item => {
                    const value = item[key] || 'Tidak diketahui';
                    counts[value] = (counts[value] || 0) + 1;
                });
                return {
                    labels: Object.keys(counts),
                    datasets: [{
                        label: label,
                        data: Object.values(counts),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                };
            }

            function loadKecamatan(kabupatenId) {
                fetch(`/fetch-kecamatan/${kabupatenId}`)
                    .then(response => response.json())
                    .then(data => {
                        const kecamatanSelect = document.getElementById('filter-kecamatan');
                        kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                        data.forEach(kecamatan => {
                            kecamatanSelect.innerHTML += `<option value="${kecamatan.id}">${kecamatan.name}</option>`;
                        });
                    });
            }

            function loadKelurahan(kecamatanId) {
                fetch(`/fetch-kelurahan/${kecamatanId}`)
                    .then(response => response.json())
                    .then(data => {
                        const kelurahanSelect = document.getElementById('filter-kelurahan');
                        kelurahanSelect.disabled = false;
                        kelurahanSelect.innerHTML = '<option value="">Semua Kelurahan</option>';
                        data.forEach(kelurahan => {
                            kelurahanSelect.innerHTML += `<option value="${kelurahan.id}">${kelurahan.name}</option>`;
                        });
                    });
            }

            function resetSelects(ids) {
                ids.forEach(id => {
                    const select = document.getElementById(id);
                    const label = select.previousElementSibling ? select.previousElementSibling.textContent : 'Pilihan';
                    select.innerHTML = `<option value="">Semua ${label}</option>`;
                    select.disabled = true;
                });
            }

            fetchSurveyData();
            setInterval(fetchSurveyData, 10000);
        });
    </script>
</body>
</html>
