@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Quotation</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <h4 class="m-3">Add Quotation</h4>
        <form action="{{ route('admin.add-quotations') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                @if($quotationNo)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" class="form-control" value="{{ $quotationNo }}" readonly>
                            <span class="text-danger" id="quotation_no_error"></span>
                        </div>
                    </div>
                @else
                    <p>No Quotation No available.</p>
                @endif
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Booking Id</label>
                        <select name="order_id" id="order_id" class="form-control mb-2" required>
                            <option value="">Select a Booking Id</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}">{{ $booking->booking_id }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="booking_id_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Quotation Date</label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        <span class="text-danger" id="quotation_date_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mt-2">
                        <label>Custom Address</label>
                        <input type="text" name="custom_address" class="form-control" value="" placeholder="Enter Customer Address..">
                        <span class="text-danger" id="custom_address_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><strong>Quotation From</strong> <small class="text-muted">Company Details</small></h5>
                        @if($contact_us)
                        
                        <input type="text" id="quotation_from" class="form-control mb-2 font-weight-bold" value="RBSR APKA BUDGET HOME SERVICES PVT. LTD." readonly>

                        {{-- Hidden input (actual value submitted) --}}
                        <input type="hidden" name="quotation_from" id="quotation_from" value="{{ $contact_us->id }}">
                        <div class="border p-2 rounded bg-light">
                            <p><strong>Company Address: </strong> {{ $contact_us->address ?? 'NA' }}</p>
                            <p><strong>Company Email: </strong> {{ $contact_us->email ?? 'NA' }}</p>
                            <p><strong>Company Phone: </strong> {{ $contact_us->phone_number ?? 'NA' }}, {{ $contact_us->whatsapp_number ?? 'NA' }}</p>
                        </div>
                        @else
                            <p>No contact info available.</p>
                        @endif
                        <span class="text-danger" id="quotation_from_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><strong>Quotation For</strong> <small class="text-muted">Client's Details</small></h5>

                        <input type="hidden" name="quotation_for" id="client_id" value="">

                        <input type="text" id="quotation_for" value="CLIENT NAME" class="form-control mb-2 font-weight-bold" readonly>

                        <div class="border p-2 rounded bg-light">
                            <p><strong>Client Address: </strong> <span id="client_address"></span></p>
                            <p><strong>Client Email: </strong> <span id="client_email"></span></p>
                            <p><strong>Client Phone: </strong> <span id="client_phone"></span></p>
                        </div>
                        <span class="text-danger" id="quotation_for_error"></span>
                    </div>
                </div>                
            </div>

            {{-- Item Table --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Service Name</th>
                            <th>Service Unit</th>
                            <th>Quantity</th>
                            <th>Rate Per Unit</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemRows">
                        <tr>
                            <td>
                                <input type="text" name="items[0][service_name]" class="form-control mb-2" placeholder="Service Name (Required)" required>

                                <div class="description-editor d-none mt-2">
                                    <textarea class="form-control description-field" name="items[0][description]" rows="3"></textarea>
                                </div>

                                <div class="attachment-input d-none mt-2">
                                    <input type="file" name="items[0][image][]" class="form-control" multiple>
                                </div>

                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggleDescription">+ Add Description</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggleAttachment">+ Add Attachment</button>
                                </div>
                            </td>
                            <td>
                                <select name="items[0][unit]" class="form-control">
                                    <option value="sqft">SQFT</option>
                                    <option value="unit">Unit</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control" value="" min="1"></td>
                            <td><input type="number" name="items[0][rate]" class="form-control" value="" min="1"></td>
                            <td><input type="text" name="items[0][amount]" class="form-control" value="" readonly></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                <button type="button" class="btn btn-info btn-sm duplicateRow">Duplicate</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-outline-primary" id="addNewLine">+ Add New Line</button>
                </div>
            </div>
             {{-- Reductions / Discounts --}}
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="form-group">
                        <!-- Labels row -->
                        <div class="d-flex  mb-1">
                            <label class="fw-bold" style="width:180px;">Total (Before Discount)</label>
                            <label class="fw-bold" style="width:165px;">Discount Amount</label>
                            <label class="fw-bold" style="width:150px;">Discount Type</label>
                        </div>

                        <!-- Inputs + Button row -->
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="text" name="total_value" id="total_value" class="form-control me-2" value="" style="max-width: 170px;" readonly>
                            <input type="number" name="reduction_value" class="form-control me-2" value="" style="max-width: 150px;">
                            <select name="reduction_type" class="form-control me-2" style="max-width: 150px;">
                                <option value="%">%</option>
                                <option value="₹">₹</option>
                            </select>
                            <button type="button" class="btn btn-outline-success">+ Add Discounts</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="btnAddNote">Add Note</button>
                </div>
                <div class="col-md-10 text-right">
                    <h6><strong>Total (Before Discount):</strong> ₹<span id="beforeDiscount">0.00</span></h6>
                    <h6><strong>Final Total (After Discount):</strong> ₹<span id="afterDiscount">0.00</span></h6>
                </div>
            </div>

            <!-- The note box / modal container which is hidden initially -->
            <div class="row mt-3" id="noteBoxRow" style="display: none;">
                <div class="col-md-12">
                    <textarea id="add_notes" name="add_notes" class="form-control" rows="5" placeholder="Enter note here..."></textarea>
                    <span class="text-danger" id="add_notes_error"></span>
                </div>
            </div>


            <div class="row mt-4">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Save Quotation</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Include TinyMCE from CDN (or your local copy) -->
<script src="https://cdn.tiny.cloud/1/4a8aml27ocx72rhvudu8h20tttgrjsrn4pxv72xpstosiq04/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE on the textarea when it appears
    tinymce.init({
        selector: '#add_notes',
        plugins: 'image link media table code',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code',
        height: 300,
        menubar: false
    });

    // Show and hide logic
    const btnAdd = document.getElementById('btnAddNote');
    const noteRow = document.getElementById('noteBoxRow');
    const btnCancel = document.getElementById('cancelNoteBtn');
    const btnSave = document.getElementById('saveNoteBtn');

    btnAdd.addEventListener('click', function() {
        noteRow.style.display = 'block';
        // optional: scroll into view, focus editor
        tinymce.get('add_notes').focus();
    });

    btnCancel.addEventListener('click', function() {
        noteRow.style.display = 'none';
        tinymce.get('add_notes').setContent('');  // clear content
    });

    btnSave.addEventListener('click', function() {
        const noteContent = tinymce.get('add_notes').getContent();
        // you can send it to server via AJAX or include in form
        console.log('Note:', noteContent);
        // after saving, maybe hide box or keep open
        alert('Note saved!'); 
        noteRow.style.display = 'none';
    });
    });
