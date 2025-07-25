@extends('layouts.dashboard')

@section('content')
<div class="page-content container-xxl">
    <div class="row profile-body">

        <div class="col-12">
            <div class="card rounded">
                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-end mb-2">
                        <div class="dropdown">
                            <a type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" data-lucide="more-horizontal"
                                    class="lucide lucide-more-horizontal icon-lg text-secondary pb-3px">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;" id="editProfileBtn"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" data-lucide="edit-2"
                                        class="lucide lucide-edit-2 icon-sm me-2">
                                        <path
                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z">
                                        </path>
                                    </svg> <span class="">Edit</span></a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center flex-column">
                        <img class="w-150px rounded-circle profile-image" src="" alt="profile">
                        <span class="h4 mt-3 profile-name"></span>
                    </div>

                    <div class="mt-3">
                        <label class="fs-11px fw-bolder mb-0 text-uppercase">About:</label>
                        <p class="text-secondary profile-about"></p>
                    </div>

                    <div class="mt-3">
                        <label class="fs-11px fw-bolder mb-0 text-uppercase">Joined:</label>
                        <p class="text-secondary profile-joined"></p>
                    </div>

                    <div class="mt-3">
                        <label class="fs-11px fw-bolder mb-0 text-uppercase">Username:</label>
                        <p class="text-secondary profile-username"></p>
                    </div>

                    <div class="mt-3">
                        <label class="fs-11px fw-bolder mb-0 text-uppercase">Email:</label>
                        <p class="text-secondary profile-email"></p>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="editTab" role="tablist">
                        @foreach (['Profile', 'Name', 'Username', 'Email', 'Password', 'About'] as $key => $tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $key === 0 ? 'active' : '' }}" id="{{ strtolower($tab) }}-tab"
                                data-bs-toggle="tab" data-bs-target="#{{ strtolower($tab) }}" type="button" role="tab">
                                {{ $tab }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="mb-3">
                                <label for="profileImage" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profileImage">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="name" role="tabpanel">
                            <div class="mb-3">
                                <label for="firstNameInput" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstNameInput">
                            </div>

                            <div class="mb-3">
                                <label for="lastNameInput" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastNameInput">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="username" role="tabpanel">
                            <div class="mb-3">
                                <label for="usernameInput" class="form-label">Username</label>
                                <input type="text" class="form-control" id="usernameInput">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailInput">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <div class="mb-3">
                                <label for="currentPasswordInput" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPasswordInput">
                            </div>
                            <div class="mb-3">
                                <label for="newPasswordInput" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPasswordInput">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPasswordInput" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPasswordInput">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="about" role="tabpanel">
                            <div class="mb-3">
                                <label for="aboutInput" class="form-label">About</label>
                                <textarea class="form-control" id="aboutInput" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProfileBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@push('scripts')
<script>
    $('#editProfileBtn').on('click', function() {
        $('#editProfileModal').modal('show');
        $('.form-control').val('');
    });

    $('#saveProfileBtn').on('click', function() {
        let activeTab = $('#editTab .nav-link.active').attr('id').replace('-tab', '');
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('tab', activeTab);

        switch (activeTab) {
            case 'profile':
                const profileFile = $('#profileImage')[0].files[0];
                if (profileFile) formData.append('profile', profileFile);
                break;

            case 'name':
                formData.append('first_name', $('#firstNameInput').val());
                formData.append('last_name', $('#lastNameInput').val());
                break;

            case 'username':
                formData.append('username', $('#usernameInput').val());
                break;

            case 'email':
                formData.append('email', $('#emailInput').val());
                break;

            case 'password':
                formData.append('current_password', $('#currentPasswordInput').val());
                formData.append('new_password', $('#newPasswordInput').val());
                formData.append('new_password_confirmation', $('#confirmPasswordInput').val());
                break;

            case 'about':
                formData.append('about', $('#aboutInput').val());
                break;
        }

        $.ajax({
            url: '{{ route("profile.update") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editProfileModal').modal('hide');
                getProfileDetails(CURRENT_USER_ID);
                $('.form-control').val('');
                toast('success', response.message); // ✅ Show success message
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // ✅ Validation errors
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, messages) {
                            messages.forEach(function(msg) {
                                toast('error', msg);
                            });
                        });
                    } else if (xhr.responseJSON.error) {
                        toast('error', xhr.responseJSON.error); // e.g. password mismatch
                    }
                } else {
                    toast('error', 'Something went wrong.'); // fallback
                }
            }
        });
    });
</script>
@endpush