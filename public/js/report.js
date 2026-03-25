function reportPage() {
    const now = new Date();

    return {
        selectedMonth: now.getMonth() + 1,
        selectedYear: now.getFullYear(),
        selectedCashier: '',
        loading: true,
        totalSales: 0,
        totalTransactions: 0,
        totalProducts: 0,
        profit: 0,
        totalExpenses: 0,
        chartLabels: [],
        chartData: [],
        expenseChartData: [],
        chartDates: [],
        topProducts: [],
        topExpenses: [],
        topCustomers: [],
        cashierSummary: [],
        insights: {
            salesComparison: null,
            expenseInsight: null,
            topProductInsight: null,
            leastProductInsight: null,
            peakHourInsight: null,
        },
        chartInstance: null,

        months: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],

        init() {
            this.loadData();
        },

        async loadData() {
            this.loading = true;

            const params = new URLSearchParams({
                month: this.selectedMonth,
                year: this.selectedYear,
                cashier: this.selectedCashier,
            });

            try {
                const response = await fetch(`/report/data?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();

                this.totalSales = data.totalSales;
                this.totalTransactions = data.totalTransactions;
                this.totalProducts = data.totalProducts;
                this.profit = data.profit;
                this.totalExpenses = data.totalExpenses;
                this.chartLabels = data.chartLabels;
                this.chartData = data.chartData;
                this.expenseChartData = data.expenseChartData;
                this.chartDates = data.chartDates;
                this.topProducts = data.topProducts;
                this.topExpenses = data.topExpenses;
                this.topCustomers = data.topCustomers;
                this.cashierSummary = data.cashierSummary;
                this.insights = data.insights ?? {};


                this.$nextTick(() => this.initChart());
            } catch (error) {
                console.error('Error loading report data:', error);
            } finally {
                this.loading = false;
            }
        },

        initChart() {
            const canvas = document.getElementById('salesChart');
            if (!canvas || typeof Chart === 'undefined') return;

            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }

            const dates = this.chartDates;

            this.chartInstance = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.chartLabels,
                    datasets: [{
                        label: 'Omzet',
                        data: this.chartData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }, {
                        label: 'Pengeluaran',
                        data: this.expenseChartData,
                        borderColor: 'rgb(100, 116, 139)',
                        backgroundColor: 'rgba(100, 116, 139, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(100, 116, 139)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 16,
                            displayColors: true,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                title: (context) => {
                                    const index = context[0].dataIndex;
                                    if (dates && dates[index]) {
                                        return `${dates[index].day}, ${dates[index].date}`;
                                    }
                                    return context[0].label;
                                },
                                label: (context) => context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 12 },
                                callback: (value) => {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 12 },
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        },
        get insightList() {
            return Object.values(this.insights).filter(v => v !== null && v !== undefined && v !== '');
        },

        get selectedCashierName() {
            const select = document.querySelector('select[x-model="selectedCashier"]')
            if (!select) return ''
            const option = select.options[select.selectedIndex]
            return option ? option.text : ''
        },

        formatRupiah(value) {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        },
    };
}
