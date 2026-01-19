import ApexCharts from "apexcharts";

window.hboCharts = {};

document.addEventListener("DOMContentLoaded", () => {
    // ✅ Initialize both charts
    initHboByDateChart();
    initHboByCategoryChart();
    initHboByCompanyChart();
    initHboByTypeChart();
    initHboBySubcategoryChart();
    initHboVsPobChart();
    initHboVsPobWeeklyChart();
    initHboByWeekChart();
});

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
            labels: {
                rotate: -45, // 🔹 Rotate for daily labels
                trim: true,
                style: {
                    colors: "#6b7280",
                    fontSize: "9px",
                },
            },
            tickPlacement: "on",
            tooltip: { enabled: true },
            axisBorder: { color: "#e5e7eb" },
            axisTicks: { color: "#e5e7eb" },
        },
        yaxis: {
            labels: {
                style: { colors: "#6b7280", fontSize: "9px" },
            },
        },
        stroke: {
            curve: "straight",
            width: 2,
            colors: ["#22c55e"],
        },
        markers: {
            size: 0,
            colors: ["#22c55e"],
            strokeColors: "#fff",
            strokeWidth: 2,
            hover: { size: 7 },
        },
        grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
        dataLabels: { enabled: false },
        legend: { show: false },
        colors: ["#22c55e"],
        tooltip: {
            x: { format: "MMM dd, yyyy" }, // 🔹 show full date on hover
            y: { formatter: (val) => `${val} reports` },
        },
    };

    window.hboCharts.byDate = new ApexCharts(el, options);
    window.hboCharts.byDate.render();
}

window.updateHboByDateChart = function (byDate) {
    if (!window.hboCharts.byDate) return;

    // Map series as { x: date, y: total }
    const seriesData = byDate.map(item => ({
        x: item.day,         // the date for the x-axis
        y: item.total ?? 0   // the total for y
    }));

    window.hboCharts.byDate.updateSeries([{
        name: "Total HBOs",
        data: seriesData
    }]);

    // x-axis labels: show only monthly
    window.hboCharts.byDate.updateOptions({
        xaxis: {
            type: "category",
            labels: {
                rotate: 0,
                trim: false,
                style: { colors: "#6b7280", fontSize: "9px" },
                formatter: function (val, timestamp, index) {
                    const date = new Date(val);
                    if (date.getDate() === 1 || index === 0) {
                        return date.toLocaleDateString("en-US", { month: "short", year: "numeric" });
                    }
                    return "";
                }
            }
        }
    });
};


// ✅ HBO By Category Chart (Vertical Bar)
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
        series: [
            {
                name: "Total Reports",
                data: [], // will be updated dynamically
            },
        ],
        xaxis: {
            categories: [],
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151",
                },
                rotate: 0,
                formatter: function (val) {
                    // truncate labels longer than 6 characters
                    return val.length > 6 ? val.substring(0, 6) + "..." : val;
                },
            },
            axisBorder: { color: "#e5e7eb" },
            axisTicks: { color: "#e5e7eb" },
        },
        yaxis: {
            labels: {
                style: { fontSize: "10px", colors: "#374151" },
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: "70%", // 🔹 wider bars
                dataLabels: { position: "top" },
            },
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toLocaleString(),
            style: {
                fontSize: "10px",
                fontWeight: "600",
                colors: ["#111827"],
            },
        },
        grid: {
            borderColor: "#e5e7eb",
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { left: 5, right: 5 },
        },
        tooltip: {
            x: {
                formatter: function (val, opts) {
                    const dataPointIndex = opts.dataPointIndex;
                    return opts.w.globals.labels[dataPointIndex];
                }
            },
            y: {
                formatter: (val) => `${val} reports`,
            },
        },
        legend: { show: false },
    };

    window.hboCharts.byCategory = new ApexCharts(el, options);
    window.hboCharts.byCategory.render();

    setTimeout(() => window.hboCharts.byCategory.resize(), 300);
}

