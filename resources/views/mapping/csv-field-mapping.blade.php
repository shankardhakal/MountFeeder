<script>
    var feedId = '{!! $feed->id !!}';
</script>

<div class="container">
    <h3 class="h3">{{ $feed->name }} Mapping</h3>
    <div class="row"><br></div>

    <div class="col-md-6">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Woocommerce Field</th>
                <th scope="col">{{ $feed->name }} Field</th>
            </tr>
            </thead>
            <tbody>

            @foreach($woocommerceProductFields as $internalField=>$woocommerceField)
                <tr>
                    <td>{{ $index++ +1 }}</td>
                    <td> {{ $internalField }}</td>
                    <td>
                        <select data-mapping-id="{{($mapping[$internalField]['id'] ?? 0)}}"
                                data-maps-to="{{$internalField}}"
                                class="source-fields form-control">
                            <option value="">Select field</option>
                            @foreach($feedFields as $field)
                                @php
                                    $isMapped = ($mapping[$internalField]['mappedTo'] ?? false) && ($mapping[$internalField]['mappedTo'] === $field);
                                $selected='';
                                if ($isMapped) $selected='selected="selected"'
                                @endphp
                                <option value="{{$field}}" {{ $selected }}> {{  $field }}</option>
                            @endforeach
                        </select>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

</div>


<script>

    $(document).ready(function (event) {

        $('.source-fields').change(function () {
                let mapsTo = $(this).data('maps-to');
                let selectedValue = $(this).val();
                let mappingData = {};
                mappingData['woocommerce-field'] = mapsTo;
                mappingData['mapped-to'] = selectedValue;
                mappingData['id'] = $(this).data('mapping-id');
                mappingData['feed-id'] = feedId;

                console.log(mappingData);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': LA.token
                    }
                });
                $.ajax({
                    method: "POST",
                    url: "{{ route('add-feed-field-mapping') }}",

                    data: mappingData
                })
                    .done(function (msg) {
                        toastr.options = {
                            "closeButton": false,
                            "debug": false,
                            "newestOnTop": true,
                            "progressBar": false,
                            "positionClass": "toast-bottom-center",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                        toastr["success"]("Field mapped!")
                    }).fail(function (msg) {

                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": false,
                        "positionClass": "toast-bottom-center",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "4000",
                        "extendedTimeOut": "0",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr["error"]("An error occurred while updating the mapping.")
                });
            }
        );
    });
</script>
