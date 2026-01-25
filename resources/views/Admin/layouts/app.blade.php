<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('admin/img/converted_image.png') }}">
    <title>Apka budget</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('Admin.includes.sidebar')
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('Admin.includes.header')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2025 Apka budget All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('admin.logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    <!-- Page level plugins -->
    {{-- <script src="{{ asset('admin/vendor/chart.js/Chart.mina.js') }}"></script> --}}
    <!-- Page level custom scripts -->
    {{-- <script src="{{ asset('admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('admin/js/demo/chart-pie-demo.js') }}"></script> --}}
    <!-- Page level plugins -->
    <script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/js/demo/datatables-demo.js') }}"></script>
    {{-- <script>
        $(document).ready(function () {
            // ✅ CSRF Token Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            /** ============================
             * ✅ Form Submission (Add Data)
             * ============================ */
            $('#addForm').on('submit', function (e) {
                e.preventDefault();
                $('.error-message, .text-danger').empty();
    
                let formData = new FormData(this);
    
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 1) {
                            alert(response.message);
                            window.location.href = response.route; // ✅ Redirect after success
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                       if (xhr.responseJSON && xhr.responseJSON.message) {
                            $.each(xhr.responseJSON.message, function(field, messages) {
                                $('#' + field + '_error').html(messages.join('<br>'))
                                    .show();
                            });
                        }
                    }
                });
            });
    
           
//              $(document).on("click", ".delete-btn", function () {
//     let rowElement = $(this).closest("tr");
//     let url = $(this).data("url");

//     if (confirm("Are you sure you want to delete this record?")) {
//         $.ajax({
//             url: url,
//             type: "DELETE",
//             headers: {
//                 "X-CSRF-TOKEN": "{{ csrf_token() }}" 
//             },
//             success: function (response) {
//                 if (response.success) {  
//                     alert(response.message);
//                     rowElement.fadeOut(500, function () {
//                         $(this).remove();
//                     });
//                 } else {
//                     alert(response.message);
//                 }
//             },
//             error: function (xhr) {
//                 alert("Failed to delete record. Please try again.");
//                 console.log(xhr.responseText);
//             }
//         });
//     }
// });


$(document).ready(function () {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    Delete Bank Detail
    $('.delete-btn').on('click', function () {
        let url = $(this).data('url');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 1) {
                        alert(response.message);
                        location.reload(); // Refresh the page after successful deletion
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    alert('Failed to delete record. Please try again.');
                }
            });
        }
    });
});
});
    </script>
     --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
{{--<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuEnA6QfwPq2xAQQnC3w23AsbQPm3OJbs&libraries&libraries=places" async defer></script>--}}
            <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuEnA6QfwPq2xAQQnC3w23AsbQPm3OJbs&libraries&libraries=places,drawing,geometry&callback=initMap"
        async defer></script>
    <script>
    window.addEventListener('load', function () {
        let interval = setInterval(() => {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                initAutocomplete();
                clearInterval(interval);
            }
        }, 100); // check every 100ms until google is ready
    });

    function initAutocomplete() {
        const input = document.getElementById('address');
        if (!input) return;

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            componentRestrictions: { country: 'IN' }
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No details available for this location!");
                return;
            }
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
</script>

     <script>
        $(document).ready(function () {
            // ✅ CSRF Token Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            /** ============================
             * ✅ Add Data Form
             * ============================ */
            $('#addForm').on('submit', function (e) {
                e.preventDefault();
                $('.error-message, .text-danger').empty();
                let formData = new FormData(this);
    
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 1) {
                            alert(response.message);
                            window.location.href = response.route;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function (field, messages) {
                                $('#' + field + '_error').html(messages.join('<br>'));
                            });
                        }
                    }
                });
            });
    
            /** ============================
             * ✅ Delete Function
             * ============================ */
            $(document).on('click', '.delete-btn', function () {
                let rowElement = $(this).closest('tr');
                let url = $(this).data('url');
    
                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function (response) {
                            if (response.status === 1) {
                                alert(response.message);
                                rowElement.fadeOut(500, function () {
                                    $(this).remove();
                                });
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function () {
                            alert('Failed to delete record. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
    
<script>
    $(document).ready(function() {
        // ✅ Country Select -> Load States
        $('#country').change(function() {
            var countryID = $(this).val();

            if (countryID) {
                let url = '/admin/get-states/' + countryID;
                console.log("Fetching states from:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        $('#state').html('<option>Loading...</option>'); // Loading indicator
                    },
                    success: function(data) {
                        console.log("States received:", data);
                        $('#state').empty().append('<option value="">-- Select State --</option>');
                        $('#city').empty().append('<option value="">-- Select City --</option>');

                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#state').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('#state').append('<option value="">No states available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching states:", xhr.responseText);
                        alert("Error fetching states. Please try again!");
                    }
                });
            } else {
                $('#state, #city').empty().append('<option value="">-- Select --</option>');
            }
        });

        // ✅ State Select -> Load Cities
        $('#state').change(function() {
            var stateID = $(this).val();
            console.log("Selected state ID:", stateID); // Debugging ke liye

            if (stateID) {
                let url = '/admin/get-cities/' + stateID;
                console.log("Fetching cities from:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        $('#city').html('<option>Loading...</option>'); // Loading indicator
                    },
                    success: function(data) {
                        console.log("Cities received:", data);
                        $('#city').empty().append('<option value="">-- Select City --</option>');

                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#city').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('#city').append('<option value="">No cities available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching cities:", xhr.responseText);
                        alert("Error fetching cities. Please try again!");
                    }
                });
            } else {
                $('#city').empty().append('<option value="">-- Select City --</option>');
            }
        });
        
        $(document).ready(function() {
            $(document).on('click', '.security-status', function() {
                var providerId = $(this).data('id');
                var planType = $(this).data('type');
                var clickedElement = $(this);

                if (clickedElement.hasClass('badge-success')) {
                    alert("Security plan is already active!");
                    return;
                }

                $('#providerId').val(providerId);
                $('#planType').val(planType);

                $.ajax({
                    url: '/admin/get-plans/' + providerId + '/' + planType,
                    type: 'GET',
                    success: function(response) {
                        var dropdown = $('#planDropdown');
                        dropdown.empty();

                        if (response.plans && response.plans.length > 0) {
                            $.each(response.plans, function(index, plan) {
                                dropdown.append('<option value="' + plan
                                    .id + '">' +
                                    plan.name + ' - ₹' + plan.price +
                                    '</option>');
                            });
                        } else {
                            dropdown.append(
                                '<option value="">No plans available</option>');
                        }

                        $('#planModal').modal('show');
                        $('#screenshot').val('');

                        $('#activatePlanBtn').off('click').on('click', function() {
                            var selectedPlanId = $('#planDropdown').val();
                            var fileInput = $('#paymentScreenshot')[0].files[0];

                            if (!selectedPlanId) {
                                alert("Please select a plan to activate.");
                                return;
                            }
                            
                            if (!fileInput) {
                                alert("Please upload payment screenshot.");
                                return;
                            }

                            var formData = new FormData();
                            formData.append('screenshot', fileInput);

                            $.ajax({
                                url: '/admin/activate-security/' +
                                    providerId + '/' +
                                    selectedPlanId,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                success: function(response) {
                                    if (response.status ===
                                        'success') {

                                        clickedElement
                                            .removeClass(
                                                'badge-danger')
                                            .addClass(
                                                'badge-success')
                                            .text('Active');

                                        $('#planModal').modal(
                                            'hide');
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function(xhr) {
                                    alert(
                                        "Failed to activate the plan. Please try again."
                                    );
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error("Error fetching plans:", xhr.responseText);
                        alert("Failed to load plans. Please try again.");
                    }
                });
            });
        });
    });
</script>
    <script>
        $(document).ready(function() {
            // Get Subcategories
            $('#category_id').change(function() {
                var categoryId = $(this).val();
                $('#subcategory_id').empty().append('<option value="">-- Select SubCategory --</option>');
                $('#sub_subcategory_id').empty().append(
                    '<option value="">-- Select Sub SubCategory --</option>');
                $('#servicesContainer').empty();

                if (categoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-subcategories') }}/${categoryId}`,
                        type: "GET",
                        success: function(data) {
                            data.forEach(function(value) {
                                $('#subcategory_id').append(
                                    `<option value="${value.id}">${value.name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            // Get Sub Subcategories
            $('#subcategory_id').change(function() {
                var categoryId = $('#category_id').val();
                var subcategoryId = $(this).val();
                $('#sub_subcategory_id').empty().append(
                    '<option value="">-- Select Sub SubCategory --</option>');
                $('#servicesContainer').empty();

                if (subcategoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-sub-subcategories') }}/${categoryId}/${subcategoryId}`,
                        type: "GET",
                        success: function(data) {
                            data.forEach(function(value) {
                                $('#sub_subcategory_id').append(
                                    `<option value="${value.id}">${value.sub_subcategory_name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            // Get Services with Images and Prices
            $('#sub_subcategory_id').change(function() {
                var categoryId = $('#category_id').val();
                var subcategoryId = $('#subcategory_id').val();
                var subSubcategoryId = $(this).val();

                if (subSubcategoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-services') }}/${categoryId}/${subcategoryId}/${subSubcategoryId}`,
                        type: "GET",
                        success: function(data) {
                            $('#servicesContainer').empty();
                            data.forEach(function(value) {
                                let imageUrl = "{{ url('/') }}" + "/" + value
                                    .image;
                                $('#servicesContainer').append(`
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="${imageUrl}" class="card-img-top" alt="Service Image" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">${value.service_name}</h5>
                                            <p class="card-text">Price: ₹ ${value.price}</p>
                                            <input type="checkbox" name="services[]" value="${value.id}"> Select<br>
                                            <small class="text-danger" id="services_error"></small>
                                        </div>
                                    </div>
                                </div>
                            `);
                            });
                        }
                    });
                }
            });
            
            // Fetch Slots
            $('#slot_date').change(function() {
                const date = $(this).val();
                $.get(`/admin/get-daily-slots?date=${date}`, function(data) {
                    $('#slot_time').empty().append('<option value="">-- Select Slot --</option>');
                    data.slots.forEach(function(slot) {
                        $('#slot_time').append(
                            `<option value="${slot.start_time}-${slot.end_time}">${slot.slot}</option>`
                            );
                    });
                });
            });
            
            //Fetch Providers
            $('#provider_id').select2({
                placeholder: "-- Select Provider --",
                allowClear: true,
                width: '100%',
                matcher: function(params, data) {
                    // If there is no search term, return all data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Search by option text (name)
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }

                    // Search by mobile safely
                    var mobile = $(data.element).data('mobile');
                    if (mobile && typeof mobile === 'string') {
                        if (mobile.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }
                    }

                    // Return null if no match
                    return null;
                }
            });

            // Trigger AJAX to populate providers when zone/category changes
            $('#zone_id, #category_id').on('change', function () {
                let zoneId = $('#zone_id').val();
                let categoryId = $('#category_id').val();

                if (zoneId && categoryId) {
                    $.ajax({
                        url: "{{ route('get.providers') }}",
                        type: "GET",
                        data: { zone_id: zoneId, category_id: categoryId },
                        success: function (data) {
                            let providerSelect = $('#provider_id');
                            providerSelect.empty();
                            providerSelect.append('<option value=""></option>'); // For placeholder

                            if (data.length > 0) {
                                $.each(data, function (index, provider) {
                                    providerSelect.append(
                                        `<option value="${provider.user_id}" data-mobile="${provider.mobile_no}">
                                            ${provider.name} (${provider.mobile_no})
                                        </option>`
                                    );
                                });
                            }

                            providerSelect.trigger('change'); // Refresh Select2
                        }
                    });
                }
            });

        });
                document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (file && allowedTypes.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                alert('Only JPG, JPEG, and PNG files are allowed.');
                event.target.value = '';
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
    </script>
        <script>
        document.getElementById('video').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let videoPreview = document.getElementById('videoPreview');
                let videoSource = document.getElementById('videoSource');

                videoSource.src = URL.createObjectURL(file);
                videoPreview.style.display = "block";
                videoPreview.load();
            }
        });
    </script>
        <script>
        $(document).ready(function() {
            $('.cancel-booking-btn').on('click', function() {
                if (!confirm('Are you sure you want to cancel this booking?')) return;

                let button = $(this);
                let bookingId = button.data('id');

                $.ajax({
                    url: "{{ route('admin.booking.cancel') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: bookingId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Refresh the page on success
                            location.reload();
                        } else {
                            alert('Something went wrong.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while cancelling.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            initDataTable();

            $('#bannerType').on('change', function () {
                var type = $(this).val();

                $.ajax({
                    url: "{{ route('admin.banners') }}",
                    type: "GET",
                    data: {
                        type: type
                    },
                    success: function (response) {
                        $('#bannersTable').html(response);
                        initDataTable();
                    },
                    error: function () {
                        alert('Failed to fetch filtered data.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#categoryType').on('change', function () {
                let categoryId = $(this).val();

                $.ajax({
                    url: "{{ route('admin.providers') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function (response) {
                        $('#providersTableBody').html(response); // ✅ partial body update
                        initDataTable();
                    },
                    error: function () {
                        alert('Failed to fetch filtered data.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            function fetchFilteredData() {
                let categoryId = $('#categoryFilter').val();
                let status = $('#statusFilter').val();

                $.ajax({
                    url: "{{ route('admin.partners_data') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId,
                        status: status
                    },
                    success: function (response) {
                        $('#partnersTable').html(response.html);
                        initDataTable(); // reinitialize if using DataTables
                    },
                    error: function () {
                        alert('Failed to fetch filtered data.');
                    }
                });
            }

            $('#categoryFilter, #statusFilter').on('change', fetchFilteredData);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.status-dropdown').on('change', function () {
                const status = $(this).val();
                const partnerId = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.partners.updateStatus') }}", // Define this route in web.php
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: partnerId,
                        status: status
                    },
                    success: function (response) {
                        console.log('Status updated:', response.message);
                    },
                    error: function (xhr) {
                        alert('Failed to update status');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('change', '.editable', function () {
            var field = $(this).data('field');
            var id = $(this).data('id');
            var value = $(this).val();

            $.ajax({
                url: "{{ route('admin.update_booking_field') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    field: field,
                    value: value
                },
                success: function (response) {
                    console.log("Updated successfully");
                },
                error: function (xhr) {
                    alert("Error updating field");
                }
            });
        });
    </script>
    <script>
        const officeLat = 28.6139; // Replace with your actual office latitude
        const officeLng = 77.2090; // Replace with your actual office longitude

        function sendPunchRequest(url, successMsg) {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            navigator.geolocation.getCurrentPosition(function (position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                $.post(url, {
                    latitude: userLat,
                    longitude: userLng
                }, function (res) {
                    $('#punchStatus').text(successMsg);
                    location.reload();
                }).fail(function (xhr) {
                    alert('Error: ' + (xhr.responseJSON.message || 'Something went wrong.'));
                });

            }, function () {
                alert('Location access denied. Please enable it to punch in/out.');
            });
        }

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('#punchInBtn').click(function () {
                sendPunchRequest("{{ route('admin.punchIn') }}", 'Successfully Punched In!');
            });

            $('#punchOutBtn').click(function () {
                sendPunchRequest("{{ route('admin.punchOut') }}", 'Successfully Punched Out!');
            });
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const target = document.getElementById("partnersTable");

        if (target) {
            target.addEventListener("contextmenu", e => e.preventDefault());
            target.addEventListener("selectstart", e => e.preventDefault());
            target.addEventListener("copy", e => e.preventDefault());
            target.addEventListener("cut", e => e.preventDefault());
        }

        document.addEventListener("keydown", function (e) {
            const key = e.key.toLowerCase(); // lowercase for consistent comparison

            if (
                key === "f12" ||
                (e.ctrlKey && e.shiftKey && ["i", "j", "c"].includes(key)) ||
                (e.ctrlKey && ["u", "a"].includes(key))
            ) {
                e.preventDefault();
                return false; // Just in case
            }
        }, false);
    });
</script>


        <script>
        $('#sharedComplaintModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            $('#complaint_user_id').val(button.data('user'))
            $('#complaint_provider_id').val(button.data('provider'))
            $('#complaint_order_id').val(button.data('order'))
            $('#complaintModalTitle').text('Add Complaint for Booking ID: ' + button.data('booking'))
        });
    </script>
    <script>
        let map;
        let drawingManager;
        let selectedPolygon;
        let infoWindow;
        let tempLine;
        let lastClickedLatLng = null;
        let distanceUpdateTimeout;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 28.6139,
                    lng: 77.2090
                }, // Default: Delhi
                zoom: 10
            });

            // Place search
            const input = document.getElementById("place-search");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) return;
                    bounds.extend(place.geometry.location);
                    // If the place has a viewport (like a city or larger area), use it
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    }
                });

                map.fitBounds(bounds);
                // map.setZoom(15);

                // Automatically draw polygon for the searched area
                if (selectedPolygon) {
                    selectedPolygon.setMap(null); // Remove previous polygon
                }

                const coords = getPolygonCoordinatesFromBounds(bounds);
                drawPolygon(coords);
            });
                // searchBox.addListener("places_changed", () => {
                    // const places = searchBox.getPlaces();
                    // if (places.length === 0) return;
                
                    // const place = places[0];
                
                    // if (!place.geometry || !place.geometry.location) return;
                
                    // // Zoom to the place
                    // map.setCenter(place.geometry.location);
                    // map.setZoom(15);
                
                    // // Optionally: place a marker
                    // if (window.searchMarker) {
                    //     window.searchMarker.setMap(null);
                    // }
                    // window.searchMarker = new google.maps.Marker({
                    //     map,
                    //     position: place.geometry.location
                    // });
                
                    // Optional: Suggest user to draw polygon now
                    // alert("आप इस लोकेशन के आस-पास polygon ड्रॉ कर सकते हैं।");
                
                    // Optional: Auto-fill name field with place name
                //     if (place.name && document.getElementById('name').value.trim() === '') {
                //         document.getElementById('name').value = place.name;
                //     }
                // });

            // Function to get polygon coordinates from bounds (approximate box)
            function getPolygonCoordinatesFromBounds(bounds) {
                return [{
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getNorthEast().lng()
                    }, // NE
                    {
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getSouthWest().lng()
                    }, // NW
                    {
                        lat: bounds.getSouthWest().lat(),
                        lng: bounds.getSouthWest().lng()
                    }, // SW
                    {
                        lat: bounds.getSouthWest().lat(),
                        lng: bounds.getNorthEast().lng()
                    }, // SE
                    {
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getNorthEast().lng()
                    } // Close the polygon
                ];
            }

            // Function to draw a polygon on map
            function drawPolygon(coordinates) {
                selectedPolygon = new google.maps.Polygon({
                    paths: coordinates,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    editable: true,
                    draggable: true
                });

                selectedPolygon.setMap(map);
                updateBoundary(selectedPolygon);

                // Add event listeners to update polygon data on edit
                google.maps.event.addListener(selectedPolygon.getPath(), "set_at", () => updateBoundary(selectedPolygon));
                google.maps.event.addListener(selectedPolygon.getPath(), "insert_at", () => updateBoundary(
                selectedPolygon));
            }

            // Drawing manager
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true,
                    draggable: true,
                    fillColor: "#FF0000",
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    fillOpacity: 0.35,
                    cursor: "crosshair"
                }
            });

            drawingManager.setMap(map);
            infoWindow = new google.maps.InfoWindow();

            // Polygon draw event
            google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                    if (selectedPolygon) {
                        selectedPolygon.setMap(null); // Remove previous polygon
                    }
                    selectedPolygon = event.overlay;
                    updateBoundary(selectedPolygon);

                    // Live update on vertex drag or edit
                    google.maps.event.addListener(selectedPolygon.getPath(), "set_at", () => updateBoundary(
                        selectedPolygon));
                    google.maps.event.addListener(selectedPolygon.getPath(), "insert_at", () => updateBoundary(
                        selectedPolygon));
                }
            });

            // Capture last clicked point for distance calculation
            map.addListener("click", (event) => {
                lastClickedLatLng = event.latLng;
            });

            // Throttle mousemove event
            map.addListener("mousemove", (event) => {
                if (lastClickedLatLng) {
                    if (distanceUpdateTimeout) {
                        clearTimeout(distanceUpdateTimeout);
                    }
                    distanceUpdateTimeout = setTimeout(() => {
                        drawTempLine(lastClickedLatLng, event.latLng);
                        showRealTimeDistance(lastClickedLatLng, event.latLng);
                    }, 100); // Adjust the delay as needed
                }
            });

            // Reset button
            document.getElementById("reset-zone").addEventListener("click", resetZone);
        }

        // Calculate and update polygon details
        function updateBoundary(polygon) {
            const path = polygon.getPath();
            let coordinates = [];
            let latSum = 0,
                lngSum = 0;
            let totalDistance = 0;

            for (let i = 0; i < path.getLength(); i++) {
                let latLng = path.getAt(i);
                coordinates.push({
                    lat: latLng.lat(),
                    lng: latLng.lng()
                });
                latSum += latLng.lat();
                lngSum += latLng.lng();

                if (i > 0) {
                    totalDistance += haversineDistance(path.getAt(i - 1), latLng);
                }
            }

            // Close the polygon loop
            totalDistance += haversineDistance(path.getAt(path.getLength() - 1), path.getAt(0));

            let centerLat = latSum / coordinates.length;
            let centerLng = lngSum / coordinates.length;
            let area = google.maps.geometry.spherical.computeArea(path) / 1e6; // Convert to km²

            document.getElementById("boundary").value = JSON.stringify(coordinates);
            document.getElementById("center_lat").value = centerLat;
            document.getElementById("center_lng").value = centerLng;
            document.getElementById("perimeter").value = totalDistance.toFixed(2);
            document.getElementById("area").value = area.toFixed(2);

            getAreaNames(polygon); // 👈 Reverse geocode and populate areas

            document.getElementById("distance-info").innerHTML = `
            <strong>Perimeter:</strong> ${totalDistance.toFixed(2)} km &nbsp;&nbsp;
            <strong>Area:</strong> ${area.toFixed(2)} km²
        `;
        }

        // Draw a dynamic line for real-time distance
        function drawTempLine(startLatLng, endLatLng) {
            if (tempLine) {
                tempLine.setMap(null); // Remove previous line
            }

            tempLine = new google.maps.Polyline({
                path: [startLatLng, endLatLng],
                geodesic: true,
                strokeColor: "#0000FF",
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: map
            });
        }

        // Show live distance between last clicked point and current cursor
        function showRealTimeDistance(startLatLng, endLatLng) {
            const distance = haversineDistance(startLatLng, endLatLng);
            infoWindow.setContent(`<strong>Distance:</strong> ${distance.toFixed(2)} km`);
            infoWindow.setPosition(endLatLng);
            infoWindow.open(map);
        }

        // Haversine formula for distance calculation
        function haversineDistance(latlng1, latlng2) {
            const R = 6371; // Radius of Earth in km
            const dLat = toRad(latlng2.lat() - latlng1.lat());
            const dLng = toRad(latlng2.lng() - latlng1.lng());
            const lat1 = toRad(latlng1.lat());
            const lat2 = toRad(latlng2.lat());

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function toRad(deg) {
            return deg * (Math.PI / 180);
        }

        // Reset everything
        function resetZone() {
            if (selectedPolygon) selectedPolygon.setMap(null);
            if (tempLine) tempLine.setMap(null);
            selectedPolygon = null;
            lastClickedLatLng = null;

            document.getElementById("boundary").value = "";
            document.getElementById("center_lat").value = "";
            document.getElementById("center_lng").value = "";
            document.getElementById("perimeter").value = "";
            document.getElementById("area").value = "";
            document.getElementById("distance-info").innerHTML = "";
        }
        // Reverse geocode lat/lng to get area names
        function getAreaNames(polygon) {
            const path = polygon.getPath();
            let areaNames = [];
            const geocoder = new google.maps.Geocoder();

            let pendingRequests = path.getLength();

            path.forEach((latLng, index) => {
                geocoder.geocode({
                    location: latLng
                }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        areaNames.push(results[0].formatted_address);
                    } else {
                        console.error("Geocoder failed:", status);
                    }
                    pendingRequests--;

                    if (pendingRequests === 0) { // Ensure all requests are complete
                        document.getElementById("areas").value = JSON.stringify(areaNames);
                    }
                });
            });
        }
    </script>
    <script>
        $('#zoneModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var orderId = button.data('id');
            console.log('Opening modal for order ID:', orderId);
            $('#zone_order_id').val(orderId);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{--<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeXtzgRc95cYuOaZD0fjyHsnqVg9Imf30&libraries=places,drawing,geometry&callback=initMap"
        async defer></script>--}}
</body>

</html>
