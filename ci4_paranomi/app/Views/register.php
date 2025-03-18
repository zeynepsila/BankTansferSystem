<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Kayıt Ol</h2>

    <p id="error-message" style="color:red; display:none;"></p>
    <p id="success-message" style="color:green; display:none;"></p>

    <form id="registerForm">
        <label>Kullanıcı Adı:</label><br>
        <input type="text" id="username" required><br><br>

        <label>Şifre:</label><br>
        <input type="password" id="password" required><br><br>

        <button type="submit">Kayıt Ol</button>
    </form>

    <br>
    <a href="/login">Zaten hesabın var mı? Giriş Yap</a>

    <script>
    $(document).ready(function() {
        $('#registerForm').submit(function(event) {
            event.preventDefault();
            
            let username = $('#username').val();
            let password = $('#password').val();

            $.ajax({
                url: 'http://localhost:8888/api/register', 
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ username, password, role: 'user' }),
                success: function() {
                    alert('Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz.');
                    window.location.href = '/login';
                },
                error: function(xhr) {
                    alert('Kayıt başarısız! ' + (xhr.responseJSON?.error || "Hata oluştu"));
                }
            });
        });
    });
    </script>

</body>
</html>
