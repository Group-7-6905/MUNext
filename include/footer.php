<!-- ======================= Newsletter Start ============================ -->
<section class="space bg-cover" style="background:#862633 url(assets/img/landing-bg.png) no-repeat;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h6 class="text-light mb-0">Subscribe Now</h6>
                    <h2 class="ft-bold text-light">Get All New Job Notifications</h2>
                </div>
            </div>
        </div>

        <div class="row align-items-center justify-content-center">
            <div class="col-xl-7 col-lg-10 col-md-12 col-sm-12 col-12">
                <form id="newsletterForm" class="bg-white rounded p-1" method="POST" action="newsletter-handler.php">
                    <div class="row no-gutters">
                        <div class="col-xl-9 col-lg-9 col-md-8 col-sm-8 col-8">
                            <div class="form-group mb-0 position-relative">
                                <input type="email" name="email" id="newsletterEmail" class="form-control lg left-ico"
                                    placeholder="Enter Your Email Address" required>
                                <i class="bnc-ico lni lni-envelope"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-4">
                            <div class="form-group mb-0 position-relative">
                                <button class="btn full-width custom-height-lg bg-dark text-white fs-md" type="submit"
                                    id="subscribeBtn">
                                    Subscribe
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Response Message -->
                <div id="newsletterResponse" class="mt-3 text-center" style="display: none;">
                    <div class="alert alert-dismissible fade show" role="alert">
                        <span id="responseMessage"></span>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======================= Newsletter End ============================ -->

<script>
// Newsletter Subscription Handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('newsletterEmail');
    const submitBtn = document.getElementById('subscribeBtn');
    const responseDiv = document.getElementById('newsletterResponse');
    const responseMessage = document.getElementById('responseMessage');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = emailInput.value.trim();

            // Simple email validation
            if (!email || !isValidEmail(email)) {
                showResponse('Please enter a valid email address.', 'danger');
                return;
            }

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm mr-2"></span>Subscribing...';

            // Create FormData
            const formData = new FormData();
            formData.append('email', email);

            // Send AJAX request
            fetch('newsletter-handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResponse(data.message, 'success');
                        emailInput.value = '';
                    } else {
                        showResponse(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponse('An error occurred. Please try again later.', 'danger');
                })
                .finally(() => {
                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Subscribe';
                });
        });
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showResponse(message, type) {
        responseMessage.innerHTML = message;
        const alertDiv = responseDiv.querySelector('.alert');
        alertDiv.className = 'alert alert-dismissible fade show alert-' + type;
        responseDiv.style.display = 'block';

        // Auto hide after 8 seconds
        setTimeout(function() {
            responseDiv.style.display = 'none';
        }, 8000);
    }
});
</script>

<style>
#newsletterResponse .alert {
    border-radius: 8px;
    padding: 15px 20px;
    font-size: 14px;
}

#newsletterResponse .alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

#newsletterResponse .alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}
</style>



