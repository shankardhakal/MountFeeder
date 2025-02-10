<div class="container-fluid">


    @if($feed->rules->count())
        <div class="row existing-rules">
            <h3 class="h3">Existing rules - {{ $feed->store_name }}</h3>
            <div class="col-md-3">
                <ul class="list-group">
                    @foreach($feed->rules as $rule)
                            <li class="list-group-item">
                                <span>{{$rule->description}}</span>
                                <button data-rule-id="{{$rule->id}}" class="btn btn-default btn-xs pull-right delete-existing-rule">
                                    <span class="text-danger glyphicon glyphicon-remove"></span>
                                </button>
                            </li>
                    @endforeach
                </ul>
            </div>
        </div>
@endif

    <div class="row">
        <h3 class="h3"> {{$feed->name}} Rules</h3>
        <button class="btn btn-sm btn-success add-rule">Add rule</button>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Csv Field</th>
                <th>Checker rule</th>
                <th>Match text</th>
                <th>Apply Rule</th>
                <th>Woocommerce Field</th>
                <th>Free value</th>
                <th scope="col">Rule Description</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody id="rules-table-body">
            <tr class="rule-row">
                <td>
                    <!--(rule-if-condition-check-field)-->
                    <select class="form-control csv-field">
                        @foreach($feedFields as $field)
                            <option value="csvProduct['{{$field}}']"> {{  $field }}</option>
                        @endforeach
                    </select>
                </td>

                <td>
                    <!--[rule-condition]!-->
                    <select class="form-control rule-condition">
                        <option value="contains(csv-field,'rule-condition-test-value')">contains</option>
                        <option value="notcontains(csv-field,'rule-condition-test-value')">does not
                            contain
                        </option>
                        <option value="equalsTo(csv-field,'rule-condition-test-value')">is equal to
                        </option>
                    </select>
                </td>
                <td>
                    <!--(rule-condition-test-value)!-->
                    <input class="form-control rule-condition-test-value" type="text">
                </td>
                <td>
                    <!--(rule-action)!-->
                     <select class="form-control rule-action">
                        <option data-rule="setAttribute" value="addAttributesFor('transform-value','woocommerce-field')">Set attribute</option>
                        <option data-rule="explodeAndSet" value="explodeAndSet('transform-value',csv-field,'woocommerce-field')">Explode and Assign</option>
                         <option data-rule="setSubCategory" value="addSubCategory('transform-value')">Set Sub Category</option>
                         <option data-rule ="skipProduct" value="skipProduct()">Skip Product</option>
                     </select>
                </td>

                <td>
                    <!--[rule-if-condition-true-act-upon-field]!-->
                    <select class="form-control woocommerce-field" style="margin-left: 5px">
                        @foreach($woocommerceProductFields as $internalField=>$woocommerceField)
                            <option value="product.{{$internalField}}">{{$woocommerceField}}</option>
                        @endforeach
                    </select>
                </td>
                    <!--(transform-value)!-->
                   <td>  <input type="text" class="form-control transform-value form-inline"><span class="transform-value-info"></span>

                   </td>
                <td><input class="form-control rule-description" type="text" placeholder="Rule description"></td>
                <td>
                    <button style="margin-left: 4px" class="btn btn-sm btn-success save-rule">Save rule</button>
                    <button style="visibility: hidden" class="btn btn-sm btn-danger remove-rule">Delete rule</button>
                </td>
            <tr>
            </tbody>
        </table>

    </div>
</div>


<script>
    $('.rule-action').change(function (event) {

        let ruleName = $('.rule-action :selected').data('rule');

        $(this).closest('tr').find('.woocommerce-field').show();
        $(this).closest('tr').find('.transform-value').show();

        if ('setSubCategory'===ruleName){
            $(this).closest('tr').find('.woocommerce-field').hide();
        }
        if('skipProduct' === ruleName){
            $(this).closest('tr').find('.woocommerce-field').hide();
            $(this).closest('tr').find('.transform-value').hide();
        }

    });

    $('.save-rule').click(function (event) {
        <!--[rule-if-condition-true-act-upon-field]!-->
        <!--[rule-condition]!-->
        <!--(rule-condition-test-value)!-->
        <!--(rule-action)!-->
        <!--(rule-if-condition-check-field)-->
        <!--(transform-value)!-->

        let activeRow = $(this).closest('tr');
        let csvProduct = activeRow.find('.csv-field').val();
        let checkCondition = activeRow.find('.rule-condition').val();
        let conditionTestValue = activeRow.find('.rule-condition-test-value').val();
        let woocommerceField = activeRow.find('.woocommerce-field').val();

        let applyRule = activeRow.find('.rule-action').val();
        let freeValue = activeRow.find('.transform-value').val();
        let ruleDescription = activeRow.find('.rule-description').val();

        let createdRule = checkCondition
                .replace('csv-field', csvProduct)
                .replace('rule-condition-test-value', conditionTestValue) +
            ' ? ' +
            applyRule
                .replace('woocommerce-field', woocommerceField)
                .replace('csv-field', csvProduct)
                .replace('transform-value', freeValue);

        if ("addAttributesFor('transform-value','woocommerce-field')" === applyRule
        || "explodeAndSet('transform-value',csv-field,'woocommerce-field')" === applyRule
        ){
            createdRule= createdRule.replace('product.','');
        }
        console.log(createdRule);

        let rawRule = checkCondition + ' ? ' + applyRule;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': LA.token
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('add-feed-rules') }}",

            data: {
                feed_id: '{{ $feed->id }}',
                syntax: createdRule,
                description: ruleDescription,
                rawRule: rawRule
            }
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

    });

    $(document).on('click', '.add-rule', function (event) {
        $('#rules-table-body tr:first').clone().appendTo('#rules-table-body');
        $('#rules-table-body tr:last').find('.remove-rule').css({visibility: 'visible'});
    });

    $(document).on('click', '.remove-rule', function (event) {

        $(this).closest('tr').remove();
    });

    $(document).on('click','.delete-existing-rule',function (event) {

        let currentItem = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': LA.token
            }
        });
        let ruleId = $(this).data('rule-id');
        $.ajax({
            method: "DELETE",
            url: "{{ route('remove-feed-rules') }}",

            data: {
                feed_id: '{{ $feed->id }}',
                id: ruleId
            }
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
                toastr["success"]("Rule deleted!");
                currentItem.closest('li').remove();


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
            toastr["error"]("An error occurred while deleting rule.")
        });


    });

</script>
