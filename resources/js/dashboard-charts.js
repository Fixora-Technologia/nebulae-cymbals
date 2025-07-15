// Dashboard Charts

// Format currency for tooltips
const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

// Top Products Donut Chart
let topProductsDonutChart = null;
// Top Categories Donut Chart
let topCategoriesDonutChart = null;

// Initialize charts when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Get current month and year from the select elements
    const currentMonth = document.getElementById("sales-month").value;
    const currentYear = document.getElementById("sales-year").value;

    // Load charts with initial data
    loadMonthlySalesCharts(currentMonth, currentYear);
    loadSalesComparisonChart();
    loadTopProductsDonutChart(currentMonth, currentYear);
    loadTopCategoriesDonutChart(currentMonth, currentYear);

    // Add event listener for the update button
    document
        .getElementById("update-sales-chart")
        .addEventListener("click", function () {
            const month = document.getElementById("sales-month").value;
            const year = document.getElementById("sales-year").value;
            loadMonthlySalesCharts(month, year);
            loadTopProductsDonutChart(month, year);
            loadTopCategoriesDonutChart(month, year);
        });
});

// Monthly Sales Charts
let monthlySalesValueChart = null;
let monthlySalesQuantityChart = null;

// Sales Comparison Chart
let salesComparisonChart = null;

/**
 * Load top categories donut chart for the selected month/year
 * @param {number} month - Month number (1-12)
 * @param {number} year - Year (e.g., 2023)
 */
function loadTopCategoriesDonutChart(month, year) {
    fetch(`/mindo/dashboard/top-categories?month=${month}&year=${year}`)
        .then((response) => response.json())
        .then((data) => {
            if (topCategoriesDonutChart) {
                topCategoriesDonutChart.destroy();
            }
            const ctx = document
                .getElementById("topCategoriesDonutChart")
                .getContext("2d");
            // Generate colors (5 + 1 for 'Others')
            const palette = [
                "#FF6384",
                "#36A2EB",
                "#FFCE56",
                "#4BC0C0",
                "#9966FF",
                "#BDBDBD",
            ];
            const bgColors = data.labels.map(
                (_, i) => palette[i % palette.length]
            );

            topCategoriesDonutChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Best Selling Categories",
                            data: data.quantities,
                            backgroundColor: bgColors,
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "right",
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const idx = context.dataIndex;
                                    const name = data.labels[idx];
                                    const qty = data.quantities[idx];
                                    const revenue = data.revenues[idx];
                                    const percent = data.percentages[idx];
                                    return `${name}: ${qty} pcs | ${formatCurrency(
                                        revenue
                                    )} | ${percent}%`;
                                },
                            },
                        },
                        title: {
                            display: false,
                        },
                    },
                },
            });
        })
        .catch((error) =>
            console.error("Error loading top categories data:", error)
        );
}

/**
 * Load top products donut chart for the selected month/year
 * @param {number} month - Month number (1-12)
 * @param {number} year - Year (e.g., 2023)
 */
function loadTopProductsDonutChart(month, year) {
    fetch(`/mindo/dashboard/top-products?month=${month}&year=${year}`)
        .then((response) => response.json())
        .then((data) => {
            if (topProductsDonutChart) {
                topProductsDonutChart.destroy();
            }
            const ctx = document
                .getElementById("topProductsDonutChart")
                .getContext("2d");
            // Generate colors (10 + 1 for 'Others')
            const palette = [
                "#36A2EB",
                "#FF6384",
                "#FFCE56",
                "#4BC0C0",
                "#9966FF",
                "#FF9F40",
                "#8BC34A",
                "#F44336",
                "#00BCD4",
                "#E91E63",
                "#BDBDBD",
            ];
            const bgColors = data.labels.map(
                (_, i) => palette[i % palette.length]
            );

            topProductsDonutChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Best Selling Products",
                            data: data.quantities,
                            backgroundColor: bgColors,
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "right",
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const idx = context.dataIndex;
                                    const name = data.labels[idx];
                                    const qty = data.quantities[idx];
                                    const revenue = data.revenues[idx];
                                    const percent = data.percentages[idx];
                                    return `${name}: ${qty} pcs | ${formatCurrency(
                                        revenue
                                    )} | ${percent}%`;
                                },
                            },
                        },
                        title: {
                            display: false,
                        },
                    },
                },
            });
        })
        .catch((error) =>
            console.error("Error loading top products data:", error)
        );
}

