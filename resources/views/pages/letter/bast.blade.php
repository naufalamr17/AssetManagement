<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="generate-letter"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="GENERATE LETTER"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="p-6">
                            <form action="{{ route('bast.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="letter_id" value="{{ $letter->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Nama Pihak Pertama</label>
                                            <input list="employeeList" class="form-control border p-2" id="nama" name="nama" required>
                                            <datalist id="employeeList">
                                                @foreach($results as $employee)
                                                <option value="{{ $employee->nama }}" data-nik="{{ $employee->nik }}" data-jabatan="{{ $employee->job_position }}" data-dept="{{ $employee->organization }}">
                                                    {{ $employee->nama }} ({{ $employee->nik }})
                                                </option>
                                                @endforeach
                                            </datalist>
                                        </div>

                                        <div class="form-group">
                                            <label for="nik">NIK Pihak Pertama</label>
                                            <input type="text" class="form-control border p-2" id="nik" name="nik" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="jabatan">Jabatan Pihak Pertama</label>
                                            <input type="text" class="form-control border p-2" id="jabatan" name="jabatan" readonly>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="nama_2">Nama Pihak Kedua</label>
                                            <input list="employeeList" class="form-control border p-2" id="nama_2" name="nama_2" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="nik_2">NIK Pihak Kedua</label>
                                            <input type="text" class="form-control border p-2" id="nik_2" name="nik_2" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="jabatan_2">Jabatan Pihak Kedua</label>
                                            <input type="text" class="form-control border p-2" id="jabatan_2" name="jabatan_2" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="place">Tempat</label>
                                            <input type="text" class="form-control border p-2" id="place" name="place" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" class="form-control border p-2" id="tanggal" name="tanggal" value="{{ $letter->tanggal }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="barang">Barang</label>
                                            <input type="text" class="form-control border p-2" id="barang" name="barang" required>
                                        </div>

                                        @if(Route::currentRouteName() !== 'form-bast-radio')
                                        <div class="form-group">
                                            <label for="kodeprod">Kode Produk</label>
                                            <input type="text" class="form-control border p-2" id="kodeprod" name="kodeprod" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="qty">Kuantitas</label>
                                            <input type="number" class="form-control border p-2" id="qty" name="qty" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="satuan">Satuan</label>
                                            <input type="text" class="form-control border p-2" id="satuan" name="satuan" required>
                                        </div>
                                        @endif

                                        @if(Route::currentRouteName() !== 'form-bast-general')
                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi</label>
                                            <textarea class="form-control border p-2" id="deskripsi" name="deskripsi" required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="alasan">Alasan</label>
                                            <textarea class="form-control border p-2" id="alasan" name="alasan" required></textarea>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </form>
                        </div>

                        <script>
                            document.getElementById('nama').addEventListener('input', function() {
                                var selectedOption = document.querySelector('#employeeList option[value="' + this.value + '"]');
                                if (selectedOption) {
                                    document.getElementById('nik').value = selectedOption.getAttribute('data-nik');
                                    document.getElementById('jabatan').value = selectedOption.getAttribute('data-jabatan');
                                    document.getElementById('dept').value = selectedOption.getAttribute('data-dept');
                                } else {
                                    document.getElementById('nik').value = '';
                                    document.getElementById('jabatan').value = '';
                                    document.getElementById('dept').value = '';
                                }
                            });

                            document.getElementById('nama_2').addEventListener('input', function() {
                                var selectedOption = document.querySelector('#employeeList option[value="' + this.value + '"]');
                                if (selectedOption) {
                                    document.getElementById('nik_2').value = selectedOption.getAttribute('data-nik');
                                    document.getElementById('jabatan_2').value = selectedOption.getAttribute('data-jabatan');
                                    document.getElementById('dept_2').value = selectedOption.getAttribute('data-dept');
                                } else {
                                    document.getElementById('nik_2').value = '';
                                    document.getElementById('jabatan_2').value = '';
                                    document.getElementById('dept_2').value = '';
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>