// ✅ Update bar chart (HBO by Category)
window.updateHboByCategoryChart = function (byCategory) {
    if (!window.hboCharts.byCategory) return;

    // Each data point includes x (category), y (total), fillColor (from JSON)
    const data = byCategory.map(item => ({
        x: item.category ?? "Uncategorized",
        y: item.total ?? 0,
        fillColor: item.color ?? "#22c55e"
    }));

    // Update x-axis categories
    const categories = byCategory.map(item => item.category ?? "Uncategorized");

    window.hboCharts.byCategory.updateOptions({
        xaxis: { categories },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: "70%" // 🔹 match Subcategory width
            },
        }
    });

    window.hboCharts.byCategory.updateSeries([{
        name: "Total Reports",
        data: data
    }]);
};



// ✅ HBO Submission by Company (Donut)
function initHboByCompanyChart() {
    const el = document.querySelector("#hbo-submitted-by-company-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "donut", // ✅ Pie → Donut
            height: "100%",
            width: "100%",
            toolbar: { show: false },
        },

        series: [],
        labels: [],

        colors: [
            "#FF5733", // bright red-orange
            "#33FF57", // bright green
            "#3357FF", // bright blue
            "#FF33A8", // bright pink
            "#FFBD33", // bright yellow-orange
            "#33FFF3", // bright cyan
            "#8D33FF", // bright purple
            "#FF6F33", // bright coral
            "#33FF8D", // bright lime green
            "#FF3380"  // bright magenta
        ],


        legend: {
            position: "right", // ✅ Right side
            fontSize: "11px",
            markers: { width: 8, height: 8, radius: 2 },
            itemMargin: { vertical: 4 },
            labels: { colors: "#374151" },

            // ✅ Make legend scrollable
            height: 220,
            offsetY: 0,
        },

        dataLabels: {
            enabled: true,
            formatter: (val) => `${val.toFixed(1)}%`,
            style: {
                fontSize: "10px",
                fontWeight: "600",
                colors: ["#000"],
            },
            dropShadow: { enabled: false },
        },

        plotOptions: {
            pie: {
                donut: {
                    size: "65%", // ✅ Donut thickness
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: "Total",
                            fontSize: "12px",
                            fontWeight: 600,
                            color: "#374151",
                        },
                    },
                },
                expandOnClick: false,
                customScale: 0.85, // ✅ Smaller to make space for legend
            },
        },

        grid: {
            padding: {
                top: 0,
                right: 10, // ✅ Space for right legend
                bottom: 0,
                left: 0,
            },
        },

        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: { height: "100%" },
                    legend: {
                        position: "bottom", // ✅ Stack on mobile
                        height: undefined,
                        fontSize: "9px",
                    },
                    plotOptions: {
                        pie: {
                            customScale: 1,
                        },
                    },
                },
            },
        ],
    };

    // ✅ Render chart
    window.hboCharts.byCompany = new ApexCharts(el, options);
    window.hboCharts.byCompany.render();

    // ✅ Resize after layout settles
    setTimeout(() => window.hboCharts.byCompany.resize(), 300);
}


// ✅ Update pie chart dynamically
window.updateHboByCompanyChart = function (byCompany) {
    if (!window.hboCharts.byCompany) return;

    const labels = byCompany.map(item => item.company ?? "Unspecified");
    const totals = byCompany.map(item => item.total ?? 0);

    window.hboCharts.byCompany.updateOptions({ labels });
    window.hboCharts.byCompany.updateSeries(totals);
};


