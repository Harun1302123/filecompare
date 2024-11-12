<div id="paymentInfo" class="collapse">
    <div class="panel panel-success">
        <div class="panel-heading">
            <strong>Payment Information</strong>
        </div>

        <div class="panel-body">
            <div style="margin:0;" class="panel panel-info">
                <div class="panel-heading">
                    Service Fee Payment
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_contact_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact email</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_contact_email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact phone</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_contact_phone }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact address</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_contact_address }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Pay amount</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_pay_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">VAT on pay amount</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_vat_on_pay_amount }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label"> Transaction charge</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_transaction_charge_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">VAT on transaction charge</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_vat_on_transaction_charge }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Total amount</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_total_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Payment status</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    @if($appInfo->sfp_payment_status == 0)
                                        <span class="label label-warning">Pending</span>
                                    @elseif($appInfo->sfp_payment_status == -1)
                                        <span class="label label-info">In-progress</span>
                                    @elseif($appInfo->sfp_payment_status == 1)
                                        <span class="label label-success">Paid</span>
                                    @elseif($appInfo->sfp_payment_status == 2)
                                        <span class="label label-danger">Exception</span>
                                    @elseif($appInfo->sfp_payment_status == 3)
                                        <span class="label label-warning">Waiting for payment confirmation</span>
                                    @else
                                        <span class="label label-warning">Invalid status</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Payment mode</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $appInfo->sfp_pay_mode }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="pull-left">
                        <a href="/spg/{{ $appInfo->sfp_pay_mode_code == 'A01' ? 'counter-' : '' }}payment-voucher/{{ Encryption::encodeId($appInfo->sf_payment_id)}}"
                           target="_blank" class="btn btn-info btn-sm">
                            <strong> Download voucher</strong>
                        </a>
                    </div>

                    {{-- Counter payment, 3 = Waiting for Payment Confirmation--}}
                    @if(($appInfo->sfp_payment_status == 3 or $appInfo->status_id == 3) && empty($appInfo->gf_payment_id) && in_array(Auth::user()->user_type, ['5x505']))
                        <div class="pull-right">
                            <a href="/spg/counter-payment-check/{{ Encryption::encodeId($appInfo->sf_payment_id)}}/{{Encryption::encodeId(0)}}"
                               class="btn btn-danger btn-sm">
                                <strong> Cancel payment request</strong>
                            </a>
                            <a href="/spg/counter-payment-check/{{ Encryption::encodeId($appInfo->sf_payment_id)}}/{{Encryption::encodeId(1)}}"
                               class="btn btn-primary btn-sm">
                                <strong> Confirm payment request</strong>
                            </a>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        @if(!empty($appInfo->gf_payment_id) && !in_array($appInfo->status_id, [15, 32]))
            <div class="panel-body">
                <div style="margin:0;" class="panel panel-info">
                    <div class="panel-heading">
                        Government Fee Payment
                    </div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_contact_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact email</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_contact_email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label"> Contact phone</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_contact_phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact address</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_contact_address }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Pay amount</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_pay_amount }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">VAT on pay amount</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_vat_on_pay_amount }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Transaction charge</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_transaction_charge_amount }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">VAT on transaction charge</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_vat_on_transaction_charge }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Total amount</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_total_amount }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Payment status</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        @if($appInfo->gfp_payment_status == 0)
                                            <span class="label label-warning">Pending</span>
                                        @elseif($appInfo->gfp_payment_status == -1)
                                            <span class="label label-info">In-progress</span>
                                        @elseif($appInfo->gfp_payment_status == 1)
                                            <span class="label label-success">Paid</span>
                                        @elseif($appInfo->gfp_payment_status == 2)
                                            <span class="label label-danger">Exception</span>
                                        @elseif($appInfo->gfp_payment_status == 3)
                                            <span class="label label-warning">Waiting for payment confirmation</span>
                                        @else
                                            <span class="label label-warning">Invalid status</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Payment mode</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->gfp_pay_mode }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <div class="pull-left">
                            <a href="/spg/{{ $appInfo->gfp_pay_mode_code == 'A01' ? 'counter-' : '' }}payment-voucher/{{ Encryption::encodeId($appInfo->gf_payment_id)}}"
                               target="_blank" class="btn btn-info btn-sm">
                                <strong> Download voucher</strong>
                            </a>
                        </div>

                        {{-- Counter payment, 3 = Waiting for Payment Confirmation--}}
                        @if(($appInfo->gfp_payment_status == 3 or $appInfo->status_id == 3) && !empty($appInfo->gf_payment_id) && in_array(Auth::user()->user_type, ['5x505']))
                            <div class="pull-right">
                                <a href="/spg/counter-payment-check/{{ Encryption::encodeId($appInfo->gf_payment_id)}}/{{Encryption::encodeId(0)}}"
                                   class="btn btn-danger btn-sm">
                                    <strong> Cancel payment request</strong>
                                </a>
                                <a href="/spg/counter-payment-check/{{ Encryption::encodeId($appInfo->gf_payment_id)}}/{{Encryption::encodeId(1)}}"
                                   class="btn btn-primary btn-sm">
                                    <strong> Confirm payment request</strong>
                                </a>
                            </div>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        @elseif(!empty($appInfo->gf_manual_payment_id) && !in_array($appInfo->status_id, [15, 32]))
            <div class="panel-body">
                <div style="margin:0;" class="panel panel-info">
                    <div class="panel-heading">
                        Government Fee Payment
                    </div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_contact_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact email</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_contact_email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label"> Contact phone</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_contact_phone }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Contact address</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_contact_address }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Bank reference number</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgf_ref_tran_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Pay amount</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_pay_amount }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Bank charge</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_bank_charge }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">VAT/ TAX</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_vat_amount }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Total amount</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        {{ $appInfo->mgfp_total_amount }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="v_label">Payment status</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7">
                                        @if($appInfo->mgfp_payment_status == 0)
                                            <span class="label label-warning">Pending</span>
                                        @elseif($appInfo->mgfp_payment_status == -1)
                                            <span class="label label-info">In-progress</span>
                                        @elseif($appInfo->mgfp_payment_status == 1)
                                            <span class="label label-success">Paid</span>
                                        @elseif($appInfo->mgfp_payment_status == 2)
                                            <span class="label label-danger">Exception</span>
                                        @elseif($appInfo->mgfp_payment_status == 3)
                                            <span class="label label-warning">Waiting for payment confirmation</span>
                                        @else
                                            <span class="label label-warning">Invalid status</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <div class="pull-left">
                            <a href="{{ URL::to('/uploads/'.$appInfo->mgf_invoice_copy) }}"
                               target="_blank" class="btn btn-info btn-sm">
                                <strong> Download invoice copy</strong>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>