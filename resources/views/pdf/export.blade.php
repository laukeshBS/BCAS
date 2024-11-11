<!-- resources/views/pdf/export.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Audit Data</h2>
    <table>
        <thead>
            <tr>
                <th>Action By</th>
                <th>Old Data / Request</th>
                <th>New Data / Response</th>
                <th>IP</th>
                <th>Date</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ @$row->user->name }}</td>
                    <td>{{ $row->old_data }}</td>
                    <td>{{ $row->new_data }}</td>
                    <td>{{ $row->ip_address }}</td>
                    <td>{{ $row->action_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
