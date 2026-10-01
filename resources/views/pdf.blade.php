<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF</title>
    <link rel="stylesheet" href="{{ public_path("bootstrap.min.css") }}">
</head>

<body>
    <div class="container-fluid">
        <nav class="nav mb-4">
            <h2>Date: {{ $date }}</h2>
            <h2>HPS: {{ $hps }}</h2>
        </nav>
        @foreach ($ramassage as $item)
            @foreach ($item["ramassage"] as $r)
                <div class="d-flex gap-2">
                    <div>
                        <h5>HDR: {{ $r["HDR"] }}</h5>
                    </div>
                    <div>
                        <h5>Trajet: {{ $r["trajet"] }}</h5>
                    </div>
                </div>
            @endforeach
            <table class="table table-bordered text-center align-middle mb-5">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Matricule</th>
                        <th>Nom et prénom</th>
                        <th>Telephone</th>
                        <th>Lieu de ramassage</th>
                        <th>Point de ramassage ADM</th>
                        <th>Point de ramassage décalé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item["emps"] as $emp)
                        <tr style="font-size: 12">
                            <td>{{ $emp["matricule"] }}</td>
                            <td>{{ $emp["nom"] }}</td>
                            <td>{{ $emp["telephone"] }}</td>
                            <td>{{ $emp["lieu_ramassage"] }}</td>
                            <td>{{ $emp["pnt_ramassage_adm"] }}</td>
                            <td>{{ $emp["pnt_ramassage_dec"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endforeach
    </div>
</body>

</html>
