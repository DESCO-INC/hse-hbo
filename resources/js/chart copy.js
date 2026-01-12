import ApexCharts from "apexcharts";

window.hboCharts = {};

document.addEventListener("DOMContentLoaded", () => {
    // ✅ Initialize both charts
    initHboByDateChart();
    initHboByCategoryChart();

    // ✅ Fetch chart data with filters
    fetchChartData();
});

function fetchChartData(filters = {}) {
    // Example: you can extend this to include filter inputs (date_from, etc.)
    fetch("/getChartData", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(filters),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                updateHboByDateChart(data.byDate);
                updateHboByCategoryChart(data.byCategory);
            }
        })
        .catch((err) => console.error("Error loading chart data:", err));
}

// ✅ HBO By Date Chart (Line)
function initHboByDateChart() {
    const el = document.querySelector("#hbo-by-date-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "line",
            height: "100%",
            width: "100%",
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        series: [{ name: "Total HBOs", data: [] }],
        xaxis: {
            categories: [],
            labels: { style: { colors: "#6b7280", fontSize: "12px" } },
            axisBorder: { color: "#e5e7eb" },
            axisTicks: { color: "#e5e7eb" },
        },
        yaxis: { labels: { style: { colors: "#6b7280", fontSize: "12px" } } },
        stroke: { curve: "smooth", width: 3, colors: ["#22c55e"] },
        markers: { size: 5, colors: ["#22c55e"], strokeColors: "#fff", strokeWidth: 2, hover: { size: 7 } },
        grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
        dataLabels: { enabled: false },
        legend: { show: false },
        colors: ["#22c55e"],
    };

    window.hboCharts.byDate = new ApexCharts(el, options);
    window.hboCharts.byDate.render();
}

// ✅ HBO By Category Chart (Bar)
function initHboByCategoryChart() {
    const el = document.querySelector("#hbo-by-category-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "bar",
            height: "100%",
            width: "100%",
            toolbar: { show: false },
        },
        series: [{ name: "Total Reports", data: [] }],
        xaxis: {
            categories: [],
            labels: { style: { colors: "#6b7280", fontSize: "12px" } },
            axisBorder: { color: "#e5e7eb" },
            axisTicks: { color: "#e5e7eb" },
        },
        yaxis: { labels: { style: { colors: "#6b7280", fontSize: "12px" } } },
        plotOptions: {
            bar: { borderRadius: 6, columnWidth: "50%", distributed: false },
        },
        grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ["transparent"] },
        colors: ["#22c55e"],
        tooltip: { y: { formatter: (val) => `${val} reports` } },
        legend: { show: false },
    };

    window.hboCharts.byCategory = new ApexCharts(el, options);
    window.hboCharts.byCategory.render();
}

// ✅ Update line chart with formatted month names (e.g., Jan 2025)
function updateHboByDateChart(byDate) {
    if (!window.hboCharts.byDate) return;
    const categories = byDate.map(item => {
        const [year, month] = item.month.split("-");
        const monthName = new Date(year, month - 1).toLocaleString("default", { month: "short" });
        return `${monthName} ${year}`;
    });
    const totals = byDate.map(item => item.total);

    window.hboCharts.byDate.updateOptions({
        xaxis: { categories },
    });
    window.hboCharts.byDate.updateSeries([{ name: "Total HBOs", data: totals }]);
}

// ✅ Update category bar chart
function updateHboByCategoryChart(byCategory) {
    if (!window.hboCharts.byCategory) return;
    const categories = byCategory.map(item => item.category);
    const totals = byCategory.map(item => item.total);

    window.hboCharts.byCategory.updateOptions({
        xaxis: { categories },
    });
    window.hboCharts.byCategory.updateSeries([{ name: "Total Reports", data: totals }]);
}


document.addEventListener("DOMContentLoaded", () => {
    const chartElement = document.querySelector("#hbo-by-subcategory-chart");

    if (chartElement) {
        const options = {
            chart: {
                type: "bar",
                height: "100%",
                width: "100%",
                toolbar: { show: false },
            },
            series: [
                {
                    name: "Total Users", // 🔹 Bar label
                    data: [10, 40, 35, 50, 49, 60],
                },
            ],
            xaxis: {
                categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                labels: {
                    style: { colors: "#6b7280", fontSize: "12px" },
                },
                axisBorder: { color: "#e5e7eb" },
                axisTicks: { color: "#e5e7eb" },
            },
            yaxis: {
                labels: {
                    style: { colors: "#6b7280", fontSize: "12px" },
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,       // 🔹 rounded corners
                    columnWidth: "50%",    // 🔹 bar width
                    distributed: false,    // 🔹 set true if you want each bar a different color
                },
            },
            grid: {
                borderColor: "#f3f4f6",
                strokeDashArray: 4,
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 2,
                colors: ["transparent"],
            },
            colors: ["#22c55e"], // Tailwind green-500
            tooltip: {
                y: {
                    formatter: (val) => `${val} users`,
                },
            },
            legend: { show: false },
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const chartElement = document.querySelector("#hbo-submitted-by-company-chart");

    if (chartElement) {
        const options = {
            chart: {
                type: "pie",
                height: "100%",
                width: "100%",
                toolbar: { show: false },
            },
            series: [44, 55, 13, 33, 22],
            labels: ["IT", "HR", "Finance", "Sales", "Admin"],
            colors: [
                "#AEC6CF", "#77DD77", "#FFB347", "#FFD1DC", "#CBAACB",
                "#FDFD96", "#B5EAD7", "#FFDAC1", "#E2F0CB", "#C7CEEA"
            ],
            fill: {
                opacity: 1,
            },
            theme: {
                monochrome: { enabled: false },
            },
            legend: {
                position: "bottom",
                labels: {
                    colors: "#374151",
                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: "12px",
                    fontWeight: "600",
                    colors: ["#000"],
                },
                dropShadow: { enabled: false },
            },
            responsive: [
                {
                    breakpoint: 640,
                    options: {
                        chart: { width: "100%" },
                        legend: { position: "bottom" },
                    },
                },
            ],
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
    }
});



document.addEventListener("DOMContentLoaded", () => {
    const chartElement = document.querySelector("#hbo-submitted-by-type-chart");

    if (chartElement) {
        const options = {
            chart: {
                type: "donut",
                height: "100%",
                width: "100%",
                toolbar: { show: false },
            },
            series: [44, 55, 13, 33],
            labels: ["IT", "HR", "Finance", "Sales"], // internal only
            colors: ["#22c55e", "#16a34a", "#4ade80", "#86efac"],

            legend: { show: false }, // ✅ remove legend

            dataLabels: {
                enabled: true,
                formatter: (val) => `${val.toFixed(1)}%`, // ✅ only percentage
                style: {
                    fontSize: "12px",
                    fontWeight: "600",
                    colors: ["#000"],
                },
                dropShadow: { enabled: false },
            },

            plotOptions: {
                pie: {
                    donut: {
                        size: "40%", // ✅ smaller hole → thicker donut ring
                        labels: {
                            show: false,
                            total: {
                                show: true,
                                label: "Total",
                                fontSize: "13px",
                                color: "#000",
                                fontWeight: "bold",
                                formatter: (w) =>
                                    w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                            },
                        },
                    },
                },
            },

            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        dataLabels: {
                            style: { fontSize: "10px" },
                        },
                        plotOptions: {
                            pie: { donut: { size: "50%" } }, // ✅ keep thick ring on mobile
                        },
                    },
                },
            ],
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();

        // Resize fix for flex/grid layout
        setTimeout(() => chart.resize(), 300);
    }
});

