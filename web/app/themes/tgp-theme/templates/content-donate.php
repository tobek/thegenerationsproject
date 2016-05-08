<?php
    use Roots\Sage\Extras;

    $BRAINTREE_TOKEN = Extras\braintree_token();
?>
<div class="tgp-form-container">
    <form id="js-donate-form" method="post">
        <?php // Re-using styles made for Contact Form 7 plugin (wpcf7) ?>

        <p><label>
            Full Name<br>
            <span class="wpcf7-form-control-wrap">
                <input type="text" name="full_name">
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
            <span class="form-instructions">(optional)</span>
            <table class="radio-table donate-to js-donate-to">
                <tr>
                    <td><label>
                        <input type="radio" name="donate_to" value="New York">
                        New York Projects
                    </label></td>
                    <td><label>
                        <input type="radio" name="donate_to" value="Bonnie Burke/GMHC">
                        Bonnie Burke/&#8203;GMHC
                    </label></td>
                    <td><label class="js-donate-to-other">
                        <input type="radio" name="donate_to" value="other">
                        Other:
                    </label></td>
                </tr>
                <tr>
                    <td><label>
                        <input type="radio" name="donate_to" value="San Francisco">
                        San Francisco Projects
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
            <table class="radio-table js-donation-amount">
                <tr>
                    <td><label>
                        <input type="radio" name="donation_amount" value="50">
                        $50
                    </label></td>
                    <td><label>
                        <input type="radio" name="donation_amount" value="500">
                        $500
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
                        <input type="radio" name="donation_amount" value="1000">
                        $1000
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
            <span class="form-instructions">
                <?php // Donate with PayPal or enter your credit card information below: ?>
                Please enter your credit card information below:
            </span>
            <div id="braintree-form"></div>
        </div>

        <div class="form-section donate">
            <input class="btn btn-primary js-donate-button" type="submit" value="Donate" disabled="disabled">
        </div>

        <div class="form-section small-print">
            The Generations Project is fiscally sponsored by Social and Environmental Entrepreneurs, a 501(c)3 nonprofit organization. All contributions $100 and over are tax deductible to the fullest extent allowed by law.
            <br><br>
            <a href="/terms">Terms &amp; Conditions</a>
            |
            <a href="/privacy">Privacy Policy</a>
        </div>
    </form>

    <div class="form-success">
        <h2>Thank you!</h2>

        <p>Thank you so much for your contribution to The Generations Project. What we are doing is very important for our communities and we cannot do it without the continued support of people like you. We encourage you to spread our message! Thanks again!</p>
    </div>
</div>

<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js"></script>
<script>
    var donateFormReady, submitDonateForm;

    (function($) {
        $('.js-donate-to label:not(".js-donate-to-other")').on('click', function() {
            $('.js-donate-to-other-input').val('');
        });
        $('.js-donate-to-other').on('click', function() {
            setTimeout(function() {
                $('.js-donate-to-other-input').focus();
            }, 50);
        });
        $('.js-donate-to-other-input').on('click', function() {
            $('.js-donate-to-other input').attr('checked', true);
        });

        $('.js-donation-amount label:not(".js-donation-amount-other")').on('click', function() {
            $('.js-donation-amount-other-input').val('');
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

            if (! data.full_name) {
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

            if (data.donation_amount === 'other') {
                data.donation_amount = data.donation_amount_other;
            }
            if (data.donate_to === 'other') {
                data.donate_to = data.donate_to_other;
            }

            data.nonce = result.nonce;

            $('.js-donate-button').attr('disabled', true);

            $.ajax({
                method: 'POST',
                url: '/api/donate-submit',
                data: data,
                error: function() {
                    alert('Sorry, we had trouble processing your donation!\n\nPlease try again later or contact wes@thegenerationsproject.info');
                    console.error('Error posting to /api/donate-submit:', arguments);
                    $('.js-donate-button').removeAttr('disabled');
                },
                success: function() {
                    $('.tgp-form-container').addClass('is-success');
                    $('html, body').animate({ scrollTop: $('.form-success').offset().top }, 250);
                }
            });
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

    braintree.setup('<?= esc_js($BRAINTREE_TOKEN) ?>', 'dropin', {
        container: 'braintree-form',
        onPaymentMethodReceived: submitDonateForm,
        onReady: donateFormReady
    });

</script>
