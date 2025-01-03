if (document.querySelector('#termsModal')) {
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.cookie.includes('terms_agreed=true')) {
            var termsModal = $('#termsModal');
            termsModal.show();
        }
    });

    console.log(document.cookie);

    $('.btn-close-modal-tos').click(function() {
        console.log('clicked');
        var termsModal = $('#termsModal');
        termsModal.hide();
    });

    document.getElementById('confirmAgreeButton').addEventListener('click', function() {
        fetch('/agree_terms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    var termsModal = $('#termsModal');
                    termsModal.hide();
                }
            });
    });
}