// ✅ HBO Submission by Type (Pie)
function initHboByTypeChart() {
    const el = document.querySelector("#hbo-submitted-by-type-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "pie", // ✅ changed from donut
            height: "100%",
            width: "100%",
            toolbar: { show: false },
        },
        series: [],
        labels: [],
        colors: [
            "#FF5733", // bright red-orange
            "#33FF57", // bright green
            "#3357FF", // bright blue
            "#FF33A8", // bright pink
            "#FFBD33", // bright yellow-orange
            "#33FFF3", // bright cyan
            "#8D33FF", // bright purple
            "#FF6F33", // bright coral
            "#33FF8D", // bright lime green
            "#FF3380"  // bright magenta
        ],

        // ✅ Legend below the chart
        legend: {
            show: true,
            position: "left", // ✅ LEFT SIDE
            fontSize: "11px",
            markers: {
                width: 10,
                height: 10,
                radius: 2,
            },
            itemMargin: {
                vertical: 4,
            },

            // ✅ Scrollable legend (same idea as donut)
            height: 220,
        },

        dataLabels: {
            enabled: true,
            formatter: (val) => `${val.toFixed(1)}%`,
            style: {
                fontSize: "10px",
                fontWeight: "600",
                colors: ["#000"],
            },
            dropShadow: { enabled: false },
        },

        plotOptions: {
            pie: {
                customScale: 0.85, // ✅ Makes room for left legend
                expandOnClick: false,
            },
        },

        stroke: {
            show: true,
            width: 2,
            colors: ["#fff"],
        },

        tooltip: {
            y: { formatter: (val) => `${val} reports` },
        },

        responsive: [
            {
                breakpoint: 768,
                options: {
                    legend: {
                        position: "bottom", // ✅ Mobile friendly
                        height: undefined,
                        fontSize: "9px",
                    },
                    plotOptions: {
                        pie: {
                            customScale: 1,
                        },
                    },
                },
            },
        ],
    };

    window.hboCharts.byType = new ApexCharts(el, options);
    window.hboCharts.byType.render();

    setTimeout(() => window.hboCharts.byType.resize(), 300);
}

// ✅ Update Pie Chart
window.updateHboByTypeChart = function (byType) {
    if (!window.hboCharts.byType) return;

    const labels = byType.map(item => item.type ?? "Unspecified");
    const totals = byType.map(item => item.total ?? 0);

    window.hboCharts.byType.updateOptions({ labels });
    window.hboCharts.byType.updateSeries(totals);
};

// ✅ HBO Submitted by Sub-Category (Vertical Bar)
function initHboBySubcategoryChart() {
    const el = document.querySelector("#hbo-by-subcategory-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "bar",
            height: "100%",
            width: "100%",
            toolbar: { show: false },
        },
        series: [
            {
                name: "Total Submissions",
                data: [], // will be updated dynamically
            },
        ],
        xaxis: {
            categories: [],
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151",
                },
                formatter: function (val) {
                    // truncate labels longer than 6 characters
                    return val.length > 6 ? val.substring(0, 6) + "..." : val;
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151",
                },
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: "70%",
                dataLabels: { position: "top" },
            },
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toLocaleString(),
            style: {
                fontSize: "10px",
                fontWeight: "600",
                colors: ["#111827"],
            },
        },
        // 🔹 Removed the hard-coded colors array
        grid: {
            borderColor: "#e5e7eb",
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { left: 5, right: 5 },
        },
        tooltip: {
            x: {
                formatter: function (val, opts) {
                    // show full sub-category name in tooltip
                    const dataPointIndex = opts.dataPointIndex;
                    return opts.w.globals.labels[dataPointIndex];
                }
            },
            y: {
                formatter: (val) => `${val} reports`,
            },
        },
        legend: { show: false },
    };

    window.hboCharts.bySubcategory = new ApexCharts(el, options);
    window.hboCharts.bySubcategory.render();

    setTimeout(() => window.hboCharts.bySubcategory.resize(), 300);
}

// ✅ Update the Sub-Category Chart (Top 10)
window.updateHboBySubcategoryChart = function (bySubcategory) {
    if (!window.hboCharts.bySubcategory) return;

    // 🔹 Take top 10 by total reports
    const top10 = bySubcategory.sort((a, b) => b.total - a.total).slice(0, 10);

    // Each data point includes y value + fillColor
    const data = top10.map(item => ({
        x: item.sub_category ?? "Unspecified",
        y: item.total ?? 0,
        fillColor: item.color ?? "#22c55e" // 🔹 use color from JSON
    }));

    // Update x-axis categories
    const categories = top10.map(item => item.sub_category ?? "Unspecified");

    window.hboCharts.bySubcategory.updateOptions({
        chart: { height: "100%", width: "100%" },
        xaxis: { categories }
    });

    // Update series with per-bar colors
    window.hboCharts.bySubcategory.updateSeries([{
        name: "Total Submissions",
        data: data
    }]);
};



