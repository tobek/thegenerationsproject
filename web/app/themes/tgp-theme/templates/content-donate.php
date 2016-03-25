<div class="tgp-form-container">
    <form id="js-donate-form" method="post">
        <?php // Re-using styles made for Contact Form 7 plugin (wpcf7) ?>

        <p><label>
            Full Name<br>
            <span class="wpcf7-form-control-wrap">
                <input type="text" name="name">
            </span>
        </label></p>

        <p><label>
            Email Address<br>
            <span class="wpcf7-form-control-wrap">
                <input type="email" name="email">
            </span>
        </label></p>

        <div class="form-section">
            <label>Donate To</label>
            <table class="radio-table">
                <tr>
                    <td><label>
                        <input type="radio" name="donate_to" value="New York">
                        New York
                    </label></td>
                    <td><label>
                        <input type="radio" name="donate_to" value="Bonnie Burke/GMHC">
                        <span class="weird-fake-newline">GMHC</span>
                        Bonnie Burke/
                    </label></td>
                    <td><label class="js-donate-to-other">
                        <input type="radio" name="donate_to" value="other">
                        Other:
                    </label></td>
                </tr>
                <tr>
                    <td><label>
                        <input type="radio" name="donate_to" value="San Francisco">
                        San Francisco
                    </label></td>
                    <td></td>
                    <td>
                        <div class="wpcf7-form-control-wrap">
                            <input type="text" name="donate_to_other" class="js-donate-to-other-input">
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-section">
            <label>Donation Amount</label>
            <table class="radio-table">
                <tr>
                    <td><label>
                        <input type="radio" name="donation_amount" value="50">
                        $50
                    </label></td>
                    <td><label>
                        <input type="radio" name="donation_amount" value="250">
                        $250
                    </label></td>
                    <td><label class="js-donation-amount-other">
                        <input type="radio" name="donation_amount" value="other">
                        Other:
                    </label></td>
                </tr>
                <tr>
                    <td><label>
                        <input type="radio" name="donation_amount" value="100">
                        $100
                    </label></td>
                    <td><label>
                        <input type="radio" name="donation_amount" value="500">
                        $500
                    </label></td>
                    <td>
                        <div class="wpcf7-form-control-wrap money-input">
                            <input type="number" name="donation_amount_other" class="js-donation-amount-other-input">
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-section wpcf7-form-control-wrap">
            <span class="form-instructions">Leave a note with your donation (optional)</span>
            <textarea name="note"></textarea>
        </div>

        <div class="form-section">
            <span class="form-instructions">Donate via PayPal or enter your credit card information below:</span>
            <div id="braintree-form"></div>
        </div>

        <div class="form-section donate">
            <input class="btn btn-primary js-donate-button" type="submit" value="Donate Now" disabled="disabled">
        </div>
    </form>

    <div class="form-success">
        <h2>Thank you!</h2>

        <p>Your donation has been received blah blah blah text.</p>
    </div>
</div>