</script>



{{-- Scripts --}}
<script>
    let rowIndex = 1;

    // Add New Line
    document.getElementById('addNewLine').addEventListener('click', function () {
        let newRow = document.querySelector('#itemRows tr').cloneNode(true);
        newRow.querySelectorAll('input, select, textarea').forEach(el => {
            let name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
            }
            if(el.type !== "hidden") el.value = "";
        });
        newRow.querySelector('.description-editor').classList.add('d-none');
        newRow.querySelector('.attachment-input').classList.add('d-none');
        document.querySelector('#itemRows').appendChild(newRow);
        rowIndex++;
    });

    // Row Actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
        if (e.target.classList.contains('duplicateRow')) {
            let clone = e.target.closest('tr').cloneNode(true);
            document.querySelector('#itemRows').appendChild(clone);
        }
        if (e.target.classList.contains('toggleDescription')) {
            let descBox = e.target.closest('td').querySelector('.description-editor');
            descBox.classList.toggle('d-none');
        }
        if (e.target.classList.contains('toggleAttachment')) {
            let attachBox = e.target.closest('td').querySelector('.attachment-input');
            attachBox.classList.toggle('d-none');
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#order_id').on('change', function () {
        let orderId = $(this).val();

        if (orderId) {
            $.ajax({
                url: "{{ route('admin.getUserDataByBookingId', ['booking_id' => ':id']) }}".replace(':id', orderId),
                type: "GET",
                success: function(response) {
                    let clientName = response.name ? response.name.toUpperCase() : "NA";

                    $('#quotation_for').val(clientName);
                    $('#client_id').val(response.id);
                    $('#client_address').text(response.address ? response.address : "NA");
                    $('#client_email').text(response.email ? response.email : "NA");
                    $('#client_phone').text(response.mobile ? response.mobile : "NA");
                },
            });
        } else {
            // Reset fields if no booking selected
            $('#quotation_for').val('CLIENT NAME');
            $('#client_address').text('');
            $('#client_email').text('');
            $('#client_phone').text('');
        }
    });
});
</script>

<script>
    function calculateRowAmount(row) {
        let qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        let rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
        let amount = qty * rate;

        let amountInput = row.querySelector('input[name*="[amount]"]');
        if (amountInput) {
            amountInput.value = amount.toFixed(2);
        }

        return amount;
    }

    function calculateTotals() {
        let total = 0;

        // loop all rows
        document.querySelectorAll('#itemRows tr').forEach(row => {
            total += calculateRowAmount(row);
        });

        // update total_value hidden field
        let totalInput = document.querySelector('input[name="total_value"]');
        if (totalInput) totalInput.value = total.toFixed(2);

        // show before discount
        document.getElementById('beforeDiscount').innerText = total.toFixed(2);

        // discount
        let discountVal = parseFloat(document.querySelector('input[name="reduction_value"]').value) || 0;
        let discountType = document.querySelector('select[name="reduction_type"]').value;
        let finalTotal = total;

        if (discountType === "%") {
            finalTotal -= (total * discountVal / 100);
        } else {
            finalTotal -= discountVal;
        }

        if (finalTotal < 0) finalTotal = 0;

        document.getElementById('afterDiscount').innerText = finalTotal.toFixed(2);
    }

    // Recalculate whenever relevant fields change
    document.addEventListener('input', function(e) {
        if (
            e.target.name.includes('[quantity]') ||
            e.target.name.includes('[rate]') ||
            e.target.name === 'reduction_value' ||
            e.target.name === 'reduction_type'
        ) {
            calculateTotals();
        }
    });

    // Also recalc on load
    document.addEventListener('DOMContentLoaded', calculateTotals);

    // Ensure duplicate or new row triggers recalculation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('duplicate-row') || e.target.classList.contains('remove-row')) {
            setTimeout(calculateTotals, 100); // wait DOM update then recalc
        }
    });

</script>
@endsection
