<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Hoşgeldin, <span id="username"></span></h2>
    <p><strong>Bakiye:</strong> <span id="balance"></span> TL</p>

    <h3>Para Transferi</h3>
    <form id="transferForm">
        <label>Alıcı ID:</label><br>
        <input type="number" id="receiver_id" required><br><br>

        <label>Miktar (TL):</label><br>
        <input type="number" step="0.01" id="amount" required><br><br>

        <button type="submit">Gönder</button>
    </form>

    <br>
    <a href="#" id="logout">Çıkış Yap</a>

    <script>
    $(document).ready(function() {
        let user = JSON.parse(sessionStorage.getItem('user'));
        let token = sessionStorage.getItem('jwtToken');

        if (!user || !token) {
            alert("Lütfen giriş yapın.");
            window.location.href = '/login';
            return;
        }

        $('#username').text(user.username);
        $('#balance').text(user.balance.toFixed(2));

        $('#transferForm').submit(function(event) {
            event.preventDefault();

            let receiver_id = $('#receiver_id').val();
            let amount = parseFloat($('#amount').val());

            $.ajax({
                url: 'http://localhost:8888/api/transfer',
                type: 'POST',
                contentType: 'application/json',
                headers: { 'Authorization': 'Bearer ' + token },
                data: JSON.stringify({ sender_id: user.id, receiver_id, amount }),
                success: function(response) {
                    alert('Transfer başarılı!');
                    user.balance -= amount;
                    $('#balance').text(user.balance.toFixed(2));
                },
                error: function() {
                    alert('Transfer başarısız!');
                }
            });
        });

        $('#logout').click(function() {
            sessionStorage.clear();
            window.location.href = '/login';
        });
    });
    </script>

</body>
</html>
