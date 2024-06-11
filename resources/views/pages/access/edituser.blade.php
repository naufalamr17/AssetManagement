<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="user-management"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Edit User"></x-navbars.navs.auth>
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
                            <form method="POST" action="{{ route('update_user', $user->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" class="form-control border p-2" type="text" name="name" value="{{ $user->name }}" required autofocus>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" class="form-control border p-2" type="email" name="email" value="{{ $user->email }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="location">Location</label>
                                            <select id="location" class="form-control border p-2" name="location" required>
                                                <option value="" selected disabled>Select Location</option>
                                                <option value="Head Office" {{ $user->location == 'Head Office' ? 'selected' : '' }}>Head Office</option>
                                                <option value="Office Kendari" {{ $user->location == 'Office Kendari' ? 'selected' : '' }}>Office Kendari</option>
                                                <option value="Site Molore" {{ $user->location == 'Site Molore' ? 'selected' : '' }}>Site Molore</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" class="form-control border p-2" name="status" required>
                                                <option value="" disabled>Select Status</option>
                                                <option value="Administrator" {{ $user->status == 'Administrator' ? 'selected' : '' }}>Administrator</option>
                                                <option value="Admin" {{ $user->status == 'Admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="Modified" {{ $user->status == 'Modified' ? 'selected' : '' }}>Modified</option>
                                                <option value="Viewers" {{ $user->status == 'Viewers' ? 'selected' : '' }}>Viewers</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input id="password" class="form-control border p-2" type="password" name="password">
                                            <small>Leave blank to keep current password</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div>
                                            <div class="form-group d-flex justify-content-between align-items-center">
                                                <label for="access">Access</label>
                                                <button type="button" id="add-access" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div id="access-container" class="space-y-4">
                                                @foreach($user->accesses as $access)
                                                <?php
                                                // dd($user->accesses);
                                                $accessName = $access['access'];
                                                // dump($accessName);
                                                ?>
                                                <div class="d-flex align-items-center">
                                                    <select name="access[]" class="form-control border p-2" style="margin-right: 10px;" required>
                                                        <option value="" disabled>Select Access</option>
                                                        <option value="Asset Management" {{ $accessName == 'Asset Management' ? 'selected' : '' }}>Asset Management</option>
                                                        <option value="IT Asset Management" {{ $accessName == 'IT Asset Management' ? 'selected' : '' }}>IT Asset Management</option>
                                                        <option value="ATK Management" {{ $accessName == 'ATK Management' ? 'selected' : '' }}>ATK Management</option>
                                                    </select>
                                                    <button type="button" class="btn btn-danger remove-access mt-3">X</button>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-block">Update</button>
                                    <a href="javascript:history.go(-1);" class="btn btn-danger remove-access">Cancel</a>
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
        // Fungsi untuk menambahkan input baru
        document.getElementById("add-access").addEventListener("click", function() {
            var accessContainer = document.getElementById("access-container");
            var newAccessDiv = document.createElement("div");
            newAccessDiv.classList.add("d-flex", "align-items-center");
            newAccessDiv.innerHTML = `
        <select name="access[]" class="form-control border p-2" style="margin-right: 10px;" required>
            <option value="" disabled selected>Select Access</option>
            <option value="Asset Management">Asset Management</option>
            <option value="IT Asset Management">IT Asset Management</option>
            <option value="ATK Management">ATK Management</option>
        </select>
        <button type="button" class="btn btn-danger remove-access mt-3">X</button>
    `;
            accessContainer.appendChild(newAccessDiv);
        });

        // Fungsi untuk menghapus input
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-access")) {
                event.target.parentElement.remove();
            }
        });
    </script>
</x-layout>