// ✅ HBO vs POB by Company (Horizontal Bar)
function initHboVsPobChart() {
    const el = document.querySelector("#hbo-vs-pob-chart");
    if (!el) return;

    const options = {
        chart: {
            type: "bar",
            height: "100%",
            width: "100%",
            toolbar: { show: true },
        },
        series: [
            { name: "POB", data: [] },
            { name: "HBO", data: [] },
        ],
        plotOptions: {
            bar: {
                horizontal: true, // ✅ horizontal bars
                barHeight: "70%", // ✅ thicker bars
                borderRadius: 5,
                dataLabels: { position: "right" },
            },
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toFixed(0),
            style: {
                fontSize: "10px",
                fontWeight: "600",
                colors: ["#111827"],
            },
        },
        // ✅ For horizontal bars, categories must be in xaxis
        xaxis: {
            categories: [], // ✅ company names go here
            labels: {
                style: {
                    fontSize: "11px",
                    colors: "#374151",
                    fontWeight: 500,
                },
            },
            title: {
                text: "Total Count",
                style: { fontSize: "11px", color: "#6b7280" },
            },
        },
        yaxis: {
            title: {
                text: "Group",
                style: { fontSize: "11px", color: "#6b7280" },
            },
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151",
                },
            },
        },
        colors: ["#22c55e", "#3b82f6"],
        grid: {
            borderColor: "#e5e7eb",
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
            padding: { left: 5, right: 5 },
        },
        tooltip: {
            y: { formatter: (val) => `${val} total` },
        },
        legend: {
            show: true,
            position: "top",
            horizontalAlign: "center",
            labels: { colors: "#374151" },
        },
    };

    window.hboCharts = window.hboCharts || {};
    window.hboCharts.hboVsPob = new ApexCharts(el, options);
    window.hboCharts.hboVsPob.render();

    setTimeout(() => window.hboCharts.hboVsPob.resize(), 300);
}

// ✅ Update HBO vs POB Chart Data
window.updateHboVsPobChart = function (data) {
    if (!window.hboCharts?.hboVsPob) return;

    const pobData = data.POB || {};
    const hboData = data.HBO || {};

    const businessUnit = data.business_unit || "All Business Units";
    const year = data.year || "";
    const week = data.week || "";

    // ✅ Format title with business unit + date range
    let titleText = `HBO vs POB by Group — ${businessUnit}`;
    if (year && week) titleText += ` [Week${week} - ${year}]`;

    const companies = Array.from(
        new Set([...Object.keys(pobData), ...Object.keys(hboData)])
    );

    const pobSeries = companies.map((c) => pobData[c] ?? 0);
    const hboSeries = companies.map((c) => hboData[c] ?? 0);

    // ✅ Update chart options (title + labels)
    window.hboCharts.hboVsPob.updateOptions({
        title: {
            text: titleText,
            align: "left",
            margin: 20,
            offsetY: 10,
            style: {
                fontSize: "18px",
                fontWeight: "bold",
                color: "#111827",
            },
        },
        xaxis: { categories: companies },
    });

    // ✅ Update series data
    window.hboCharts.hboVsPob.updateSeries([
        { name: "POB", data: pobSeries },
        { name: "HBO", data: hboSeries },
    ]);
};

