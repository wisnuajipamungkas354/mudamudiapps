<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengurus</title>
    <!-- Include the Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-xl mx-auto py-12">
        <div class="bg-white p-8 rounded shadow-md">
            <img src="{{ asset('img/logo.png') }}" alt="" class="w-14 h-14 m-auto">
            <h2 class="text-2xl font-semibold text-center mb-6 mt-3">Form Registrasi Pengurus</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                <div>
                        <label for="tingkatan" class="block text-sm font-medium text-gray-700">Tingkatan</label>
                        <select id="select-tingkatan" name="tingkatan"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Tingkatan</option>
                            <option value="Daerah">Daerah</option>
                            <option value="Desa">Desa</option>
                            <option value="Kelompok">Kelompok</option>
                        </select>
                    </div>
                    <div>
                        <label for="dapukan" class="block text-sm font-medium text-gray-700">Dapukan</label>
                        <select id="select-dapukan" name="dapukan"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Dapukan</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama-pengurus" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="nama-pengurus" name="nama-pengurus"
                            class="col mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>
                <div>
                    <label for="no-hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                    <input type="text" id="no-hp" name="no-hp"
                        class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Masukkan Nomor HP" required>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                        id="submitdata">Kirim</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="{{ asset('js/jquery/dist/jquery-3.7.1.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $('#select-tingkatan').on('change', function() {
                let tingkatan = $(this).val();
                $.getJSON('/pengurus-daerah/registrasi-pengurus/getDapukan/' + tingkatan, function(data) {
                    $('#select-dapukan option').remove();
                    $('#select-dapukan').append('<option value="">Pilih Dapukan</option>');
                    let menu = data.data;
                    // console.log(menu);
                    $.each(menu, function(i, data) {
                        $('#select-dapukan').append('<option value="' + data.nama_dapukan + '">' +
                            data.nama_dapukan + '</option>');
                    });
                });
            })

            $("#submitdata").on('click', function() {
                const swalWithBootstrapButtons = Swal.mixin({
                    buttonsStyling: true
                });
                swalWithBootstrapButtons.fire({
                    title: "Apakah Data Sudah Benar?",
                    text: "Data yang dikirimkan tidak dapat diedit ulang!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Kirim!",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/pengurus-daerah/registrasi-pengurus',
                            type: 'POST',
                            data: {
                                tingkatan: $('#select-tingkatan').val(),
                                dapukan: $('#select-dapukan').val(),
                                nama_pengurus: $('#nama-pengurus').val(),
                                no_hp: $('#no-hp').val(),
                            },
                            success: function(response) {
                                if (response.errors) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Oops... ",
                                        text: response.errors,
                                    });
                                } else if (response.success) {
                                    swalWithBootstrapButtons.fire({
                                        title: "Berhasil!",
                                        text: response.success,
                                        icon: "success"
                                    }).then(($result) => location
                                        .reload());
                                }
                            }
                        })
                    } // If Confirmed
                });
            }); // Submit Data
        })
    </script>
</body>

</html>
