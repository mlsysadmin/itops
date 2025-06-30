<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home Utilization Report - MLhuillier</title>
    <style>
        :root {
            --ml-red: #CE2029;
            --ml-yellow: #F5A623;
            --ml-dark: #333333;
        }

        body {
            font-family: Arial, sans-serif;
            color: var(--ml-dark);
            margin: 30px;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--ml-red);
            padding-bottom: 10px;
        }

        header img {
            height: 60px;
            margin-bottom: 10px;
        }

        h1 {
            color: var(--ml-red);
            margin: 5px 0;
        }

        .address {
            font-size: 12px;
            margin: 0;
        }

        .date {
            text-align: right;
            font-size: 12px;
            margin-bottom: 20px;
        }

        h2 {
            color: var(--ml-red);
            text-align: center;
            text-transform: uppercase;
            font-size: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid var(--ml-red);
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: var(--ml-red);
            color: #fff;
            padding: 10px;
            text-align: center;
            text-transform: uppercase;
            border: 1px solid #ccc;
        }

        td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .signatories {
            margin-top: 30px;
            font-size: 13px;
        }

        .signatories strong {
            color: var(--ml-red);
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('images/ml-logo.png') }}" alt="MLhuillier Logo"
            style="width:400px; height:auto; margin-bottom: 20px;">
        <p class="address">B. Benedicto Street, North Reclamation Area, Cebu City, Cebu, Philippines, 6000</p>
    </header>

    <p class="date">Date: {{ \Carbon\Carbon::now()->format('F j, Y') }}</p>

    <h2>Home Utilization Report</h2>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Database Name</th>
                <th>Home Available</th>
                <th>Home Used</th>
                <th>Home Total</th>
                <th>Home %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->database_name }}</td>
                    <td>{{ $row->root_free }}</td>
                    <td>{{ $row->root_used }}</td>
                    <td>{{ $row->root_total }}</td>
                    <td>{{ $row->percentage }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signatories" style="margin-top:30px; width:100%; border:none; font-size:13px;">
        <thead>
            <tr>
                <th style="background:none; color:var(--ml-dark); border:none; text-align:left;">Name</th>
                <th style="background:none; color:var(--ml-dark); border:none; text-align:left;">Position</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Charles Conrad Lim</strong></td>
                <td>IT Operations Manager</td>
            </tr>
            <tr>
                <td><strong>Junryl Furog / Christian Marlou Ytac</strong></td>
                <td>Assistant Data Management Managers</td>
            </tr>
            <tr>
                <td><strong>Aldrin Cuerda</strong></td>
                <td>Database Specialist</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
