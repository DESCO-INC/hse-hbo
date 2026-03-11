import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts; // make globally available

// ==============================
// Chart 1: HBO submitted by Date
// ==============================
window.renderHboByDateChart = function (data) {
    // Map series data as-is
    const seriesData = data.map(item => item.Count);

    // Group dates by month for X-axis labels
    const monthLabelsMap = {}; // { "Jan 2026": firstIndexOfMonth }
    data.forEach((item, idx) => {
        const d = new Date(item.Date);
        const monthKey = d.toLocaleString('default', { month: 'short', year: 'numeric' });
        if (!(monthKey in monthLabelsMap)) {
            monthLabelsMap[monthKey] = idx; // store first index of this month
        }
    });

    // Create categories array (same length as series) but only put month name at first occurrence
    const categories = data.map((item, idx) => {
        const d = new Date(item.Date);
        const monthKey = d.toLocaleString('default', { month: 'short', year: 'numeric' });
        return monthLabelsMap[monthKey] === idx ? monthKey : ''; // empty string for other days
    });

    if (window.hboByDateChart) {
        window.hboByDateChart.destroy();
    }

    const options = {
        chart: {
            type: 'line',
            height: '95%',
            zoom: { enabled: false, type: 'none' },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false,
                    customIcons: []
                }
            }
        },
        series: [{
            name: 'HBO Count',
            data: seriesData
        }],
        xaxis: {
            categories: categories,
            labels: {
                rotate: 0,                 // set to 0 if you want horizontal labels
                style: {
                    fontSize: '11px',
                    fontWeight: 'semi-bold'
                }
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true
        },
        grid: {
            show: false
        },
        stroke: {
            curve: 'straight',
            colors: ['#22c55e'],
            width: 2
        },
        markers: {
            size: 1,
            colors: ['#22c55e'],
            strokeColors: '#22c55e',
            shape: 'circle',
            radius: 1,
            hover: { size: 3 }
        },
        tooltip: {
            x: {
                formatter: function (value, { dataPointIndex }) {
                    const d = new Date(data[dataPointIndex].Date);
                    return d.toLocaleDateString('default', { month: 'long', day: 'numeric', year: 'numeric' });
                }
            }
        },
    };

    window.hboByDateChart = new ApexCharts(document.querySelector("#hbo-by-date"), options);
    window.hboByDateChart.render();
}

window.renderHboByCategoryChart = function (data) {
    const categories = data.map(item => item.category);
    const seriesData = data.map(item => item.total);
    const colors = data.map(item => item.color || '#10B981'); // fallback color

    if (window.hboByCategoryChart) {
        window.hboByCategoryChart.destroy();
    }

    const options = {
        chart: {
            type: 'bar',
            height: '95%',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false,
                    customIcons: []
                }
            }
        },
        series: [{
            name: 'HBO Count',
            data: seriesData
        }],
        colors: colors, // use colors from the data
        xaxis: {
            categories: categories,
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
            }
        },
        yaxis: {
            labels: {
                style: { fontSize: "10px", colors: "#374151" },
            },
        },
        legend: {
            show: false
        },
        plotOptions: {
            bar: {
                distributed: true,
                borderRadius: 4,
                horizontal: false,
                columnWidth: '70%',
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            style: {
                fontSize: '12px',
                colors: ['#000']
            },
            offsetY: -15
        },
        tooltip: {
            x: {
                formatter: function (val, { dataPointIndex }) {
                    // show full category name in tooltip
                    return categories[dataPointIndex];
                }
            },
            y: {
                formatter: function (val) {
                    return val; // total count
                }
            }
        }
    };

    window.hboByCategoryChart = new ApexCharts(document.querySelector("#hbo-by-category"), options);
    window.hboByCategoryChart.render();
}


