// PRINT TOP DATA
getDashboardData();
function getDashboardData() {
    var url = $('#get-dashboard').val();
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            $('#total_businesses').text(res.total_businesses);
            $('#expired_businesses').text(res.expired_businesses);
            $('#plan_subscribes').text(res.plan_subscribes);
            $('#business_categories').text(res.business_categories);
            $('#total_plans').text(res.total_plans);
            $('#total_staffs').text(res.total_staffs);
        }
    });
}

// PRINT TOP DATA END

$(document).ready(function() {
    getYearlySubscriptions();
    bestPlanSubscribes();
})


// Subscription Plan chart start

$('.overview-year').on('change', function () {
    let year = $(this).val();
    bestPlanSubscribes(year);
});

let userOverView = false;

function bestPlanSubscribes(year = new Date().getFullYear()) {
    if (userOverView) {
        userOverView.destroy();
    }

    let url = $('#get-plans-overview').val();
    $.ajax({
        url: url += '?year=' + year,
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            var labels = [];
            var data = [];
            var colors = ["#019981", "#E86A09", "#2CE78D", "#0079CE"]; // Define colors for plans
            var backgroundColors = [];
            var borderColors = [];

            $(".subscription-plans").html(""); // Clear existing plans

            if (res.length > 0) {
                // If data exists, display only dynamic plans with solid colors (no transparency)
                res.forEach((planData, index) => {
                    var label = `${planData.plan.subscriptionName}: ${planData.plan_count}`;
                    labels.push(label);

                    // Prevent 0 values from disappearing
                    var count = planData.plan_count > 0 ? planData.plan_count : 0.1;
                    data.push(count);

                    let planColor = colors[index % colors.length]; // Assign color dynamically

                    // Apply full colors without transparency for both cases

                    backgroundColors.push(planData.plan_count > 0 ? planColor + "4D" : planColor + "10"); // Light transparency for 0
                    borderColors.push(planColor);

                    let planElement = `
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <div class="circle plan-indicator" style="background-color: ${planColor};"></div>
                            <p>${planData.plan.subscriptionName}: <strong>${planData.plan_count}</strong></p>
                        </div>
                    `;
                    $(".subscription-plans").append(planElement);
                });
            } else {
                // If no data, show default plans with solid colors
                var defaultPlans = [
                    { subscriptionName: "Free", count: 0 },
                    { subscriptionName: "Standard", count: 0 },
                    { subscriptionName: "Premium", count: 0 },
                ];

                defaultPlans.forEach((plan, index) => {
                    var label = `${plan.subscriptionName}: ${plan.count}`;
                    labels.push(label);

                    // Ensure colors show up even if values are 0
                    var count = plan.count > 0 ? plan.count : 0.1;
                    data.push(count);


                    let planColor = colors[index % colors.length]; // Assign color dynamically
                    backgroundColors.push(planColor + "4D"); // Super light transparent color for 0
                    borderColors.push(planColor);

                    let planElement = `
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <div class="circle plan-indicator" style="background-color: ${planColor};"></div>
                            <p>${plan.subscriptionName}: <strong>${plan.count}</strong></p>
                        </div>
                    `;
                    $(".subscription-plans").append(planElement);
                });
            }

            let inMonths = $("#plans-chart");
            userOverView = new Chart(inMonths, {
                type: 'polarArea',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Total Users",
                        borderWidth: 1,
                        data: data,
                        backgroundColor: backgroundColors, // Use solid colors
                        borderColor: borderColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 10
                            },
                            display: false
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            backgroundColor: "white",
                            titleColor: "black",
                            bodyColor: "black",
                            borderWidth: 2,
                            shadowOffsetX: 3,
                            shadowOffsetY: 3,
                            shadowBlur: 6,
                            shadowColor: "rgba(0, 0, 0, 0.2)",
                        },
                    },
                    scales: {
                        r: {
                            ticks: {
                                display: true
                            },
                            grid: {
                                display: true
                            }
                        }
                    },
                }
            });
        }
        ,
        error: function (xhr, textStatus, errorThrown) {
            console.log('Error fetching user overview data: ' + textStatus);
        }
    });
}

