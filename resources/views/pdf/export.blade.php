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
                <th>Action Name</th>
                <th>Title</th>
                <th>Username</th>
                <th>Name</th>
                <th>Eamil</th>
                <th>Phone</th>
                <th style="max-width: 300px;">Request</th>
                <th>Audit Date</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ @$row->action_name }}</td>
                    <td>{{ @$row->module_item_title }}</td>
                    <td>{{ @$row->user->username }}</td>
                    <td>{{ @$row->user->name }}</td>
                    <td>{{ @$row->user->email }}</td>
                    <td>{{ @$row->user->phone }}</td>
                    <td>{{ $row->new_data }}</td>
                    <td>{{ $row->action_date }}</td>
                    <td>{{ $row->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
