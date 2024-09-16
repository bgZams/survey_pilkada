<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-image: url('{{ asset('images/bg.jpg') }}');
            background-repeat: no-repeat;
            background-size: cover;
        }
        .image-container {
            position: relative;
            cursor: pointer;
        }
        .image-container img {
            width: 100%;
            height: auto;
            filter: brightness(100%);
            transition: filter 0.3s ease;
        }
        .image-container:hover img {
            filter: brightness(70%);
        }
        .image-container .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        .image-container.loading .loading-overlay {
            opacity: 1;
        }
    </style>
</head>
<body>

    <form id="imageForm" action="{{ route('data.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="nama" value="{{ session('nama') }}">
        <input type="hidden" name="kecamatan_id" value="{{ session('kecamatan_id') }}">
        <input type="hidden" name="nagari_id" value="{{ session('nagari_id') }}">
        <input type="hidden" name="slug" value="{{ session('slug') }}">
        <input type="hidden" name="ip_address" value="{{ request()->ip() }}">

        <div class="container py-3">
            <div class="row g-2">
                <!-- Gambar 1 -->
                <div class="col-6 col-md-3">
                    <div class="bg-light image-container" data-paslon="1">
                        <div class="loading-overlay">Loading...</div>
                        <img src="{{ asset('images/HK.png') }}" class="rounded" alt="HK Image">
                    </div>
                </div>
                <!-- Gambar 2 -->
                <div class="col-6 col-md-3">
                    <div class="bg-light image-container" data-paslon="2">
                        <div class="loading-overlay">Loading...</div>
                        <img src="{{ asset('images/YM.png') }}" class="rounded" alt="YM Image">
                    </div>
                </div>
                <!-- Gambar 3 -->
                <div class="col-6 col-md-3">
                    <div class="bg-light image-container" data-paslon="3">
                        <div class="loading-overlay">Loading...</div>
                        <img src="{{ asset('images/TS.png') }}" class="rounded" alt="TS Image">
                    </div>
                </div>
                <!-- Gambar 4 -->
                <div class="col-6 col-md-3">
                    <div class="bg-light image-container" data-paslon="4">
                        <div class="loading-overlay">Loading...</div>
                        <img src="{{ asset('images/DH.png') }}" class="rounded" alt="DH Image">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.image-container').forEach(function(container) {
            container.addEventListener('click', function() {
                var paslonId = this.getAttribute('data-paslon');
                var form = document.getElementById('imageForm');
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'paslon_id';
                input.value = paslonId;
                form.appendChild(input);

                // Tampilkan loading overlay
                this.classList.add('loading');

                // Debugging: Log paslonId
                console.log('Paslon ID:', paslonId);

                // Submit form
                form.submit();
            });
        });
    </script>
</body>
</html>