// Subscription Plan chart start End

// Financial Overview Start Here

$('.yearly-statistics').on('change', function () {
    let year = $(this).val();
    getYearlySubscriptions(year);
});


function getYearlySubscriptions(year = new Date().getFullYear()) {
    var url = $('#yearly-subscriptions-url').val();
    $.ajax({
        type: "GET",
        url: url += '?year=' + year,
        dataType: "json",
        success: function (res) {
            var subscriptions = [];
            var totalAmount = 0;

            for (var i = 0; i <= 11; i++) {
                var monthName = getMonthNameFromIndex(i);

                var subscriptionsData = res.find(item => item.month === monthName);
                var amount = subscriptionsData ? subscriptionsData.total_amount : 0;
                subscriptions[i] = amount;
                totalAmount += amount;
            }

            updateTotalAmountDisplay(totalAmount);

            subscriptionChart(subscriptions);
        },
    });
}



function updateTotalAmountDisplay(amount) {
    // $('.green-circle + p strong').text('$' + amount.toFixed(2));
    let formattedAmount = Number.isInteger(amount) ? amount : amount.toFixed(2);
    $('.green-circle + p strong').text('$' + formattedAmount);
}

function getMonthNameFromIndex(index) {
    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return months[index]; // Directly use 0-based indexing
}

let statiSticsValu = null;

function subscriptionChart(subscriptions) {
    if (statiSticsValu) {
        statiSticsValu.destroy();
    }

    // Correcting month indexing issue
    let correctedSubscriptions = new Array(12).fill(0);
    subscriptions.forEach((value, index) => {
        if (index >= 0 && index < 12) {
            correctedSubscriptions[index] = value;
        }
    });

    var ctx = document.getElementById('monthly-statistics').getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0.2192, '#00987F');
    gradient.addColorStop(0.803, '#14B8A6');

    var totals = correctedSubscriptions.reduce((acc, val) => acc + val, 0);

    statiSticsValu = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                backgroundColor: gradient,
                label: "Total Subscription Amount: $" + (Number.isInteger(totals) ? totals : totals.toFixed(2)),
                fill: true,
                borderWidth: 0,
                borderColor: "#C52127",
                data: correctedSubscriptions,
                barThickness: 15,
                borderRadius: 10,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'top',
                    display: false,
                    labels: {
                        usePointStyle: true,
                        padding: 30,
                        font: {
                            weight: 'bold',
                            size: window.innerWidth < 768 ? 12 : 14
                        }
                    }
                },
                tooltip: {
                    mode: "index",
                    intersect: false,
                    backgroundColor: "white",
                    titleColor: "black",
                    bodyColor: "black",
                    borderWidth: 2,
                    shadowOffsetX: 3,
                    shadowOffsetY: 3,
                    shadowBlur: 6,
                    shadowColor: "rgba(0, 0, 0, 0.2)"
                },
            },
            scales: {
                x: {
                    display: true,
                    grid: { display: false },
                    categoryPercentage: 0.5,
                    barPercentage: 0.5,
                    ticks: {
                        font: { size: window.innerWidth < 768 ? 10 : 12 }
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        },
                        font: { size: window.innerWidth < 768 ? 10 : 12 }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        lineWidth: 1,
                        drawBorder: false,
                        drawOnChartArea: true,
                        tickMarkLength: 5,
                        borderDash: [5, 5],
                    }
                }
            },
            layout: {
                padding: { left: 10, right: 10, top: 10, bottom: 10 }
            },
            onResize: function(chart, size) {
                chart.data.datasets[0].barThickness = size.width < 768 ? 10 : 15;
            }
        }
    });
}

// Financial Overview End Here


