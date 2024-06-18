<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="INPUT ASSET"></x-navbars.navs.auth>
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
                            <form method="POST" action="{{ route('store_inventory') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="old_asset_code">Kode Asset Lama</label>
                                            <input id="old_asset_code" class="form-control border p-2" type="text" name="old_asset_code" value="{{ old('old_asset_code') }}" autofocus>
                                            @if ($errors->has('old_asset_code'))
                                            <div class="text-danger mt-2">{{ $errors->first('old_asset_code') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="location">Location</label>
                                            <select id="location" class="form-control border p-2" name="location" required>
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
                                            <select id="asset_category" class="form-control border p-2" name="asset_category" required>
                                                <option value="" selected disabled>Select Category</option>
                                                <option value="Kendaraan">01 - Kendaraan</option>
                                                <option value="Peralatan">02 - Peralatan</option>
                                                <option value="Bangunan">03 - Bangunan</option>
                                                <option value="Mesin">04 - Mesin</option>
                                                <option value="Alat Berat">05 - Alat Berat</option>
                                                <option value="Alat Lab & Preparasi">06 - Alat Lab & Preparasi</option>
                                            </select>
                                            @if ($errors->has('asset_category'))
                                            <div class="text-danger mt-2">{{ $errors->first('asset_category') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="asset_position_dept">Asset Position</label>
                                            <input id="asset_position_dept" class="form-control border p-2" type="text" name="asset_position_dept" list="asset_position_list" required>
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
                                            <input list="asset_types" class="form-control border p-2" id="asset_type" name="asset_type" required>
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
                                            <textarea id="description" class="form-control border p-2" name="description" required>{{ old('description') }}</textarea>
                                            @if ($errors->has('description'))
                                            <div class="text-danger mt-2">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="serial_number">Serial Number</label>
                                            <input id="serial_number" class="form-control border p-2" type="text" name="serial_number" value="{{ old('serial_number') }}">
                                            @if ($errors->has('serial_number'))
                                            <div class="text-danger mt-2">{{ $errors->first('serial_number') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acquisition_date">Tanggal Perolehan</label>
                                            <input type="date" class="form-control border p-2" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date') }}" required>
                                            @if ($errors->has('acquisition_date'))
                                            <div class="text-danger mt-2">{{ $errors->first('acquisition_date') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="useful_life">Umur ekonomis (Tahun)</label>
                                            <input type="number" class="form-control border p-2" id="useful_life" name="useful_life" value="{{ old('useful_life') }}" readonly>
                                            @if ($errors->has('useful_life'))
                                            <div class="text-danger mt-2">{{ $errors->first('useful_life') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="acquisition_value">Nilai Perolehan</label>
                                            <input id="acquisition_value" class="form-control border p-2" type="number" name="acquisition_value" value="{{ old('acquisition_value', 0) }}">
                                            @if ($errors->has('acquisition_value'))
                                            <div class="text-danger mt-2">{{ $errors->first('acquisition_value') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="hand_over_date">Tanggal Serah Terima</label>
                                            <input type="date" class="form-control border p-2" id="hand_over_date" name="hand_over_date" value="{{ old('hand_over_date') }}">
                                            @if ($errors->has('hand_over_date'))
                                            <div class="text-danger mt-2">{{ $errors->first('hand_over_date') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="user">User</label>
                                            <input id="user" class="form-control border p-2" type="text" name="user" value="{{ old('user') }}">
                                            @if ($errors->has('user'))
                                            <div class="text-danger mt-2">{{ $errors->first('user') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="dept">Dept</label>
                                            <input id="dept" class="form-control border p-2" type="text" name="dept" value="{{ old('dept') }}">
                                            @if ($errors->has('dept'))
                                            <div class="text-danger mt-2">{{ $errors->first('dept') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Remarks</label>
                                            <input id="note" class="form-control border p-2" type="text" name="note" value="{{ old('note') }}">
                                            @if ($errors->has('note'))
                                            <div class="text-danger mt-2">{{ $errors->first('note') }}</div>
                                            @endif
                                            <div class="form-check mt-2">
                                                <input id="store_to_database" type="checkbox" class="form-check-input" name="store_to_database" value="true" @if(old('store_to_database')=='true' ) checked @endif>
                                                <label for="store_to_database" class="form-check-label">Add user to history</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-block">Add Asset</button>
                                    <a href="{{ route('inventory') }}" class="btn btn-danger">Cancel</a>
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

    <script>
        document.getElementById('asset_category').addEventListener('change', function() {
            var usefulLifeInput = document.getElementById('useful_life');
            var selectedCategory = this.value;
            var usefulLife;

            switch (selectedCategory) {
                case 'Kendaraan':
                    usefulLife = 8;
                    break;
                case 'Peralatan':
                    usefulLife = 4;
                    break;
                case 'Bangunan':
                    usefulLife = 20;
                    break;
                case 'Mesin':
                    usefulLife = 16;
                    break;
                case 'Alat Berat':
                    usefulLife = 16;
                    break;
                case 'Alat Lab & Preparasi':
                    usefulLife = 8;
                    break;
                default:
                    usefulLife = '';
            }

            usefulLifeInput.value = usefulLife;
        });

        // Trigger change event to set initial value if a category is already selected
        document.getElementById('asset_category').dispatchEvent(new Event('change'));
    </script>
</x-layout>