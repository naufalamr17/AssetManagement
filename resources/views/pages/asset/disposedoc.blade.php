<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="dispose_inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="DISPOSE ASSET"></x-navbars.navs.auth>
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
                        <div class="p-2">
                            <form method="POST" action="{{ route('store_disposedoc') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="id" style="display: none;" value="{{ $dispose->id }}">
                                    <label for="disposal_document">Dokumen Disposal</label>
                                    <input type="file" class="form-control border p-2" id="disposal_document" name="disposal_document">
                                    @if ($errors->has('disposal_document'))
                                    <div class="text-danger mt-2">{{ $errors->first('disposal_document') }}</div>
                                    @endif
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
        </div>
        </div>
        <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</x-layout>