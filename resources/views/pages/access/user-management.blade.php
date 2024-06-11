<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="user-management"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="User Management"></x-navbars.navs.auth>
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
                        <div class=" me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('add_user') }}"><i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New
                                User</a>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Action
                                            </th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Name</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Email</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Location</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                                Access</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        @php
                                        $userAccesses = $accesses->where('user_id', $user->id);
                                        @endphp
                                        <tr class="text-center text-xs">
                                            <td class="border-b border-gray-200 py-2" rowspan="{{ $userAccesses->count() + 1 }}">
                                                <div class="flex items-center justify-center">
                                                    <a rel="tooltip" class="btn btn-success btn-link btn-sm" href="{{ route('edit_user', ['id' => $user->id]) }}" data-original-title="" title="" style="font-size: 0.875rem; padding: 0.5rem;">
                                                        <i class="material-icons" style="font-size: 16px;">edit</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                    <form action="{{ route('destroy_user', ['id' => $user->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-link btn-sm" data-original-title="" title="" style="font-size: 0.875rem; padding: 0.5rem;">
                                                            <i class="material-icons" style="font-size: 16px;">close</i>
                                                            <div class="ripple-container"></div>
                                                        </button>
                                                    </form>
                                                </div>

                                            </td>
                                            <td class="border-b border-gray-200 py-2" rowspan="{{ $userAccesses->count() + 1 }}">
                                                <div class="flex items-center justify-center">{{ $user->name }}</div>
                                            </td>
                                            <td class="border-b border-gray-200 py-2" rowspan="{{ $userAccesses->count() + 1 }}">
                                                <div class="flex items-center justify-center">{{ $user->email }}</div>
                                            </td>
                                            <td class="border-b border-gray-200 py-2" rowspan="{{ $userAccesses->count() + 1 }}">
                                                <div class="flex items-center justify-center">{{ $user->location }}</div>
                                            </td>
                                            <td class="border-b border-gray-200 py-2" rowspan="{{ $userAccesses->count() + 1 }}">
                                                <div class="flex items-center justify-center">{{ $user->status }}</div>
                                            </td>
                                        </tr>
                                        @foreach($userAccesses as $access)
                                        <tr class="text-center text-xs border-b border-gray-200">
                                            <td class="py-2">{{ $access->access }}</td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>