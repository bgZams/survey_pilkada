
const gambarIdData = prepareChartData(data, 'nama_calon', 'Jumlah Pemilih');
const kategoriData = prepareChartData(data, 'nama_kategori', 'Jumlah Pemilih');

                if (gambarIdChart) gambarIdChart.destroy();
                gambarIdChart = new Chart(document.getElementById('gambarIdChart').getContext('2d'), {
                    type: 'pie',
                    data: gambarIdData,
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
                                const label = gambarIdData.labels[index];
                                const voteCount = gambarIdData.datasets[0].data[index];
                                const percentage = ((voteCount / data.length) * 100).toFixed(2);
                                showPopup(label, voteCount, percentage, [], []); // Adjust as needed
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                if (kategoriChart) kategoriChart.destroy();
                kategoriChart = new Chart(document.getElementById('kategoriChart').getContext('2d'), {
                    type: 'pie',
                    data: kategoriData,
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
                                const label = kategoriData.labels[index];
                                const voteCount = kategoriData.datasets[0].data[index];
                                const percentage = ((voteCount / data.length) * 100).toFixed(2);
                                showPopup(label, voteCount, percentage, [], []); // Adjust as needed
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