/**
 * Load monthly sales charts with data from API
 * @param {number} month - Month number (1-12)
 * @param {number} year - Year (e.g., 2023)
 */
function loadMonthlySalesCharts(month, year) {
    fetch(`/mindo/dashboard/monthly-sales?month=${month}&year=${year}`)
        .then((response) => response.json())
        .then((data) => {
            // Destroy existing charts if they exist
            if (monthlySalesValueChart) {
                monthlySalesValueChart.destroy();
            }
            if (monthlySalesQuantityChart) {
                monthlySalesQuantityChart.destroy();
            }

            // Create Sales Value Chart
            const salesValueCtx = document
                .getElementById("monthlySalesValueChart")
                .getContext("2d");
            monthlySalesValueChart = new Chart(salesValueCtx, {
                type: "bar",
                data: {
                    labels: data.days,
                    datasets: [
                        {
                            label: "Penjualan Harian (Rp)",
                            data: data.values,
                            backgroundColor: "rgba(54, 162, 235, 0.5)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return formatCurrency(value);
                                },
                            },
                            title: {
                                display: true,
                                text: "Nominal (Rp)",
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Tanggal",
                            },
                        },
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return formatCurrency(context.raw);
                                },
                            },
                        },
                    },
                },
            });

            // Create Sales Quantity Chart
            const salesQuantityCtx = document
                .getElementById("monthlySalesQuantityChart")
                .getContext("2d");
            monthlySalesQuantityChart = new Chart(salesQuantityCtx, {
                type: "bar",
                data: {
                    labels: data.days,
                    datasets: [
                        {
                            label: "Penjualan Harian (pcs)",
                            data: data.quantities,
                            backgroundColor: "rgba(75, 192, 192, 0.5)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                            },
                            title: {
                                display: true,
                                text: "Jumlah (pcs)",
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Tanggal",
                            },
                        },
                    },
                },
            });
        })
        .catch((error) =>
            console.error("Error loading monthly sales data:", error)
        );
}

/**
 * Load sales comparison chart for the last 12 months
 */
function loadSalesComparisonChart() {
    fetch("/mindo/dashboard/sales-comparison")
        .then((response) => response.json())
        .then((data) => {
            // Destroy existing chart if it exists
            if (salesComparisonChart) {
                salesComparisonChart.destroy();
            }

            // Create Sales Comparison Chart
            const salesComparisonCtx = document
                .getElementById("salesComparisonChart")
                .getContext("2d");
            salesComparisonChart = new Chart(salesComparisonCtx, {
                type: "line",
                data: {
                    labels: data.months,
                    datasets: [
                        {
                            label: "Nilai Penjualan (Rp)",
                            data: data.values,
                            borderColor: "rgba(54, 162, 235, 1)",
                            backgroundColor: "rgba(54, 162, 235, 0.1)",
                            borderWidth: 2,
                            fill: true,
                            yAxisID: "y",
                        },
                        {
                            label: "Jumlah Penjualan (pcs)",
                            data: data.quantities,
                            borderColor: "rgba(255, 99, 132, 1)",
                            backgroundColor: "rgba(255, 99, 132, 0.1)",
                            borderWidth: 2,
                            fill: true,
                            yAxisID: "y1",
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: "index",
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: "linear",
                            display: true,
                            position: "left",
                            title: {
                                display: true,
                                text: "Nilai Penjualan (Rp)",
                            },
                            ticks: {
                                callback: function (value) {
                                    return formatCurrency(value);
                                },
                            },
                        },
                        y1: {
                            type: "linear",
                            display: true,
                            position: "right",
                            title: {
                                display: true,
                                text: "Jumlah Penjualan (pcs)",
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        },
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.dataset.label || "";
                                    if (label === "Nilai Penjualan (Rp)") {
                                        return (
                                            label +
                                            ": " +
                                            formatCurrency(context.raw)
                                        );
                                    }
                                    return label + ": " + context.raw;
                                },
                            },
                        },
                    },
                },
            });
        })
        .catch((error) =>
            console.error("Error loading sales comparison data:", error)
        );
}
