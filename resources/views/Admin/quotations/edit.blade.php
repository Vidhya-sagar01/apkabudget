@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Quotation</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <h4 class="m-3">Edit Quotation</h4>
        <form action="{{ route('admin.edit-quotations', $quotation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Quotation Header --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Quotation No</label>
                        <input type="text" name="quotation_no" class="form-control" value="{{ $quotation->quotation_no }}" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Booking Id</label>
                        <select name="order_id" id="order_id" class="form-control mb-2" required>
                            <option value="{{ $quotation->order->id }}">{{ $quotation->order->booking_id }}</option>
                            @foreach($bookings as $booking)
                                @if($booking->id != $quotation->order->id)
                                    <option value="{{ $booking->id }}">{{ $booking->booking_id }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Quotation Date</label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ $quotation->quotation_date }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mt-2">
                        <label>Custom Address</label>
                        <input type="text" name="custom_address" class="form-control" value="{{ $quotation->custom_address ?? 'NA' }}" placeholder="Enter Customer Address..">
                    </div>
                </div>

                {{-- Quotation From --}}
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><strong>Quotation From</strong> <small class="text-muted">Company Details</small></h5>
                        <input type="text" class="form-control mb-2 font-weight-bold" value="RBSR APKA BUDGET HOME SERVICES PVT. LTD." readonly>
                        <input type="hidden" name="quotation_from" value="{{ $quotation->contact->id }}">
                        <div class="border p-2 rounded bg-light">
                            <p><strong>Company Address: </strong> {{ $quotation->contact->address ?? 'NA' }}</p>
                            <p><strong>Company Email: </strong> {{ $quotation->contact->email ?? 'NA' }}</p>
                            <p><strong>Company Phone: </strong> {{ $quotation->contact->phone_number ?? 'NA' }}, {{ $quotation->contact->whatsapp_number ?? 'NA' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Quotation For --}}
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><strong>Quotation For</strong> <small class="text-muted">Client's Details</small></h5>
                        <input type="hidden" name="quotation_for" id="client_id" value="{{ $quotation->user->id }}">
                        <input type="text" id="quotation_for" class="form-control mb-2 font-weight-bold" value="{{ $quotation->user ? strtoupper($quotation->user->name) : 'NA' }}" readonly>
                        <div class="border p-2 rounded bg-light">
                            <p><strong>Client Address: </strong> <span id="client_address">{{ $quotation->order->address->address ?? 'NA' }}</span></p>
                            <p><strong>Client Email: </strong> <span id="client_email">{{ $quotation->user->email ?? 'NA' }}</span></p>
                            <p><strong>Client Phone: </strong> <span id="client_phone">{{ $quotation->user->mobile_no ?? 'NA' }}</span></p>
                        </div>
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
                        @foreach($quotation->items as $index => $item)
                        <tr>
                            <td>
                                <input type="text" name="items[{{ $index }}][service_name]" class="form-control mb-2" value="{{ $item->service_name }}" required>
                                <div class="description-editor mt-2">
                                    <textarea class="form-control description-field" name="items[{{ $index }}][description]" rows="3">{{ $item->description }}</textarea>
                                </div>
                                <div class="attachment-input mt-2">
                                    <input type="file" name="items[{{ $index }}][image][]" class="form-control" multiple>
                                </div>
                            </td>
                            <td>
                                <select name="items[{{ $index }}][unit]" class="form-control">
                                    <option value="sqft" {{ $item->unit == 'sqft' ? 'selected' : '' }}>SQFT</option>
                                    <option value="unit" {{ $item->unit == 'unit' ? 'selected' : '' }}>Unit</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}"></td>
                            <td><input type="number" name="items[{{ $index }}][rate]" class="form-control" value="{{ $item->rate }}" ></td>
                            <td><input type="text" name="items[{{ $index }}][amount]" class="form-control" value="{{ $item->amount }}" readonly></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                <button type="button" class="btn btn-info btn-sm duplicateRow">Duplicate</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-outline-primary" id="addNewLine">+ Add New Line</button>
                </div>
            </div>

            {{-- Discounts / Reductions --}}
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="form-group">
                        <div class="d-flex mb-1">
                            <label class="fw-bold" style="width:278px;">Total (Before Discount)</label>
                            <label class="fw-bold" style="width:253;">Discount Amount</label>
                            <label class="fw-bold" style="width:150px;">Discount Type</label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="text" name="total_value" id="total_value" class="form-control me-2" value="{{ $quotation->total_amount }}" style="max-width: 170px;" readonly>
                            <input type="number" name="reduction_value" class="form-control me-2" value="{{ $quotation->discount_value }}" style="max-width: 150px;">
                            <select name="reduction_type" class="form-control me-2" style="max-width: 150px;">
                                <option value="%" {{ $quotation->discount_type == '%' ? 'selected' : '' }}>%</option>
                                <option value="₹" {{ $quotation->discount_type == '₹' ? 'selected' : '' }}>₹</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right mt-4">
                    <h6><strong>Total (Before Discount):</strong> ₹<span id="beforeDiscount">{{ $quotation->total_amount }}</span></h6>
                    <h6><strong>Final Total (After Discount):</strong> ₹<span id="afterDiscount">{{ $quotation->total_amount - ($quotation->discount_type == '%' ? ($quotation->total_amount * $quotation->discount_value / 100) : $quotation->discount_value) }}</span></h6>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    @if(!$quotation->add_notes)
                        <button type="button" class="btn btn-primary" id="btnAddNote" style="display: block;">
                            Add Note</button>
                        <div id="noteBoxRow" style="display: none;">
                            <textarea id="add_notes" name="add_notes" class="form-control" rows="5" placeholder="Enter note here..."></textarea>
                        </div>
                    @else
                        <button type="button" class="btn btn-primary" id="btnAddNote" style="display: none;">Add Note</button>
                        <div id="noteBoxRow" style="display: block;">
                            <textarea id="add_notes" name="add_notes" class="form-control" rows="5" placeholder="Enter note here...">
                                {{ $quotation->add_notes }}
                            </textarea>
                        </div>
                    @endif
                </div>
            </div>


            <!-- The note box / modal container which is hidden initially -->
            

            <div class="row mt-4">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Save Quotation</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}

