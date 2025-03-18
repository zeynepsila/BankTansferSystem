<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Girişi</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h3>Giriş Yap</h3>

    <p id="error-message" style="color:red; display:none;"></p>
    <p id="success-message" style="color:green; display:none;"></p>

    <form id="loginForm">
        <label for="username">Kullanıcı Adı:</label><br>
        <input type="text" id="username" required><br><br>

        <label for="password">Şifre:</label><br>
        <input type="password" id="password" required><br><br>

        <button type="submit">Giriş Yap</button>
    </form>

    <br>
    <a href="/register">Hesabın yok mu? Kayıt Ol</a>

    <script>
    $(document).ready(function() {
        $('#loginForm').submit(function(event) {
            event.preventDefault();

            let username = $('#username').val();
            let password = $('#password').val();

            $.ajax({
                url: 'http://localhost:8888/api/login',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ username, password }),
                success: function(response) {
                    sessionStorage.setItem('jwtToken', response.token);
                    sessionStorage.setItem('user', JSON.stringify(response.user));

                    $('#success-message').text("Giriş başarılı, yönlendiriliyorsunuz...").show();
                    setTimeout(() => { window.location.href = '/dashboard'; }, 1000);
                },
                error: function(xhr) {
                    $('#error-message').text(xhr.responseJSON?.error || 'Hatalı giriş!').show();
                }
            });
        });
    });
    </script>

</body>
</html>