// ✅ HBO vs POB by Week (Vertical Bar)
function initHboVsPobWeeklyChart() {
    const el = document.querySelector("#hbo-vs-pob-weekly-chart");
    if (!el) return;

    const options = {
        chart: { type: "bar", height: 500, toolbar: { show: true } },
        title: {
            text: "HBO vs POB Weekly Report",
            align: "left",
            margin: 20,
            offsetY: 10,
            style: { fontSize: "18px", fontWeight: "bold", color: "#111827" },
        },
        plotOptions: { bar: { horizontal: false, columnWidth: "55%", borderRadius: 6 } },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ["transparent"] },
        xaxis: { categories: [], title: { text: "Date / Weekday" }, labels: { rotate: 0, style: { fontSize: "11px", colors: "#374151" } } },
        yaxis: { title: { text: "Count / Average" }, labels: { style: { fontSize: "11px", colors: "#374151" } } },
        fill: { opacity: 1 },
        colors: ["#22c55e", "#3b82f6"], // green = POB, blue = HBO
        legend: { position: "top", horizontalAlign: "center", labels: { colors: "#374151" } },
        tooltip: { y: { formatter: val => `${val}` } },
        series: [
            { name: "POB", data: [] },
            { name: "HBO", data: [] },
        ],
    };

    const chart = new ApexCharts(el, options);
    chart.render();

    window.hboCharts = window.hboCharts || {};
    window.hboCharts.hboVsPobWeekly = chart;

    // ✅ Updater for new daily + weekly summary format
    window.updateHboVsPobWeeklyChart = function (data) {
        const pob = data.POB ?? [];
        const hbo = data.HBO ?? [];
        const businessUnit = data.business_unit ?? "All Business Units";
        const year = data.year || "";
        const week = data.week || "";

        // Use dates as labels (daily + weekly summary)
        const labels = pob.map(i => i.date);

        const pobData = pob.map(i => i.average ?? 0);
        const hboData = hbo.map(i => i.total ?? 0);

        // ✅ Format title with business unit + date range
        let titleText = `HBO vs POB Weekly Report — ${businessUnit}`;
        if (year && week) titleText += ` [Week${week} - ${year}]`;

        // Update chart
        const chart = window.hboCharts?.hboVsPobWeekly;
        if (chart) {
            chart.updateOptions({
                title: {
                    text: titleText,
                    align: "left",
                    style: { fontSize: "18px", fontWeight: "bold", color: "#111827" },
                },
                xaxis: { categories: labels },
            });

            chart.updateSeries([
                { name: "POB", data: pobData },
                { name: "HBO", data: hboData },
            ]);
        }
    };
}



// ✅ HBO By Week Chart (Line)
function initHboByWeekChart() {
    const el = document.querySelector("#hbo-weekly-chart");
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
            labels: {
                rotate: 0,
                trim: true,
                style: { colors: "#6b7280", fontSize: "9px" },
            },
            tickPlacement: "on",
            tooltip: { enabled: true },
            axisBorder: { color: "#e5e7eb" },
            axisTicks: { color: "#e5e7eb" },
            title: {
                text: "Work week",
                style: {
                    color: "#6b7280",
                    fontSize: "12px",
                    fontWeight: 600,
                },
            },
        },
        yaxis: {
            labels: { style: { colors: "#6b7280", fontSize: "9px" } },
        },
        stroke: {
            curve: "smooth",
            width: 3,
            colors: ["#3b82f6"], // blue line
        },
        markers: {
            size: 2,
            colors: ["#3b82f6"],
            strokeColors: "#fff",
            strokeWidth: 2,
            hover: { size: 5 },
        },
        grid: { borderColor: "#f3f4f6", strokeDashArray: 4 },
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: {
            y: { formatter: (val) => `${val} reports` },
        },
    };

    window.hboCharts.byWeek = new ApexCharts(el, options);
    window.hboCharts.byWeek.render();
}

// ✅ Update HBO By Week Chart
window.updateHboByWeekChart = function (byWeekly) {
    if (!window.hboCharts.byWeek) return;

    // Expecting [{ week_range: "Jan. 1–7 2025", total: 3 }, ...]
    const categories = byWeekly.map(item => item.week);
    const totals = byWeekly.map(item => item.total ?? 0);

    window.hboCharts.byWeek.updateOptions({
        xaxis: { categories },
    });
    window.hboCharts.byWeek.updateSeries([{ name: "Total HBOs", data: totals }]);
};
