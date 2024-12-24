<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Giriş - Nokta Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;ampdisplay=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="assets/vendor/fonts/tabler-icons.css"/>
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />

    <!-- Vendor -->
    <link rel="stylesheet" href="assets/vendor/libs/%40form-validation/form-validation.css" />
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css">

    
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    
  </head>

  <body>
    
    <!-- Content -->
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <!-- Login -->
      <div class="card">
        <div class="card-body">
          <h4 class="mb-1">Hoşgeldiniz! 👋</h4>
          <p class="mb-6">Lütfen hesabınız ile giriş yapın</p>

            <form id="formAuthentication" class="mb-4">
                <div class="mb-6">
                    <label for="email" class="form-label">Email veya kullanıcı adı</label>
                    <input type="text" class="form-control" id="email" name="email-username" placeholder="Email veya kullanıcı adı" autofocus>
                </div>
                <div class="mb-6 form-password-toggle">
                    <label class="form-label" for="password">Şifre</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password" placeholder="••••••••••••" autocomplete="" aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                </div>
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Giriş Yap</button>
                </div>
            </form>
        </div>
      </div>
      <!-- /Register -->
    </div>
  </div>
</div>

<!-- / Content -->

<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/libs/%40form-validation/bootstrap5.js"></script>
<script src="assets/vendor/libs/%40form-validation/auto-focus.js"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<!-- Page JS -->
<script src="assets/js/pages-auth.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('formAuthentication').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way
        // Gather form data
        const formData = new FormData(this);

        // Send login request via AJAX
        fetch('functions/login.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and redirect
                    Swal.fire({
                        icon: 'success',
                        title: 'Giriş Başarılı',
                        text: 'Yönlendiriliyorsunuz!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'pages/dashboard'; // Redirect to dashboard
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Giriş Başarısız',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                alert(error);
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Bir hata oluştu',
                    text: 'Lütfen daha sonra tekrar deneyiniz.'
                });
                console.error('Error:', error);
            });
    });
</script>

  </body>
</html>