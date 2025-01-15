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
                            <form action="{{ route('berita-acara.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="letter_id" value="{{ $letter->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
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
                                            <label for="nik">NIK</label>
                                            <input type="text" class="form-control border p-2" id="nik" name="nik" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="dept">Dept</label>
                                            <input type="text" class="form-control border p-2" id="dept" name="dept" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="jabatan">Jabatan</label>
                                            <input type="text" class="form-control border p-2" id="jabatan" name="jabatan" readonly>
                                        </div>

                                        @if($letter->perihal != 'MUTASI ASSET')
                                        <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <input type="text" class="form-control border p-2" id="alamat" name="alamat" required>
                                        </div>
                                        @endif

                                        @if($letter->perihal == 'MUTASI ASSET')
                                        <div class="form-group">
                                            <label for="nama_2">Nama 2</label>
                                            <input list="employeeList" class="form-control border p-2" id="nama_2" name="nama_2">
                                        </div>

                                        <div class="form-group">
                                            <label for="nik_2">NIK 2</label>
                                            <input type="text" class="form-control border p-2" id="nik_2" name="nik_2" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="dept_2">Dept 2</label>
                                            <input type="text" class="form-control border p-2" id="dept_2" name="dept_2" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="jabatan_2">Jabatan 2</label>
                                            <input type="text" class="form-control border p-2" id="jabatan_2" name="jabatan_2" readonly>
                                        </div>
                                        @endif

                                        @if($letter->perihal != 'MUTASI ASSET')
                                        <div class="form-group">
                                            <label for="kronologi">Kronologi</label>
                                            <textarea class="form-control border p-2" id="kronologi" name="kronologi" required></textarea>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <div id="dynamic-fields">
                                            <div class="dynamic-field">
                                                <div class="form-group">
                                                    <label for="no_asset">No Asset</label>
                                                    <input list="assetList" class="form-control border p-2" name="no_asset[]" required>
                                                    <datalist id="assetList">
                                                        @foreach(App\Models\inventory::all() as $inventory)
                                                        <option value="{{ $inventory->asset_code }}">
                                                            {{ $inventory->asset_code }} ({{ $inventory->description }})
                                                        </option>
                                                        @endforeach
                                                    </datalist>
                                                </div>

                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal</label>
                                                    <input type="date" class="form-control border p-2" name="tanggal[]" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="alasan">Alasan</label>
                                                    <textarea class="form-control border p-2" name="alasan[]" required></textarea>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2 mt-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                                                    <button type="button" class="btn btn-dark btn-sm add-field">Add More</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </form>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                function attachRemoveEvent(button) {
                                    button.addEventListener('click', function() {
                                        button.closest('.dynamic-field').remove();
                                    });
                                }

                                document.addEventListener('click', function(event) {
                                    if (event.target.classList.contains('add-field')) {
                                        var newField = document.querySelector('.dynamic-field').cloneNode(true);
                                        newField.querySelectorAll('input, textarea').forEach(function(input) {
                                            input.value = '';
                                        });
                                        var removeButton = newField.querySelector('.remove-field');
                                        attachRemoveEvent(removeButton);
                                        document.getElementById('dynamic-fields').appendChild(newField);
                                    }

                                    if (event.target.classList.contains('remove-field')) {
                                        event.target.closest('.dynamic-field').remove();
                                    }
                                });

                                // Attach event listeners to existing remove buttons
                                document.querySelectorAll('.remove-field').forEach(function(button) {
                                    attachRemoveEvent(button);
                                });
                            });

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