// ======================================
// Chart 3: HBO Submission by Group (Donut)
// ======================================
window.renderHboByGroupChart = function (data) {
    const labels = data.map(item => item.company);
    const seriesData = data.map(item => item.total);

    if (window.hboByCompanyChart) {
        window.hboByCompanyChart.destroy();
    }

    const options = {
        chart: {
            type: 'donut',
            height: '95%',
            toolbar: {
                show: true,
                tools: {
                    download: true,   // only keep download
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false,
                    customIcons: []
                }
            }
        },
        series: seriesData,
        labels: labels,
        legend: {
            show: true,
            position: 'right',      // move legend to the right side
            verticalAlign: 'middle', // aligns the legend vertically
            fontSize: '12px',
            labels: {
                useSeriesColors: true // makes legend text match the slice color
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val; // shows the total count
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',      // size of the inner donut hole
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px'
                        },
                        value: {
                            show: true,
                            fontSize: '14px'
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
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
        ]
    };

    window.hboByCompanyChart = new ApexCharts(document.querySelector("#hbo-by-group"), options);
    window.hboByCompanyChart.render();
}

// ======================================
// Chart: HBO Submission by Type (Pie)
// ======================================
window.renderHboByTypeChart = function (data) {

    const labels = data.map(item => item.type);
    const seriesData = data.map(item => item.total);

    if (window.hboByTypeChart) {
        window.hboByTypeChart.destroy();
    }

    const options = {
        chart: {
            type: 'pie',
            height: '95%',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        series: seriesData,
        labels: labels,
        legend: {
            show: true,
            position: 'left',
            fontSize: '12px',
            labels: {
                useSeriesColors: true // makes legend text match the slice color
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        },

        colors: [
            "#FF5733", // bright red-orange
            "#33FF57", // bright green
            "#3357FF", // bright blue
            "#FF33A8", // bright pink
        ],
    };

    window.hboByTypeChart = new ApexCharts(
        document.querySelector("#hbo-by-type"),
        options
    );

    window.hboByTypeChart.render();
}


// ======================================
// Chart: HBO Reported by Sub-Category
// ======================================
window.renderHboBySubCategoryChart = function (data) {

    data = data.slice(0, 10);
    const categories = data.map(item => item.sub_category);
    const seriesData = data.map(item => item.total);
    const colors = data.map(item => item.color || '#10B981');

    if (window.hboBySubCategoryChart) {
        window.hboBySubCategoryChart.destroy();
    }

    const options = {
        chart: {
            type: 'bar',
            height: '95%',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },

        series: [{
            name: 'HBO Count',
            data: seriesData
        }],

        colors: colors,

        xaxis: {
            categories: categories,
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151"
                },
                rotate: 0,
                formatter: function (val) {
                    return val.length > 12 ? val.substring(0, 12) + "..." : val;
                }
            }
        },

        yaxis: {
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151"
                }
            }
        },

        legend: {
            show: false
        },

        plotOptions: {
            bar: {
                distributed: true,
                borderRadius: 4,
                horizontal: false,
                columnWidth: '65%',
                dataLabels: {
                    position: 'top'
                }
            }
        },

        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            style: {
                fontSize: '11px',
                colors: ['#000']
            },
            offsetY: -14
        },

        tooltip: {
            x: {
                formatter: function (val, { dataPointIndex }) {
                    // show the full sub-category in tooltip
                    return categories[dataPointIndex];
                }
            },
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        }
    };

    window.hboBySubCategoryChart = new ApexCharts(
        document.querySelector("#hbo-by-subcategory"),
        options
    );

    window.hboBySubCategoryChart.render();
}

// ======================================
// Chart: HBO Submitted by Week
// ======================================
window.renderHboByWeekChart = function (data) {

    const categories = data.map(item => item.week);
    const seriesData = data.map(item => item.total);

    if (window.hboByWeekChart) {
        window.hboByWeekChart.destroy();
    }

    const options = {
        chart: {
            type: 'line',
            height: '95%',
            zoom: { enabled: false, type: 'none' },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        series: [{
            name: 'HBO Count',
            data: seriesData
        }],
        colors: ['#5895f7'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 3
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151"
                }
            },
            title: {
                text: 'Work Week',
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
                    color: '#374151'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: "10px",
                    colors: "#374151"
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        tooltip: {
            x: {
                formatter: function (val, { dataPointIndex }) {
                    return "Week " + data[dataPointIndex].week;
                }
            },
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        },
        grid: {
            show: true
        }
    };

    window.hboByWeekChart = new ApexCharts(
        document.querySelector("#hbo-by-week"),
        options
    );

    window.hboByWeekChart.render();
}

// ======================================
// Chart: Weekly Summary Chart
// ======================================
window.renderWeeklySummaryChart = function (data) {
    // Weekdays
    const weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    // Prepare series
    const series = data.map(week => ({
        name: week.week_label,
        data: week.days
    }));

    if (window.weeklySummaryChart) {
        window.weeklySummaryChart.destroy();
    }

    const options = {
        chart: {
            type: 'line',
            zoom: { enabled: false, type: 'none' },
            height: '95%',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        series: series,
        xaxis: {
            categories: weekdays,
            title: {
                text: 'Day of Week',
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    color: '#374151'
                }
            }
        },
        yaxis: {
            title: {
                text: 'HBO Count',
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    color: '#374151'
                }
            },
            labels: {
                style: {
                    fontSize: '10px',
                    colors: '#374151'
                }
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        markers: {
            size: 4
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        },
        legend: {
            position: 'top'
        },
        colors: ["#f87171", "#3b82f6", "#22c55e"], // customize colors per week
        grid: {
            borderColor: '#E5E7EB',
            row: {
                colors: ['#f9f9f9', 'transparent'], // alternating row colors
                opacity: 0.5
            }
        }
    };

    window.weeklySummaryChart = new ApexCharts(
        document.querySelector("#weekly-summary"),
        options
    );

    window.weeklySummaryChart.render();
}