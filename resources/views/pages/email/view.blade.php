<!DOCTYPE html>
<html>
<head>
    <title>Disposal Notification</title>
</head>
<body>
    <h1>Disposal Request for Asset Code: {{ $details['asset_code'] }}</h1>
    <p>Hii,</p>
    <p>The following asset is waiting for disposal approval:</p>
    <ul>
        <li><strong>Asset Code:</strong> {{ $details['asset_code'] }}</li>
        <li><strong>Disposal Date:</strong> {{ $details['disposal_date'] }}</li>
        <li><strong>Remarks:</strong> {{ $details['remarks'] }}</li>
    </ul>
    <p>Please approve by visiting the following link:</p>
    <p><a href="https://assetmanagement.mlpmining.com/public/dispose_inventory">Approve Disposal Request</a></p>
    <p>Thank You.</p>
</body>
</html>
