$(document).ready(function() {
    $('#loginForm').submit(function(event) {
        event.preventDefault();
        
        let username = $('#username').val();
        let password = $('#password').val();

        $.ajax({
            url: 'http://localhost:8888/api/login', 
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ username: username, password: password }),
            success: function(response) {
                try {
                    let jsonResponse = JSON.parse(response);
                    console.log("API Yanıtı:", jsonResponse);
                    
                    if (jsonResponse.token) {
                        localStorage.setItem('jwtToken', jsonResponse.token);
                        window.location.href = 'http://localhost:8080/dashboard';
                    } else {
                        alert('Giriş başarısız! Geçersiz yanıt.');
                    }
                } catch (error) {
                    console.error("Yanıt JSON formatında değil:", response);
                    alert('Giriş başarısız! Sunucudan beklenmeyen yanıt alındı.');
                }
            },
            error: function(xhr) {
                let errorMessage = "Bilinmeyen hata!";
                
                try {
                    let errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.error) {
                        errorMessage = errorResponse.error;
                    }
                } catch (e) {
                    errorMessage = xhr.responseText;
                }

                alert('Giriş başarısız! ' + errorMessage);
            }
        });
    });
});