<!-- ============================ Footer Start ================================== -->
<footer class="dark-footer skin-dark-footer style-2">
    <div class="footer-middle">
        <div class="container">
            <div class="row">

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                    <div class="footer_widget">
                        <!-- <img src="assets/img/logo-light.png" class="img-footer small mb-2" alt="" /> -->
                        <strong>MUNext</strong>

                        <div class="address mt-2">
                            MUN NL<br>Canada
                        </div>
                        <div class="address mt-3">
                            +1-123-456 7890<br>support@munext.com
                        </div>
                        <div class="address mt-2">
                            <ul class="list-inline">
                                <li class="list-inline-item"><a href="#" class="theme-cl"><i
                                            class="lni lni-facebook-filled"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="theme-cl"><i
                                            class="lni lni-twitter-filled"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="theme-cl"><i
                                            class="lni lni-youtube"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="theme-cl"><i
                                            class="lni lni-instagram-filled"></i></a></li>
                                <li class="list-inline-item"><a href="#" class="theme-cl"><i
                                            class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <div class="footer_widget">
                        <h4 class="widget_title">For Employers</h4>
                        <ul class="footer-menu">
                            <li><a href="dashboard/">Explore Candidates</a></li>
                            <li><a href="dashboard/">Submit Job</a></li>
                            <li><a href="dashboard/">Shortlisted</a></li>
                            <li><a href="dashboard/">Dashboard</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <div class="footer_widget">
                        <h4 class="widget_title">For Candidates</h4>
                        <ul class="footer-menu">
                            <li><a href="dashboard/">Explore All Jobs</a></li>
                            <li><a href="dashboard/">Browse Categories</a></li>
                            <li><a href="dashboard/">Saved Jobs</a></li>
                            <li><a href="dashboard/">Dashboard</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <div class="footer_widget">
                        <h4 class="widget_title">Links</h4>
                        <ul class="footer-menu">
                            <li><a href="about-us.php">Who We Are?</a></li>
                            <li><a href="about-us.php#mission">Our Mission</a></li>
                            <li><a href="login.php">Sign Up</a></li>
                            <li><a href="login.php">Sign In</a></li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="footer-bottom br-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 col-md-12 text-center">
                    <p class="mb-0">© 2025 MUNext</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ============================ Footer End ================================== -->

<!-- Log In Modal -->
<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginmodal" aria-hidden="true">
    <div class="modal-dialog modal-xl login-pop-form" role="document">
        <div class="modal-content" id="loginmodal">
            <div class="modal-headers">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="ti-close"></span>
                </button>
            </div>

            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h2 class="m-0 ft-regular">Login</h2>
                </div>

                <form method="post">
                    <div class="form-group">
                        <label>User Name</label>
                        <input type="text" class="form-control" placeholder="Username*" name="username">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Password*" name="password">
                    </div>

                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-1">
                                <input id="dd" class="checkbox-custom" name="dd" type="checkbox">
                                <label for="dd" class="checkbox-custom-label">Remember Me</label>
                            </div>
                            <div class="eltio_k2">
                                <a href="forgot-password.php" class="theme-cl">Lost Your Password?</a>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-1">
                                <?php echo $Lerror; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="login_btn"
                            class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
                    </div>

                    <div class="form-group text-center mb-0">
                        <p class="extra">Not a member?<a href="login.php" class="text-dark"> Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->

<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


<script>
(function() {
    var input = document.getElementById('customFile');
    var label = document.querySelector('label[for="customFile"]');
    var display = document.querySelector('.selected-file-name');

    if (!input) return;

    input.addEventListener('change', function(e) {
        var fileName = 'No file chosen';
        if (this.files && this.files.length > 0) {
            fileName = this.files[0].name;
        }
        // Update bootstrap custom-file label (if present)
        if (label) label.textContent = fileName;
        // Update helper text
        if (display) display.textContent = fileName;
    });
})();
</script>





<!-- Toast Notification Script -->
<script>
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) {
        console.error('Toast element not found');
        return;
    }

    toast.className = '';

    let icon = '';
    switch (type) {
        case 'success':
            icon = '✓';
            break;
        case 'error':
            icon = '✕';
            break;
        case 'warning':
            icon = '⚠';
            break;
        case 'info':
            icon = 'ℹ';
            break;
    }

    toast.innerHTML = '<span style="font-size: 1.2rem;">' + icon + '</span><span>' + message + '</span>';
    toast.className = 'show ' + type;

    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}

// Password validation for registration
document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.getElementById('registrationForm');
    if (regForm) {
        regForm.addEventListener('submit', function(e) {
            const password = document.getElementById('regPassword').value;
            const confirmPassword = document.getElementById('regConfirmPassword').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                showToast('Passwords do not match!', 'error');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                showToast('Password must be at least 6 characters long!', 'error');
                return false;
            }
        });
    }
});
</script>

<!-- Render Toast from Session -->
<?php  echo Toast::render(); ?>