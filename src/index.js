document.addEventListener("DOMContentLoaded", function () {
    jQuery(function ($) {
        var productId = $('#add-rating-btn').data('product-id');
        console.log("one2five_vars  ", one2five_vars);
        $('#productId').val(productId);
        var nonce = one2five_vars.nonce;
        var primary_color = one2five_vars.primary_color;
        var accent_color = one2five_vars.accent_color;
        var font_color = one2five_vars.font_color;
        var text_case = one2five_vars.button_text_case;
        var is_product_page = one2five_vars.is_product_page;
        var translationData = one2five_vars.translations;


        var styles = `
        <style>
            .design-line:hover::before,
            .design-line.active::before,
            .functionality-line:hover::before,
            .functionality-line.active::before,
            .quality-line:hover::before,
            .quality-line.active::before {
                background-color: ${accent_color} !important; 
            }
            .star label.active,
            .star label:hover {
                color: ${accent_color} !important; 
            }
        </style>
    `;
        $('head').append(styles);
        var offcanvasHtml = `<div class="one2five-bootstrap">
    <div class="offcanvas offcanvas-end modal-content" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="background-color:#fff;">
        <div class="offcanvas-header">
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="container">
                <div class="row">
                    <form action="" id="myreviewform" method="post">
                        <div class="col-12">
                            <h2 id="offcanvasheading" class="fw-normal">${translationData.welcome?.label || 'Welcome'}</h2>
                            <p>${translationData.welcome?.description || 'We are glad you are here!'}</p>
                            <p>${translationData.write_review?.label || 'Write a Review'}</p>

                            <label for="">${translationData.your_rating?.label || 'Your Rating'} <span class="required">*</span></label>
                            <div class="star mb-4">
                                <input type="radio" id="star1" name="rating" value="1" class="star-radio visually-hidden" required><label for="star1" class="bi-star-fill" data-ratings="1"></label>
                                <input type="radio" id="star2" name="rating" value="2" class="star-radio visually-hidden" required><label for="star2" class="bi-star-fill" data-ratings="2"></label>
                                <input type="radio" id="star3" name="rating" value="3" class="star-radio visually-hidden" required><label for="star3" class="bi-star-fill" data-ratings="3"></label>
                                <input type="radio" id="star4" name="rating" value="4" class="star-radio visually-hidden" required><label for="star4" class="bi-star-fill" data-ratings="4"></label>
                                <input type="radio" id="star5" name="rating" value="5" class="star-radio visually-hidden" required><label for="star5" class="bi-star-fill" data-ratings="5"></label>
                            </div>

                            <label for="">${translationData.design?.label || 'Design'} <span class="required">*</span></label>
                            <p class="mb-2">${translationData.design?.description || 'How would you rate the design?'}</p>
                            <div class="design-rating mb-4">
                                <input type="radio" id="design1" name="designRating" value="1" class="design-radio visually-hidden" required><label for="design1" class="design-line" data-ratings="1"></label>
                                <input type="radio" id="design2" name="designRating" value="2" class="design-radio visually-hidden" required><label for="design2" class="design-line" data-ratings="2"></label>
                                <input type="radio" id="design3" name="designRating" value="3" class="design-radio visually-hidden" required><label for="design3" class="design-line" data-ratings="3"></label>
                                <input type="radio" id="design4" name="designRating" value="4" class="design-radio visually-hidden" required><label for="design4" class="design-line" data-ratings="4"></label>
                                <input type="radio" id="design5" name="designRating" value="5" class="design-radio visually-hidden" required><label for="design5" class="design-line" data-ratings="5"></label>
                            </div>

                            <label for="">${translationData.functionality?.label || 'Functionality'} <span class="required">*</span></label>
                            <p class="mb-2">${translationData.functionality?.description || 'How would you rate the functionality?'}</p>
                            <div class="functionality-rating mb-4">
                                <input type="radio" id="functionality1" name="functionalityRating" value="1" class="functionality-radio visually-hidden" required><label for="functionality1" class="functionality-line" data-ratings="1"></label>
                                <input type="radio" id="functionality2" name="functionalityRating" value="2" class="functionality-radio visually-hidden" required><label for="functionality2" class="functionality-line" data-ratings="2"></label>
                                <input type="radio" id="functionality3" name="functionalityRating" value="3" class="functionality-radio visually-hidden" required><label for="functionality3" class="functionality-line" data-ratings="3"></label>
                                <input type="radio" id="functionality4" name="functionalityRating" value="4" class="functionality-radio visually-hidden" required><label for="functionality4" class="functionality-line" data-ratings="4"></label>
                                <input type="radio" id="functionality5" name="functionalityRating" value="5" class="functionality-radio visually-hidden" required><label for="functionality5" class="functionality-line" data-ratings="5"></label>
                            </div>

                            <label for="">${translationData.quality?.label || 'Quality'} <span class="required">*</span></label>
                            <p class="mb-2">${translationData.quality?.description || 'How would you rate the quality?'}</p>
                            <div class="quality-rating mb-4">
                                <input type="radio" id="quality1" name="qualityRating" value="1" class="quality-radio visually-hidden" required><label for="quality1" class="quality-line" data-ratings="1"></label>
                                <input type="radio" id="quality2" name="qualityRating" value="2" class="quality-radio visually-hidden" required><label for="quality2" class="quality-line" data-ratings="2"></label>
                                <input type="radio" id="quality3" name="qualityRating" value="3" class="quality-radio visually-hidden" required><label for="quality3" class="quality-line" data-ratings="3"></label>
                                <input type="radio" id="quality4" name="qualityRating" value="4" class="quality-radio visually-hidden" required><label for="quality4" class="quality-line" data-ratings="4"></label>
                                <input type="radio" id="quality5" name="qualityRating" value="5" class="quality-radio visually-hidden" required><label for="quality5" class="quality-line" data-ratings="5"></label>
                            </div>

                            <label for="">${translationData.your_nickname?.label || 'Your Nickname'} <span class="required">*</span></label>
                            <input type="text" class="form-control mb-3" id="name" name="name" placeholder="${translationData.your_nickname?.label || 'Your Nickname'}" required>

                            <label for="">${translationData.your_email?.label || 'Your Email'} <span class="required">*</span></label>
                            <input type="email" class="form-control mb-3" id="email" name="email" placeholder="${translationData.your_email?.label || 'Your Email'}" required>

                            <label for="">${translationData.review_title?.label || 'Review Title'} <span class="required">*</span></label>
                            <input type="text" class="form-control mb-3" id="title" name="title" placeholder="${translationData.review_title?.label || 'Review Title'}" required>

                            <label for="">${translationData.write_your_review?.label || 'Write Your Review'} <span class="required">*</span></label>
                            <textarea class="form-control mb-3" id="opinion" name="opinion" rows="4" placeholder="${translationData.write_your_review?.label || 'Write Your Review'}" required></textarea>

                            <label for="" class="form-label">Would you recommend this product? <span class="required">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="isRecommended" id="inlineRadio1" value="Yes" required>
                                        <p>Yes</p>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="isRecommended" id="inlineRadio2" value="No" required>
                                        <p>No</p>
                                    </div>
 <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="termsAgreement" name="termsAgreement" value="isAgree" required>
                                <label class="form-check-label" for="terms">${translationData.terms_conditions?.label || 'I agree to the terms and conditions'}</label>
                            </div>
 <input type="hidden" id="productId" name="productId" value="${productId}">
                                    <input type="hidden" id="nonce" name="nonce" value="${nonce}">
                                    <input type="hidden" id="action" name="action" value="submit_review">
<div id="thankyou"></div>
                                    <button type="submit" class="btn d-block mx-auto w-100 mb-4 border-0 rounded-0" style="background-color: ${primary_color} !important; text-transform: ${text_case};  color: ${font_color} !important;">Submit your review</button>
                                </div>
                           
                            <a href="${translationData.link?.description || 'https://chatgpt.com/c/1ff17a8d-c12a-45c6-8c12-6afa8be6ba32'}">${translationData.term_conditions?.label || 'Terms & Conditions'}</a>
                            <div class="review-summary">
                                <p>${translationData.useful_reviews?.label || 'Useful Reviews'}</p>
                                <p>${translationData.unsynced_products?.label || 'Unsynced Products'}</p>
                                <p>${translationData.synced_products?.label || 'Synced Products'}</p>
                            </div>
                            
                           
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>`;

        var successHTML = `<div class="one2five-bootstrap">
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="successModalLabel">${translationData.success?.label || 'Thank you!'}</h5>
                    <p>${translationData.success?.description || 'Your review has been submitted successfully!'}</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>`;

        if (is_product_page) {
            document.body.insertAdjacentHTML('beforeend', offcanvasHtml);
            document.body.insertAdjacentHTML('beforeend', successHTML);
            document.addEventListener("click", function (event) {
                if (event.target.matches(".add-rating-btn")) {
                    document.getElementById('offcanvasRight').classList.add('show');
                    document.getElementById('offcanvasRight').classList.remove('hide');
                }
            });

            document.addEventListener("click", function (event) {
                if (event.target.matches(".btn-close")) {
                    var offcanvasRight = document.getElementById('offcanvasRight');
                    offcanvasRight.classList.remove('show');
                    offcanvasRight.classList.add('hide');
                }
            });
            $('.star-radio').on('change', function () {
                // Remove 'active' class from all stars
                $('.star label').removeClass('active');
                // Add 'active' class to the clicked star and all stars before it
                $(this).prevAll('input.star-radio').addBack().next('label').addClass('active');
                // Set the rating value in the hidden input field
                $('#ratings').val($(this).data('ratings'));
            });

            $('.design-radio, .functionality-radio, .quality-radio').on('change', function () {
                var rating = $(this).val();
                $(this).siblings('label').removeClass('active').slice(0, rating).addClass('active');
                $('#' + $(this).attr('name')).val(rating);
            });

            $('.review-text').on('click', function (e) {
                e.preventDefault();

                // smooth scroll to section
                $('html, body').animate({
                    scrollTop: $('.woocommerce-tabs ul').offset().top - 32
                }, 2000);

                // remove "active" class.
                $('.woocommerce-tabs ul li').each(function () {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                    }
                });

                $('.woocommerce-tabs ul li a').each(function () {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                    }
                });

                // add "active" class.
                $('.woocommerce-tabs li#tab-title-reviews_tab a').addClass('active');
                $('.woocommerce-tabs li#tab-title-reviews_tab').addClass('active');
                $('.woocommerce-Tabs-panel').css('display', 'none');
                $('#tab-reviews_tab').css('display', 'block');
            });

            // Add event listener for form submission
            $('#myreviewform').on('submit', function (event) {


                // console.log("Form values:", {
                //     rating: $('input[name="rating"]:checked').val(),
                //     designRating: $('input[name="designRating"]:checked').val(),
                //     functionalityRating: $('input[name="functionalityRating"]:checked').val(),
                //     qualityRating: $('input[name="qualityRating"]:checked').val(),
                //     name: $('#name').val(),
                //     email: $('#email').val(),
                //     title: $('#title').val(),
                //     opinion: $('#opinion').val(),
                //     isRecommended: $('input[name="isRecommended"]:checked').val(),
                //     terms: $('#terms').is(':checked')
                // });


                event.preventDefault();
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                submitButton.prop('disabled', true);
                var isValid = true;
                var email = $('#email').val();
                if (email && !isValidEmail(email)) {
                    isValid = false;
                    $('#email').addClass('is-invalid').siblings('.invalid-feedback').text('Please enter a valid email address.');
                    $('#email').focus();
                } else {
                    $('#email').removeClass('is-invalid').siblings('.invalid-feedback').text('');
                }

                // Validation for the opinion field
                var opinion = $('#opinion').val();
                if (opinion.length < 50) {
                    isValid = false;
                    $('#opinionLengthMessage').html('Please enter at least 50 characters.');
                    $('#opinion').addClass('is-invalid');
                    $('#opinion').focus();
                    submitButton.html('Submit your review');
                    submitButton.prop('disabled', false);
                    return; // Stop form submission
                } else {
                    $('#opinionLengthMessage').html('');
                    $('#opinion').removeClass('is-invalid');
                }

                if (!isValid) {
                    submitButton.html('Confirm your review');
                    submitButton.prop('disabled', false);
                    return;
                }

                var formData = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    url: one2five_vars.ajax_url,
                    data: formData,
                    success: function (response) {
                        if (response.success == true) {
                            $('#thankyou').text('Thank you for your review!');
                            var offcanvasRight = document.getElementById('offcanvasRight');
                            offcanvasRight.classList.remove('show');
                            offcanvasRight.classList.add('hide');
                            $('#successModal').modal('show'); // Show success modal
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);

                        } else {
                            var offcanvasRight = document.getElementById('offcanvasRight');
                            offcanvasRight.classList.remove('show');
                            offcanvasRight.classList.add('hide');
                            var errorModalContent = `
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="errorModalLabel">⚠️ Oops!</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p> you have already submitted a review for this product</p>
                                        <p>${response.data}</p> <!-- Append response data to modal body -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    `;
                            $('#successModal .modal-content').html(errorModalContent);
                            $('#successModal').modal('show'); // Show error modal
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        console.log(error);
                        $('#thankyou').text('An error occurred. Please try again later.');
                    },
                    complete: function () {
                        // Re-enable the button and revert the changes
                        submitButton.html('Submit your review');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        }

    });
});

function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

document.addEventListener('DOMContentLoaded', function () {
    var syncButtons = document.querySelectorAll('.sync-product-btn');
    syncButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            var productId = button.dataset.productId;
            var adminNonce = one2five_vars.nonce;
            var data = {
                'action': 'sync_product_ajax',
                'product_id': productId,
                'nonce': adminNonce
            };
            button.innerHTML = '<i class="fas fa-sync fa-spin"></i>';

            jQuery.ajax({
                type: 'POST',
                url: one2five_vars.ajax_url,
                data: data,
                success: function (response) {
                    console.log('AJAX Response:', response); // Log the entire response object
                    if (response !== '0') { // Check if the response is not '0'
                        console.log('Product synced successfully!');
                        alert('Product synced successfully!');
                    } else {
                        console.log('Error syncing product.');
                        alert('Failed to sync product.');
                    }
                    button.disabled = true;
                    button.classList.add('disabled');
                    window.location.reload(true); // Reload the page after processing the AJAX response
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    console.error('AJAX Error:', error);
                    alert('An error occurred while syncing product.');
                    window.location.reload(true); // Reload the page if there's an error
                },
                complete: function () {
                    // Hide loading spinner/icon
                    button.innerHTML = '<i class="fas fa-sync"></i>';
                }
            });
        });
    });
});
