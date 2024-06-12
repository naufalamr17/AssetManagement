<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="repair_inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Breakdown Asset"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                        @endif
                        <div class="p-6">
                            <form method="POST" action="{{ route('store_repair') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="asset_code">Kode Asset</label>
                                            <input id="asset_code" class="form-control border p-2" type="text" name="asset_code" value="{{ old('asset_code') }}" autofocus>
                                            @if ($errors->has('asset_code'))
                                            <div class="text-danger mt-2">{{ $errors->first('asset_code') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="location">Location</label>
                                            <select id="location" class="form-control border p-2" name="location" disabled>
                                                <option value="" selected disabled>Select Location</option>
                                                <option value="Head Office">01 - Head Office</option>
                                                <option value="Office Kendari">02 - Office Kendari</option>
                                                <option value="Site Molore">03 - Site Molore</option>
                                            </select>
                                            @if ($errors->has('location'))
                                            <div class="text-danger mt-2">{{ $errors->first('location') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="asset_category">Kategori</label>
                                            <select id="asset_category" class="form-control border p-2" name="asset_category" disabled>
                                                <option value="" selected disabled>Select Category</option>
                                                <option value="Kendaraan">01 - Kendaraan</option>
                                                <option value="Mesin">02 - Mesin</option>
                                                <option value="Alat Berat">03 - Alat Berat</option>
                                                <option value="Alat Lab">04 - Alat Lab</option>
                                                <option value="Alat Preparasi">05 - Alat Preparasi</option>
                                                <option value="Peralatan">06 - Peralatan</option>
                                                <option value="Perlengkapan">07 - Perlengkapan</option>
                                            </select>
                                            @if ($errors->has('asset_category'))
                                            <div class="text-danger mt-2">{{ $errors->first('asset_category') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="asset_position_dept">Asset Position</label>
                                            <input id="asset_position_dept" class="form-control border p-2" type="text" name="asset_position_dept" list="asset_position_list" disabled>
                                            <datalist id="asset_position_list">
                                                <option value="Geology">Geology</option>
                                                <option value="R. HSE">R. HSE</option>
                                                <option value="Klinik">Klinik</option>
                                                <option value="R. Finance">R. Finance</option>
                                                <option value="R. Meeting">R. Meeting</option>
                                                <option value="R. HRGA (SITE)">R. HRGA (SITE)</option>
                                                <option value="R. Logistik">R. Logistik</option>
                                                <option value="R. Produksi">R. Produksi</option>
                                                <option value="R. KTT">R. KTT</option>
                                                <option value="R. Eksternal">R. Eksternal</option>
                                                <option value="R. Shipping">R. Shipping</option>
                                                <option value="R. Maintenance">R. Maintenance</option>
                                                <option value="R. Lab">R. Lab</option>
                                                <option value="R. Preparasi">R. Preparasi</option>
                                                <option value="Pos Security">Pos Security</option>
                                                <option value="Pantry SITE">Pantry SITE</option>
                                                <option value="Gs Maintenance">Gs Maintenance</option>
                                                <option value="Rumah Genset">Rumah Genset</option>
                                                <option value="Room A1">Room A1</option>
                                                <option value="Room A2">Room A2</option>
                                                <option value="Room A3A">Room A3A</option>
                                                <option value="Room A3B">Room A3B</option>
                                                <option value="Room A4">Room A4</option>
                                                <option value="Room A5">Room A5</option>
                                                <option value="Room A6">Room A6</option>
                                                <option value="Room A7">Room A7</option>
                                                <option value="Room A8">Room A8</option>
                                                <option value="Room A9">Room A9</option>
                                                <option value="Room A10">Room A10</option>
                                                <option value="Room B1">Room B1</option>
                                                <option value="Room B2">Room B2</option>
                                                <option value="Room B3A">Room B3A</option>
                                                <option value="Room B3B">Room B3B</option>
                                                <option value="Room B4">Room B4</option>
                                                <option value="Room B5">Room B5</option>
                                                <option value="Room B6">Room B6</option>
                                                <option value="Room B7">Room B7</option>
                                                <option value="Room B8">Room B8</option>
                                                <option value="Room B9">Room B9</option>
                                                <option value="Room B10">Room B10</option>
                                                <option value="User">User</option>
                                                <option value="Mushola">Mushola</option>
                                                <option value="R. Dapur">R. Dapur</option>
                                                <option value="Gudang GA">Gudang GA</option>
                                                <option value="Stone Crusher">Stone Crusher</option>
                                                <option value="Survey">Survey</option>
                                                <option value="Jetty">Jetty</option>
                                                <option value="Nursery">Nursery</option>
                                                <option value="Room VIP 1">Room VIP 1</option>
                                                <option value="Room VIP 2">Room VIP 2</option>
                                                <option value="Room VIP 3A">Room VIP 3A</option>
                                                <option value="Room VIP 3B">Room VIP 3B</option>
                                                <option value="Room VIP 5">Room VIP 5</option>
                                                <option value="Laundry">Laundry</option>
                                                <option value="Gudang Mesin">Gudang Mesin</option>
                                                <option value="LV 01">LV 01</option>
                                                <option value="LV 02">LV 02</option>
                                                <option value="LV 03">LV 03</option>
                                                <option value="LV 05">LV 05</option>
                                                <option value="LV 06">LV 06</option>
                                                <option value="LV 07">LV 07</option>
                                                <option value="LV 08">LV 08</option>
                                                <option value="LV 09">LV 09</option>
                                                <option value="LV 10">LV 10</option>
                                                <option value="LV 11">LV 11</option>
                                                <option value="LV 12">LV 12</option>
                                                <option value="LV 16">LV 16</option>
                                                <option value="LV 15">LV 15</option>
                                                <option value="ELF">ELF</option>
                                                <option value="Dump Truck">Dump Truck</option>
                                                <option value="R. PAK WIN">R. PAK WIN</option>
                                                <option value="R. Pantry">R. Pantry</option>
                                                <option value="R. Meeting Kecil">R. Meeting Kecil</option>
                                                <option value="R. Meeting Besar">R. Meeting Besar</option>
                                                <option value="R. Staff">R. Staff</option>
                                                <option value="R. Manager 4">R. Manager 4</option>
                                                <option value="R. Deputy GM Support">R. Deputy GM Support</option>
                                                <option value="R. Manager 1">R. Manager 1</option>
                                                <option value="R. Manager 2">R. Manager 2</option>
                                                <option value="R. Manager 3">R. Manager 3</option>
                                                <option value="R. Direksi 1">R. Direksi 1</option>
                                                <option value="R. Direksi 2">R. Direksi 2</option>
                                                <option value="R. Direksi 3">R. Direksi 3</option>
                                                <option value="R. Lounge">R. Lounge</option>
                                                <option value="R. Legal">R. Legal</option>
                                                <option value="R. Receptionist">R. Receptionist</option>
                                                <option value="Basement">Basement</option>
                                                <option value="R. HRGA-IT">R. HRGA-IT</option>
                                                <option value="R. Staff 18B/L">R. Staff 18B/L</option>
                                                <option value="R. GM Operation">R. GM Operation</option>
                                                <option value="R. Deputy GM Operation">R. Deputy GM Operation</option>
                                                <option value="R. Manager Engineer">R. Manager Engineer</option>
                                                <option value="R. Manager 18B/L">R. Manager 18B/L</option>
                                                <option value="R. Smooking Room">R. Smooking Room</option>
                                                <option value="R. CEO">R. CEO</option>
                                            </datalist>
                                            @if ($errors->has('asset_position_dept'))
                                            <div class="text-danger mt-2">{{ $errors->first('asset_position_dept') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="asset_type">Jenis</label>
                                            <input list="asset_types" class="form-control border p-2" id="asset_type" name="asset_type" disabled>
                                            <datalist id="asset_types">
                                                <option value="LV">LV</option>
                                                <option value="Mobil Tangki">Mobil Tangki</option>
                                                <option value="Dump Truck">Dump Truck</option>
                                                <option value="Elf">Elf</option>
                                                <option value="Mobil Operasional">Mobil Operasional</option>
                                                <option value="Motor Operasional">Motor Operasional</option>
                                                <option value="Speed Boat">Speed Boat</option>
                                                <option value="Genset">Genset</option>
                                                <option value="Compressor">Compressor</option>
                                                <option value="Crusher Big">Crusher Big</option>
                                                <option value="Excavator">Excavator</option>
                                                <option value="Ramp Door">Ramp Door</option>
                                                <option value="Oven">Oven</option>
                                                <option value="Jaw Crusher">Jaw Crusher</option>
                                                <option value="Pul Vulizer">Pul Vulizer</option>
                                                <option value="Mixer Type C">Mixer Type C</option>
                                                <option value="Top Grinder">Top Grinder</option>
                                                <option value="Roll Crusher">Roll Crusher</option>
                                                <option value="Sieve Shaker Mesin">Sieve Shaker Mesin</option>
                                                <option value="Epsilon">Epsilon</option>
                                                <option value="Mesin Press">Mesin Press</option>
                                                <option value="Laptop/PC">Laptop/PC</option>
                                                <option value="Printer/Scanner">Printer/Scanner</option>
                                                <option value="UPS">UPS</option>
                                                <option value="GPS">GPS</option>
                                                <option value="Alat Komunikasi">Alat Komunikasi</option>
                                                <option value="Perangkat Jaringan">Perangkat Jaringan</option>
                                                <option value="Brankas">Brankas</option>
                                                <option value="Alat Kesehatan">Alat Kesehatan</option>
                                                <option value="Meja">Meja</option>
                                                <option value="Kursi">Kursi</option>
                                                <option value="Lemari">Lemari</option>
                                                <option value="Elektronik">Elektronik</option>
                                                <option value="Tempat Tidur">Tempat Tidur</option>
                                                <option value="Lain - Lain">Lain - Lain</option>
                                            </datalist>
                                            @if ($errors->has('asset_type'))
                                            <div class="text-danger mt-2">{{ $errors->first('asset_type') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea id="description" class="form-control border p-2" name="description" disabled>{{ old('description') }}</textarea>
                                            @if ($errors->has('description'))
                                            <div class="text-danger mt-2">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="serial_number">Serial Number</label>
                                            <input id="serial_number" class="form-control border p-2" type="text" name="serial_number" value="{{ old('serial_number') }}" disabled>
                                            @if ($errors->has('serial_number'))
                                            <div class="text-danger mt-2">{{ $errors->first('serial_number') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acquisition_date">Tanggal Perolehan</label>
                                            <input type="date" class="form-control border p-2" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date') }}" disabled>
                                            @if ($errors->has('acquisition_date'))
                                            <div class="text-danger mt-2">{{ $errors->first('acquisition_date') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="useful_life">Umur ekonomis (Tahun)</label>
                                            <input type="number" class="form-control border p-2" id="useful_life" name="useful_life" value="{{ old('useful_life') }}" disabled>
                                            @if ($errors->has('useful_life'))
                                            <div class="text-danger mt-2">{{ $errors->first('useful_life') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="acquisition_value">Nilai Perolehan</label>
                                            <input id="acquisition_value" class="form-control border p-2" type="number" name="acquisition_value" value="{{ old('acquisition_value') }}" disabled>
                                            @if ($errors->has('acquisition_value'))
                                            <div class="text-danger mt-2">{{ $errors->first('acquisition_value') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" class="form-control border p-2" type="number" name="status" value="{{ old('status') }}">
                                                <option value="" selected disabled>Select Status</option>
                                                <option value="Good">Good</option>
                                                <option value="Breakdown">Breakdown</option>
                                                <option value="Repair">Repair</option>
                                            </select>
                                        </div>

                                        <div id="additional-fields-repair" style="display:none;">
                                            <div class="form-group">
                                                <label for="tanggal_kerusakan_repair">Tanggal Kerusakan</label>
                                                <input id="tanggal_kerusakan_repair" class="form-control border p-2" type="date" name="tanggal_kerusakan_repair" value="{{ old('tanggal_kerusakan_repair') }}">
                                                <!-- Add the 'required' attribute to make the field mandatory -->
                                                @if ($errors->has('tanggal_kerusakan_repair'))
                                                <div class="text-danger mt-2">{{ $errors->first('tanggal_kerusakan_repair') }}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="tanggal_pengembalian_repair">Tanggal Pengembalian</label>
                                                <input id="tanggal_pengembalian_repair" class="form-control border p-2" type="date" name="tanggal_pengembalian_repair" value="{{ old('tanggal_pengembalian_repair') }}">
                                                <!-- No need for 'required' attribute if this field is optional -->
                                                @if ($errors->has('tanggal_pengembalian_repair'))
                                                <div class="text-danger mt-2">{{ $errors->first('tanggal_pengembalian_repair') }}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="remarks_repair">Remarks</label>
                                                <textarea id="remarks_repair" class="form-control border p-2" name="remarks_repair">{{ old('remarks_repair') }}</textarea>
                                                <!-- Add the 'required' attribute to make the field mandatory -->
                                                @if ($errors->has('remarks_repair'))
                                                <div class="text-danger mt-2">{{ $errors->first('remarks_repair') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div id="additional-fields-breakdown" style="display:none;">
                                            <div class="form-group">
                                                <label for="tanggal_kerusakan_breakdown">Tanggal Kerusakan</label>
                                                <input id="tanggal_kerusakan_breakdown" class="form-control border p-2" type="date" name="tanggal_kerusakan_breakdown" value="{{ old('tanggal_kerusakan_breakdown') }}">
                                                @if ($errors->has('tanggal_kerusakan_breakdown'))
                                                <div class="text-danger mt-2">{{ $errors->first('tanggal_kerusakan_breakdown') }}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="remarks_breakdown">Remarks</label>
                                                <textarea id="remarks_breakdown" class="form-control border p-2" name="remarks_breakdown">{{ old('remarks_breakdown') }}</textarea>
                                                @if ($errors->has('remarks_breakdown'))
                                                <div class="text-danger mt-2">{{ $errors->first('remarks_breakdown') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-block">Submit</button>
                                    <a href="{{ route('repair_inventory') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('status').addEventListener('change', function() {
            var additionalFieldsRepair = document.getElementById('additional-fields-repair');
            var additionalFieldsBreakdown = document.getElementById('additional-fields-breakdown');

            // Hide both additional fields by default
            additionalFieldsRepair.style.display = 'none';
            additionalFieldsBreakdown.style.display = 'none';

            // Show the appropriate additional fields based on the selected status
            if (this.value === 'Repair') {
                additionalFieldsRepair.style.display = 'block';
            } else if (this.value === 'Breakdown') {
                additionalFieldsBreakdown.style.display = 'block';
            }
        });

        // Menggunakan jQuery untuk memudahkan pemilihan elemen dan manipulasi DOM
        $(document).ready(function() {
            // Event listener untuk input asset_code
            $('#asset_code').on('input', function() {
                // Ambil nilai yang dimasukkan ke dalam input asset_code
                var assetCode = $(this).val();

                if (assetCode.length >= 13) {
                    // Lakukan permintaan AJAX untuk mendapatkan data inventaris berdasarkan kode aset
                    $.ajax({
                        type: 'GET',
                        url: '/get-inventory-data', // Ganti dengan URL yang benar sesuai dengan rute Anda
                        data: {
                            asset_code: assetCode
                        },
                        success: function(response) {
                            // Isi input lainnya dengan data yang diterima dari permintaan AJAX
                            $('#location').val(response.location);
                            $('#asset_category').val(response.asset_category);
                            $('#asset_position_dept').val(response.asset_position_dept);
                            $('#asset_type').val(response.asset_type);
                            $('#description').val(response.description);
                            $('#serial_number').val(response.serial_number);
                            $('#acquisition_date').val(response.acquisition_date);
                            $('#useful_life').val(response.useful_life);
                            $('#acquisition_value').val(response.acquisition_value);
                            $('#status').val(response.status);
                        },
                        error: function(xhr, status, error) {
                            $('#location').val('');
                            $('#asset_category').val('');
                            $('#asset_position_dept').val('');
                            $('#asset_type').val('');
                            $('#description').val('');
                            $('#serial_number').val('');
                            $('#acquisition_date').val('');
                            $('#useful_life').val('');
                            $('#acquisition_value').val('');
                            $('#status').val('');
                            console.error(xhr.responseText); // Log pesan kesalahan ke konsol
                            // Atau tampilkan pesan kesalahan kepada pengguna
                            alert('Data tidak ditemukan. Silakan coba lagi.');
                        }
                    });
                }
            });
        });
    </script>
</x-layout>