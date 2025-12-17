<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 50px 30px;
            background: #d4e4ff;
            background-image: url('{{ public_path("pdf_background.png") }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .content {
            padding: 30px 40px;
            border-radius: 8px;
        }

        h1 {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 40px;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: #5c6b88;
            margin: 0 0 10px 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 0 !important;
        }

        .logo {
            float: right;
            height: 60px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background: #b8dafd;
            padding: 20px 6px;
            font-size: 16px;
            color: #5c6b88;
            border: 2px solid #444;
            text-align: center;
        }

        th:nth-last-child(1) {
            background: #9dc8ff;
        }

        td {
            border: 2px solid #444;
            padding: 6px;
            height: 22px;
            font-size: 12px;
            text-align: center;
            color: #212121;
        }

        td.name {
            font-size: 15px;
            text-align: left;
        }

        td.estado {
            font-size: 17px;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <img src="{{ public_path('logo-iebs.png') }}" class="logo">
            <h1>{{ $mesNombre }}</h1>
        </div>

        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th style="min-width:160px">APELLIDOS Y NOMBRES</th>

                    {{-- CABECERAS = 5 SEMANAS --}}
                    @foreach ($diasDelMes as $dia)
                    <th>{{ $dia }}</th>
                    @endforeach

                    <th>T</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($alumnos as $i => $alumno)
                <tr>
                    <td width="30">{{ $i + 1 }}</td>
                    <td class="name">{{ $alumno['nombre'] }}</td>

                    @php $total = 0; @endphp

                    @foreach ($diasDelMes as $dia)
                    @php
                    $estado = $map[$alumno['id']][$dia] ?? '';
                    if ($estado === 'presente') $total++;
                    @endphp
                    <td class="estado" width="25">{{ $estados[$estado] }}</td>
                    @endforeach

                    <td class="estado">{{ $total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>

</html>