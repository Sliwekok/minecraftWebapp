import {showAlert} from "./alert";
import {updateButton} from "./updateButton";

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

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Time 1", "Time 2", "Time 3"], // Time points
        datasets: [{
            label: 'CPU Usage (%)',
            data: [10, 20, 15], // CPU usage over time
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        }, {
            label: 'Memory Usage (%)',
            data: [30, 40, 35], // Memory usage over time
            borderColor: 'rgba(153, 102, 255, 1)',
            fill: false
        }]
    }
});

$(document).ready(function() {
    setTimeout(
        updateChart(),
        5000
    );
});

function updateChart() {

    $.ajax({
        url: '',
        method: '/server/usage',
        error: function(message) {
            console.log(message)
        },

        success: function(data) {
            console.log(data);

        }
    });
}

const ctx = document.getElementById('serverUsage');

new Chart(ctx, {
    type: 'line',
    labels: [
        "5:00", "4:30", "4:00", "3:30", "3:00",
        "2:30", "2:00", "1:30", "1:00", "0:30",
        "0:00"
    ],
    data: {
        datasets: [
            {
                label: 'CPU usage',
                data: [],
                borderWidth: 1,
                fill: true,
                borderColor: 'rgb(28,86,255)',
                tension: 0.5,
                order: 1,
            },
            {
                label: 'System usage',
                data: [],
                borderWidth: 1,
                fill: true,
                borderColor: 'rgb(90,255,65)',
                tension: 0.5,
                order: 2,
            },
            {
                label: 'RAM usage',
                data: [],
                borderWidth: 1,
                fill: true,
                borderColor: 'rgb(255,31,31)',
                tension: 0.5,
                order: 3,
            }
        ]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
