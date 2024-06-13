<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="#"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="IMPORT DATA"></x-navbars.navs.auth>
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

                        <div class="d-flex align-items-center mb-4 p-3">
                            <div class="ms-auto my-3">
                                <a class="btn bg-gradient-dark mb-0" href="{{ asset('Template.xlsx') }}" download>
                                    <i class="material-icons text-sm">file_download</i>&nbsp;&nbsp;Download Template
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <form action="{{ route('store_excel') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="m-3">
                                    <label for="file" class="form-label">Choose Excel File</label>
                                    <input class="form-control border p-2 mb-2" type="file" id="file" name="file" required>
                                    <button type="submit" class="btn bg-gradient-primary">Import Data</button>
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
</x-layout>