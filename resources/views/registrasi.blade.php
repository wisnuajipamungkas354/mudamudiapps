<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Muda-Mudi</title>
    <!-- Include the Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-xl mx-auto py-12">
        <div class="bg-white p-8 rounded shadow-md">
            <img src="{{ asset('img/logo.png') }}" alt="" class="w-14 h-14 m-auto">
            <h2 class="text-2xl font-semibold text-center mb-6 mt-3">Form Registrasi Database</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="daerah" class="block text-sm font-medium text-gray-700">Daerah</label>
                        <select id="select-daerah" name="daerah_id"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            required>

                            <option value="">Pilih Daerah</option>
                            <!-- Menambahkan opsi daerah sesuai kebutuhan -->
                            @foreach ($daerah as $d)
                                <option value="{{ $d->id }}">{{ $d->nm_daerah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="desa" class="block text-sm font-medium text-gray-700">Desa</label>
                        <select id="select-desa" name="desa_id"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Desa</option>
                        </select>
                    </div>
                    <div>
                        <label for="kelompok" class="block text-sm font-medium text-gray-700">Kelompok</label>
                        <select id="select-kelompok" name="kelompok_id"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Kelompok</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama"
                            class="col mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>
                <div>
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700">Jenis Kelamin</legend>
                        <div class="mt-1">
                            <label for="jk_laki" class="inline-flex items-center">
                                <input type="radio" id="jk_laki" name="jk" value="L"
                                    class="form-radio text-blue-500" checked>
                                <span class="ml-2">Laki-laki</span>
                            </label>
                            <label for="jk_perempuan" class="inline-flex items-center ml-6">
                                <input type="radio" id="jk_perempuan" name="jk" value="P"
                                    class="form-radio text-blue-500">
                                <span class="ml-2">Perempuan</span>
                            </label>
                        </div>
                    </fieldset>
                </div>
                <div>
                    <label for="kota_lahir" class="block text-sm font-medium text-gray-700">Kota Lahir</label>
                    <input type="text" id="kota_lahir" name="kota_lahir"
                        class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Masukkan Kota Lahir" required>
                </div>
                <div>
                    <label for="tgl_lahir" class="block text-sm font-medium text-gray-700">Tanggal
                        Lahir</label>
                    <input type="date" id="tgl_lahir" name="tgl_lahir"
                        class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                        max="{{ Carbon\Carbon::now()->endOfYear()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700">Apakah kamu seorang Mubaligh (MT/MS) ?</legend>
                        <div class="mt-1">
                            <label for="mubaligh_yes" class="inline-flex items-center">
                                <input type="radio" id="mubaligh_yes" name="mubaligh" value="Ya"
                                    class="form-radio text-blue-500">
                                <span class="ml-2">Ya</span>
                            </label>
                            <label for="mubaligh_no" class="inline-flex items-center ml-6">
                                <input type="radio" id="mubaligh_no" name="mubaligh" value="Bukan"
                                    class="form-radio text-blue-500" checked>
                                <span class="ml-2">Bukan</span>
                            </label>
                        </div>
                    </fieldset>
                </div>
                <div>
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700">Status</legend>
                        <select id="select-status" name="status"
                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                            size="5" required>
                            @foreach ($status as $st)
                                <option value="{{ $st->nm_status }}">{{ $st->nm_status }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                </div>
                <div>
                    <label for="detail_status" class="block text-sm font-medium text-gray-700">Detail
                        Status</label>
                    <textarea id="detail_status" name="detail_status" rows="3"
                        class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Diisi Apa Aja"></textarea>
                </div>
                <div>
                    <label for="siap_nikah" class="block text-sm font-medium text-gray-700">Siap Nikah</label>
                    <div class="mt-1">
                        <label for="siap_nikah_yes" class="inline-flex items-center">
                            <input type="radio" id="siap_nikah_yes" name="siap_nikah" value="Siap"
                                class="form-radio text-blue-500">
                            <span class="ml-2">Siap</span>
                        </label>
                        <label for="siap_nikah_no" class="inline-flex items-center ml-6">
                            <input type="radio" id="siap_nikah_no" name="siap_nikah" value="Belum"
                                class="form-radio text-blue-500" checked>
                            <span class="ml-2">Belum</span>
                        </label>
                    </div>
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

            $('#select-daerah').on('change', function() {
                let daerahId = $(this).val();
                $.getJSON('/registrasi/getDesa/' + daerahId, function(data) {
                    $('#select-desa option').remove();
                    $('#select-desa').append('<option value="">Pilih Desa</option>');
                    let menu = data.data;
                    $.each(menu, function(i, data) {
                        $('#select-desa').append('<option value="' + data.id + '">' +
                            data.nm_desa + '</option>');
                    });
                });
            })
            $('#select-desa').on('change', function() {
                let desaId = $(this).val();
                $.getJSON('/registrasi/getKelompok/' + desaId, function(data) {
                    $('#select-kelompok option').remove();
                    $('#select-kelompok').append('<option value="">Pilih Kelompok</option>');
                    let menu = data.data;
                    $.each(menu, function(i, data) {
                        $('#select-kelompok').append('<option value="' + data.id + '">' +
                            data.nm_kelompok + '</option>');
                    });
                });
            })

            $('#select-status').on('change', function() {
                if ($(this).val() == 'Pelajar SMP') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Kelas 7'
                    })
                } else if ($(this).val() == 'Pelajar SMA') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan IPA'
                    })
                } else if ($(this).val() == 'Pelajar SMK') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Rekayasa Perangkat Lunak (RPL)'
                    })
                } else if ($(this).val() == 'Mahasiswa D3') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Sistem Informasi'
                    })
                } else if ($(this).val() == 'Mahasiswa S1/D4') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Teknik Sipil'
                    })
                } else if ($(this).val() == 'Mahasiswa S2') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Manajemen Sistem Informasi'
                    })
                } else if ($(this).val() == 'Mahasiswa S3') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Filsafat'
                    })
                } else if ($(this).val() == 'Pencari Kerja SMP') {
                    $('#detail_status').attr({
                        placeholder: 'Diisi Keahlian yang dimiliki'
                    })
                } else if ($(this).val() == 'Pencari Kerja SMA/K') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan IPA/Teknik Mesin'
                    })
                } else if ($(this).val() == 'Pencari Kerja D3') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Sistem Informasi'
                    })
                } else if ($(this).val() == 'Pencari Kerja S1/D4') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Teknik Informatika (S.Kom)'
                    })
                } else if ($(this).val() == 'Pencari Kerja S2') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Teknik Informatika (M.Kom)'
                    })
                } else if ($(this).val() == 'Karyawan/Pegawai') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Operator Produksi/Pegawai Toko'
                    })
                } else if ($(this).val() == 'Tenaga Sabilillah (SB)') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Tugas MT/SB Rawagabus'
                    })
                } else if ($(this).val() == 'Kuliah & Kerja') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Jurusan Manajemen, Operator Produksi'
                    })
                } else if ($(this).val() == 'Wirausaha/Freelance') {
                    $('#detail_status').attr({
                        placeholder: 'Contoh : Bakpao Barokah 354/Designer'
                    })
                }
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
                            url: '/registrasi',
                            type: 'POST',
                            data: {
                                daerah_id: $('#select-daerah').val(),
                                desa_id: $('#select-desa').val(),
                                kelompok_id: $('#select-kelompok').val(),
                                nama: $('#nama').val(),
                                jk: $('input[name="jk"]:checked').val(),
                                kota_lahir: $('#kota_lahir').val(),
                                tgl_lahir: $('#tgl_lahir').val(),
                                mubaligh: $('input[name="mubaligh"]:checked')
                                    .val(),
                                status: $('#select-status').val(),
                                detail_status: $('#detail_status').val(),
                                siap_nikah: $(
                                        'input[name="siap_nikah"]:checked')
                                    .val()
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
