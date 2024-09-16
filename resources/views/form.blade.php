<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Wilayah</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url('{{ asset('images/form-bg.jpg') }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .card {
            box-shadow: 10px 10px rgba(0, 0, 0, 0.2);
        }

        .container {
            width: 100%;
            max-width: 600px; /* Adjust based on your layout */
            padding: 20px;
        }

        @media (max-width: 576px) {
            .card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    @include('sweetalert::alert')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center">Pilih Wilayah</h5>
                <form action="{{ route('wilayah.submit') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="nama" name="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Lengkap">
                    </div>
                    <div class="form-group mb-3">
                        <label for="kecamatan">Kecamatan:</label>
                        <select id="kecamatan" name="kecamatan_id" class="form-control" required>
                            <option value="">Pilih Kecamatan</option>
                            @foreach ($kecamatan as $kec)
                                <option value="{{ $kec['id'] }}">{{ $kec['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nagari">Nagari:</label>
                            <select id="nagari" name="nagari_id" class="form-control" required>
                                <option value="">Pilih Nagari</option>
                            </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="slug">Kategori</label>
                        <select id="slug" name="slug" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary col-sm-12">Lanjut Pilih Cabup</button>
                </form>
            </div>

            <div class="container mt-5 text-center">
                <small style="color: wheat;">Copyright by <a href="https://github.com/bgZams" style="text-decoration: none;color: wheat;">@bgZams</a></small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        document.getElementById('kecamatan').addEventListener('change', function() {
            var kecamatanId = this.value;

            fetch('/fetch-nagari/' + kecamatanId)
                .then(response => response.json())
                .then(data => {
                    var nagariSelect = document.getElementById('nagari');
                    nagariSelect.innerHTML = '<option value="">Pilih Nagari</option>';
                    data.forEach(function(nagari) {
                        var option = document.createElement('option');
                        option.value = nagari.id;
                        option.text = nagari.name;
                        nagariSelect.appendChild(option);
                    });
                });
        });
    </script>
</body>

</html>
