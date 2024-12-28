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
}