<!-- Include TinyMCE from CDN (or your local copy) -->
<script src="https://cdn.tiny.cloud/1/4a8aml27ocx72rhvudu8h20tttgrjsrn4pxv72xpstosiq04/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE on the textarea
    tinymce.init({
        selector: '#add_notes',
        plugins: 'image link media table code',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code',
        height: 300,
        menubar: false
    });

    // Show note textarea on button click
    const btnAdd = document.getElementById('btnAddNote');
    const noteRow = document.getElementById('noteBoxRow');

    if(btnAdd){
        btnAdd.addEventListener('click', function() {
            noteRow.style.display = 'block';
            btnAdd.style.display = 'none'; // hide the button
            // Focus TinyMCE editor after opening
            setTimeout(() => {
                tinymce.get('add_notes').focus();
            }, 200);
        });
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let rowIndex = {{ $quotation->items->count() }};

// Add new line dynamically
$('#addNewLine').click(function(){
    let newRow = $('#itemRows tr:first').clone();
    newRow.find('input, select, textarea').each(function(){
        let name = $(this).attr('name');
        if(name) {
            $(this).attr('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
            $(this).val($(this).is('select') ? $(this).find('option:first').val() : '');
        }
    });
    newRow.find('.description-editor, .attachment-input');
    $('#itemRows').append(newRow);
    rowIndex++;
    calculateTotals();
});

// Row actions
$(document).on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    calculateTotals();
});
$(document).on('click', '.duplicateRow', function(){
    let clone = $(this).closest('tr').clone();
    $('#itemRows').append(clone);
    calculateTotals();
});
$(document).on('click', '.toggleDescription', function(){
    $(this).closest('td').find('.description-editor').toggleClass('d-none');
});
$(document).on('click', '.toggleAttachment', function(){
    $(this).closest('td').find('.attachment-input').toggleClass('d-none');
});

// Calculate amounts
function calculateTotals(){
    let total = 0;
    $('#itemRows tr').each(function(){
        let qty = parseFloat($(this).find('input[name*="[quantity]"]').val()) || 0;
        let rate = parseFloat($(this).find('input[name*="[rate]"]').val()) || 0;
        let amount = qty * rate;
        $(this).find('input[name*="[amount]"]').val(amount.toFixed(2));
        total += amount;
    });
    $('#total_value').val(total.toFixed(2));
    $('#beforeDiscount').text(total.toFixed(2));

    let discountVal = parseFloat($('input[name="reduction_value"]').val()) || 0;
    let discountType = $('select[name="reduction_type"]').val();
    let finalTotal = discountType == '%' ? total - (total * discountVal / 100) : total - discountVal;
    if(finalTotal < 0) finalTotal = 0;
    $('#afterDiscount').text(finalTotal.toFixed(2));
}

// Recalculate on input change
$(document).on('input', 'input[name*="[quantity]"], input[name*="[rate]"], input[name="reduction_value"], select[name="reduction_type"]', calculateTotals);

// Initial calculation
$(document).ready(calculateTotals);
</script>

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
@endsection
