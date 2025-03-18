<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Paneli - Havale Onay</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<h3>Onay Bekleyen Transferler</h3>

<div id="approveResult"></div>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Gönderen ID</th>
        <th>Alıcı ID</th>
        <th>Miktar</th>
        <th>Tarih</th>
        <th>İşlem</th>
    </tr>

    <?php foreach ($transfers as $transfer): ?>
        <tr id="transfer-<?= $transfer['id'] ?>">
            <td><?= $transfer['id'] ?></td>
            <td><?= $transfer['sender_id'] ?></td>
            <td><?= $transfer['receiver_id'] ?></td>
            <td><?= number_format($transfer['amount'], 2) ?> TL</td>
            <td><?= $transfer['created_at'] ?></td>
            <td>
                <button onclick="approveTransfer(<?= $transfer['id'] ?>)">Onayla</button>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<script>
function approveTransfer(id) {
    if (confirm("Bu transferi onaylamak istediğinize emin misiniz?")) {
        $.ajax({
            url: '<?= base_url("/admin/approveTransfer/") ?>' + id,
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    $('#approveResult').html('<p style="color:green;">' + res.success + '</p>');
                    $('#transfer-' + id).remove();
                } else {
                    $('#approveResult').html('<p style="color:red;">' + res.error + '</p>');
                }
            },
            error: function(xhr) {
                $('#approveResult').html('<p style="color:red;">Hata oluştu: ' + xhr.responseJSON.error + '</p>');
            }
        });
    }
}
</script>

</body>
</html>