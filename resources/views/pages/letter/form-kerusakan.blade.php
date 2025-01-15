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
                            <form action="{{ route('kerusakan.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="letter_id" value="{{ $letter->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input list="employeeList" class="form-control border p-2" id="nama" name="nama" required>
                                            <datalist id="employeeList">
                                                @foreach($results as $employee)
                                                <option value="{{ $employee->nama }}" data-nik="{{ $employee->nik }}" data-jabatan="{{ $employee->job_position }}">
                                                    {{ $employee->nama }} ({{ $employee->nik }})
                                                </option>
                                                @endforeach
                                            </datalist>
                                        </div>

                                        <div class="form-group">
                                            <label for="nik">NIK</label>
                                            <input type="text" class="form-control border p-2" id="nik" name="nik" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="jabatan">Jabatan</label>
                                            <input type="text" class="form-control border p-2" id="jabatan" name="jabatan" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="kode_asset">Kode Asset</label>
                                            <input list="assetList" class="form-control border p-2" id="kode_asset" name="kode_asset" required>
                                            <datalist id="assetList">
                                                @foreach(App\Models\inventory::all() as $inventory)
                                                <option value="{{ $inventory->asset_code }}">
                                                    {{ $inventory->asset_code }} ({{ $inventory->description }})
                                                </option>
                                                @endforeach
                                            </datalist>
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kerusakan">Kerusakan</label>
                                            <textarea class="form-control border p-2" id="kerusakan" name="kerusakan" required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="penyebab">Penyebab</label>
                                            <textarea class="form-control border p-2" id="penyebab" name="penyebab" required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="tindakan">Tindakan</label>
                                            <input type="text" class="form-control border p-2" id="tindakan" name="tindakan" value="{{ $letter->perihal }}" readonly>
                                        </div>
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
                                } else {
                                    document.getElementById('nik').value = '';
                                    document.getElementById('jabatan').value = '';
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