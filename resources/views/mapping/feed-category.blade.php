<script src="{{asset('js/bootstrap-select.min.js')}}"></script>
<link rel="stylesheet" href="{{asset('css/bootstrap-select.min.css')}}">

<script>
    var mappingData = JSON.parse('@json($currentMapping)');

    if (mappingData=== null){
        mappingData={};
    }
    var feedIdentification = '{{ $feedIdentification }}';
</script>
<div class="container">
    <h3 class="h3">{{ $feed->slug }} Category Mapping for website {{ $feed->website->name }}</h3>
    <div class="row"><br></div>

    <div class="row">
        <div class="col-md-8">
            <table class="table">
                <div class="sticky">
                    Save changes
                </div>
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ $feed->website->name }} Categories</th>
                    <th scope="col">{{ $feed->slug }} Categories</th>
                </tr>
                </thead>
                <tbody>

                @foreach($wocommerceCategories as $category)
                    <tr>
                        <td>{{ $index++ +1 }}</td>
                        <td> {{ $category }}</td>
                        <td>
                            <select  data-live-search="true"
                               data-woocommerce-category="{{ $category }}"
                                class="selectpicker website-category form-control">
                                <option value="" >Select Category</option>
                                @foreach($csvCategoryFields as $fieldCategory)
                                    @php
                                        $isMapped = ($currentMapping[$fieldCategory] ?? false) && ($currentMapping[$fieldCategory] === $category);
                                        $selected='';
                                    if ($isMapped) $selected='selected="selected"'
                                    @endphp
                                    <option value="{{$fieldCategory}}" {{ $selected }}> {{  $fieldCategory }}</option>
                                @endforeach
                            </select>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>

    $(document).ready(function (event) {
        $('select').selectpicker();
        $('.website-category').change(function () {
                let woocommerceCategory = $(this).data('woocommerce-category').trim();
                let fieldCategory = $(this).val().trim();

                var isUnmap= false;
                if (0 === fieldCategory.length){
                     isUnmap= true;

                    for (var csvField in mappingData) {
                        if (mappingData.hasOwnProperty(csvField)
                        && mappingData[csvField] === woocommerceCategory
                        ) {
                            delete mappingData[csvField];
                        }
                    }

                }
                else {
                    mappingData[fieldCategory] = woocommerceCategory;
                }


                let data ={
                  'category-mapping':mappingData,
                  'feed-identification':feedIdentification
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': LA.token
                    }
                });
                $.ajax({
                    method: "POST",
                    url: "{{ route('add-category-mapping') }}",
                    data:data
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


                        if (isUnmap){
                            toastr["warning"]("Category field unmapped.");
                        }
                        else{
                            toastr["success"]("Category field mapped.");
                        }

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
