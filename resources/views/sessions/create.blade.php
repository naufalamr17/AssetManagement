<x-layout bodyClass="bg-gray-200">

    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->
                <!-- <x-navbars.navs.guest signin='login' signup='register'></x-navbars.navs.guest> -->
                <!-- End Navbar -->
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <div class="page-header align-items-start min-vh-100" style="background-image: url('{{ asset('img/exa.jpg') }}');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container mt-5">
                <div class="row signin-margin">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="border-radius-lg py-3 pe-1">
                                    <div class="text-center">
                                        <img src="{{ asset('img/mlpLogo.jpg') }}" alt="Application Logo" class="img-fluid w-20 h-20 mb-2" />
                                    </div>
                                    <div class="row mt-3">
                                        <h4 class='text-center' style="color: rgb(8, 47, 73);">
                                            <span class="font-weight-normal">Log in to your account</span>
                                            <br>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form role="form" method="POST" action="{{ route('login') }}" class="text-start">
                                    @csrf
                                    @if (Session::has('status'))
                                    <div class="alert alert-success alert-dismissible text-white" role="alert">
                                        <span class="text-sm">{{ Session::get('status') }}</span>
                                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif
                                    <div style="margin-top: 1rem; position: relative;">
                                        <label style="position: absolute; top: -0.5rem; left: 0.75rem; background: white; padding: 0 0.25rem; font-size: 0.6 rem; color: #6b7280;">Email</label>
                                        <input type="email" name="email" style="display: block; width: 100%; padding: 0.5rem 0.75rem; border: 1px solid rgb(8, 47, 73); border-radius: 0.375rem; font-size: 0.875rem; line-height: 1.5rem; color: #374151;" autofocus>
                                    </div>
                                    @error('email')
                                    <p class='text-danger inputerror'>{{ $message }} </p>
                                    @enderror
                                    <div style="margin-top: 1rem; position: relative;">
                                        <label style="position: absolute; top: -0.5rem; left: 0.75rem; background: white; padding: 0 0.25rem; font-size: 0.6 rem; color: #6b7280;">Password</label>
                                        <input type="password" name="password" style="display: block; width: 100%; padding: 0.5rem 0.75rem; border: 1px solid rgb(8, 47, 73); border-radius: 0.375rem; font-size: 0.875rem; line-height: 1.5rem; color: #374151;">
                                    </div>
                                    @error('password')
                                    <p class='text-danger inputerror'>{{ $message }} </p>
                                    @enderror
                                    <!-- <div class="form-check form-switch d-flex align-items-center my-3">
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label mb-0 ms-2" for="rememberMe">Remember
                                                me</label>
                                        </div> -->
                                    <div class="text-center">
                                        <button type="submit" class="btn w-100 my-4 mb-2" style="background-color: rgb(8, 47, 73); color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer;">Log In</button>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm mt-3 mb-2">Or continue with</p>
                                        <a href="{{ route('auth.azure') }}" class="btn w-100 mb-2" style="background-color: #2f2f2f; color: white; border: 1px solid #2f2f2f; padding: 12px 20px; border-radius: 5px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 21 21" style="margin-right: 10px;">
                                                <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
                                                <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
                                                <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
                                                <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
                                            </svg>
                                            Microsoft
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <x-footers.guest></x-footers.guest> -->
        </div>
    </main>
    @push('js')
    <script src="{{ asset('assets') }}/js/jquery.min.js"></script>
    <script>
        $(function() {

            var text_val = $(".input-group input").val();
            if (text_val === "") {
                $(".input-group").removeClass('is-filled');
            } else {
                $(".input-group").addClass('is-filled');
            }
        });
    </script>
    @endpush
</x-layout>