<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js"></script>
<script>
    var donateFormReady, submitDonateForm;

    (function($) {
        $('.js-donate-to-other').on('click', function() {
            setTimeout(function() {
                $('.js-donate-to-other-input').focus();
            }, 50);
        });
        $('.js-donate-to-other-input').on('click', function() {
            $('.js-donate-to-other input').attr('checked', true);
        });

        $('.js-donation-amount-other').on('click', function() {
            setTimeout(function() {
                $('.js-donation-amount-other-input').focus();
            }, 50);
        });
        $('.js-donation-amount-other-input').on('click', function() {
            $('.js-donation-amount-other input').attr('checked', true);
        });

        donateFormReady = function() {
            $('.braintree-wrap .form-instructions').show();
            $('.js-donate-button').removeAttr('disabled');
        };

        // Braintree library totally blocks form submission and just calls this when payment info ready
        submitDonateForm = function(result) {
            $('.braintree-wrap .form-instructions').hide();

            var data = serializeAsObject($('#js-donate-form'));

            if (! data.name) {
                alert('Please enter your name.');
                return;
            }
            else if (! data.email) {
                alert('Please enter your email address.');
                return;
            }
            else if (! data.donation_amount || (data.donation_amount === 'other' && ! data.donation_amount_other)) {
                alert('Please enter a donation amount!');
                return;
            }

            data.nonce = result.nonce;

            $('.js-donate-button').attr('disabled', true);

            $('.tgp-form-container').addClass('is-success');
            $('html, body').animate({ scrollTop: $('.form-success').offset().top }, 250);
        };

        function serializeAsObject(el) {
            return deparam($(el).serialize().replace(/\+/g, '%20'));
        }
        function deparam(query) {
            var querysplit,
                params = {};
            if (query[0] === '?') {
                query = query.substring(1);
            }
            querysplit = query.split('&');
            $.each(querysplit, function (i, nv) {
                var nvsplit = nv.split('=');
                params[nvsplit[0]] = decodeURIComponent(nvsplit[1]);
            });
            return params;
        }
    })(jQuery);

    // test token
    var BRAINTREE_TOKEN = "eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiJmOWE5MWNjYWIzYzJkZmViMjcxNDRhZTMzMmU2OTIzMGQ0NTk4MTk5YzE2NGNlYzNlMzQ3ODFlMTQzYzIxYmEzfGNyZWF0ZWRfYXQ9MjAxNi0wMy0xNFQwNjoxMjo0OS4zNjIxNDc2NDErMDAwMFx1MDAyNm1lcmNoYW50X2lkPTM0OHBrOWNnZjNiZ3l3MmJcdTAwMjZwdWJsaWNfa2V5PTJuMjQ3ZHY4OWJxOXZtcHIiLCJjb25maWdVcmwiOiJodHRwczovL2FwaS5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvMzQ4cGs5Y2dmM2JneXcyYi9jbGllbnRfYXBpL3YxL2NvbmZpZ3VyYXRpb24iLCJjaGFsbGVuZ2VzIjpbXSwiZW52aXJvbm1lbnQiOiJzYW5kYm94IiwiY2xpZW50QXBpVXJsIjoiaHR0cHM6Ly9hcGkuc2FuZGJveC5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzLzM0OHBrOWNnZjNiZ3l3MmIvY2xpZW50X2FwaSIsImFzc2V0c1VybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXV0aFVybCI6Imh0dHBzOi8vYXV0aC52ZW5tby5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIiwiYW5hbHl0aWNzIjp7InVybCI6Imh0dHBzOi8vY2xpZW50LWFuYWx5dGljcy5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIn0sInRocmVlRFNlY3VyZUVuYWJsZWQiOnRydWUsInBheXBhbEVuYWJsZWQiOnRydWUsInBheXBhbCI6eyJkaXNwbGF5TmFtZSI6IkFjbWUgV2lkZ2V0cywgTHRkLiAoU2FuZGJveCkiLCJjbGllbnRJZCI6bnVsbCwicHJpdmFjeVVybCI6Imh0dHA6Ly9leGFtcGxlLmNvbS9wcCIsInVzZXJBZ3JlZW1lbnRVcmwiOiJodHRwOi8vZXhhbXBsZS5jb20vdG9zIiwiYmFzZVVybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXNzZXRzVXJsIjoiaHR0cHM6Ly9jaGVja291dC5wYXlwYWwuY29tIiwiZGlyZWN0QmFzZVVybCI6bnVsbCwiYWxsb3dIdHRwIjp0cnVlLCJlbnZpcm9ubWVudE5vTmV0d29yayI6dHJ1ZSwiZW52aXJvbm1lbnQiOiJvZmZsaW5lIiwidW52ZXR0ZWRNZXJjaGFudCI6ZmFsc2UsImJyYWludHJlZUNsaWVudElkIjoibWFzdGVyY2xpZW50MyIsImJpbGxpbmdBZ3JlZW1lbnRzRW5hYmxlZCI6dHJ1ZSwibWVyY2hhbnRBY2NvdW50SWQiOiJhY21ld2lkZ2V0c2x0ZHNhbmRib3giLCJjdXJyZW5jeUlzb0NvZGUiOiJVU0QifSwiY29pbmJhc2VFbmFibGVkIjpmYWxzZSwibWVyY2hhbnRJZCI6IjM0OHBrOWNnZjNiZ3l3MmIiLCJ2ZW5tbyI6Im9mZiJ9";

    braintree.setup(BRAINTREE_TOKEN, 'dropin', {
        container: 'braintree-form',
        paypal: {
            button: {
                type: 'checkout'
            }
        },
        onPaymentMethodReceived: submitDonateForm,
        onReady: donateFormReady
    });

</script>
