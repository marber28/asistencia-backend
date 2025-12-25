<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            color: #374155;
            font-family: 'Bebas Neue', 'Arial Narrow Bold', sans-serif;
            margin: 0;
            padding: 50px 70px;
            background: #d4e4ff;
            background-image: url('{{ public_path("pdf_background.png") }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .title {
            color: #5d6c89;
            text-align: left;
            font-size: 40px;
            margin-bottom: 10px;
        }

        .box {
            margin-bottom: 15px;
            padding: 20px 30px;
            border-radius: 8px;
        }

        .section-title {
            color: #383e48;
            font-size: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .section-text {
            font-size: 20px;
            line-height: 1.5;
            margin-bottom: 0;
            font-style: italic;
        }

        .section-text span {
            border-bottom: 2px dashed #67789a;
        }

        .celeste {
            background: #d9eae6;
        }

        .naranja {
            background: #fde3cc;
        }

        .salmon {
            background: #fdaba0;
        }

        .lila {
            background: #f4cfdf;
        }

        .azul {
            background: #b6d8f2;
        }
    </style>
</head>

<body>

    <h2 class="title">DESARROLLO DE <br>LECCIÓN</h2>

    <div class="box celeste">
        <strong class="section-title">Lección y Versículo Memorizado:</strong>
        <p class="section-text">
            <span>{{ $data->leccion->titulo . ' - ' . $data->versiculo_memorizado }}</span>
        </p>
    </div>

    <div class="box naranja">
        <strong class="section-title">¿Qué me enseñó la lección?</strong>
        <p class="section-text">
            <span>{{ $data->ensenanza }}</span>
        </p>
    </div>

    <div class="box salmon">
        <strong class="section-title">¿Qué motivé en mis niños?</strong>
        <p class="section-text">
            <span>{{ $data->motivacion }}</span>
        </p>
    </div>

    <div class="box lila">
        <strong class="section-title">¿Qué estrategias usé?</strong>
        <p class="section-text">
            <span>{{ $data->estrategias }}</span>
        </p>
    </div>

    <div class="box azul">
        <strong class="section-title">Observaciones:</strong>
        <p class="section-text">
            <span>{{ $data->observaciones }}</span>
        </p>
    </div>

</body>

</html>