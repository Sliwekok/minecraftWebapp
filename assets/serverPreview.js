import {showAlert} from "./alert";
import {updateButton} from "./updateButton";
import Chart from 'chart.js/auto';

$(document).on('click', ".confirmation", async function () {
    let div = $(this),
        url = div.data('url'),
        message = div.data('message'),
        confirmation = div.data('confirmation')
    ;

    if (div.hasClass('active')) {
        return;
    }

    if (await showAlert('warning', message, 'Be careful!', confirmation)) {
        $.ajax({
            url: url,
            method: 'get',
            error: function(message) {
                showAlert(
                    'danger',
                    message.responseText,
                    'Oops! Something went wrong'
                );

                return false;
            },

            success: function(message) {
                showAlert(
                    'success',
                    message,
                    'Success'
                );

                updateButton(div)

                return true;
            }
        });
    }
});


$(document).on('click', '.connectToServer', function (e) {
    e.preventDefault();
    $(this).attr('title','Copied');
    $(this).hover();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text()).select();
    document.execCommand("copy");
    $temp.remove();
    setTimeout(() => {
        $(this).attr('title','Click to copy');
    }, 5000);
})

const ctx = document.getElementById('serverUsage');
if (ctx !== null) {
    // Create the chart instance and assign it to a variable
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                "5:00", "4:30", "4:00", "3:30", "3:00",
                "2:30", "2:00", "1:30", "1:00", "0:30",
                "0:00"
            ],
            datasets: [
                {
                    label: 'CPU usage',
                    data: [], // Data will be dynamically updated
                    borderWidth: 1,
                    fill: true,
                    borderColor: 'rgb(28,86,255)',
                    tension: 0.5,
                    order: 1,
                },
                {
                    label: 'RAM usage',
                    data: [], // Data will be dynamically updated
                    borderWidth: 1,
                    fill: true,
                    borderColor: 'rgb(255,31,31)',
                    tension: 0.5,
                    order: 2,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Usage (%)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Set interval to fetch data every 5 seconds
    setInterval(updateChartFromApi, 15000);

    function updateChartFromApi () {
        $.ajax({
            url: '/server/usage',
            method: 'get',
            dataType: 'json',
            error: function (message) {
                showAlert(
                    'danger',
                    message.responseText,
                    'Oops! Something went wrong'
                );
            },
            success: function (data) {
                var data = JSON.parse(data);

                const labels = Object.values(data).map(item => item.time);
                const cpuData = Object.values(data).map(item => parseFloat(item.cpu));
                const ramData = Object.values(data).map(item => parseFloat(item.memory));

                myChart.data.labels = labels;
                myChart.data.datasets[0].data = cpuData;
                myChart.data.datasets[1].data = ramData;
                myChart.update();
            }
        });
    }

    updateChartFromApi();
}
