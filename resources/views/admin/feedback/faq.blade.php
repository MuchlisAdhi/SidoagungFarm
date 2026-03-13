@extends('admin.master')
@section('page')
    Pertanyaan Customer
@endsection

@php
    $categories = [
        'pakanternak'   => "Pakan Ternak",
        'bibitayam'     => "Bibit Ayam Umur Sehari",
        'ayamhidup'     => "Ayam Hidup",
        'ayampotong'    => "Ayam Potong",
        'makananolahan' => "Makanan Olahan"
    ];
@endphp

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="tblFaq" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Name</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 12%;">Ticket</th>
                        <th style="width: 20%;">Phone</th>
                        <th>Description</th>
                        <th style="width: 20%;">Produk</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 5%;">Show</th>
                    </tr>
                </thead>
                <tbody id="tblBodyFaq">
                    @foreach ($list as $u)
                        @php
                            $cat = $categories[$u->category] ?? null;
                            $category = $cat ? ' (' . $categories[$u->category] . ')' : $u->category;
                            $status = $u->ticket_status ?? ($u->replied ? 'responded' : 'new');
                        @endphp
                        <tr>
                            <td></td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->ticket_no ?: '-' }}</td>
                            <td>{{ $u->phone }}</td>
                            <td>{{ substr($u->description, 0, 100) }}</td>
                            <td>{{ $u->title . $category }}</td>
                            <td>
                                @if($status === 'responded')
                                    <span class="text-success">Responded</span>
                                @elseif($status === 'open')
                                    <span class="text-primary">Open</span>
                                @else
                                    <span class="text-warning">New</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Show"
                                    onclick="viewSelected('{{ encrypt($u->id) }}', '{{$u->mode}}')">
                                    <i class="fa fa-eye" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalFormFaq">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Question</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="formName" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="formName" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formEmail" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="formEmail" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formPhone" class="col-sm-3 control-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="formPhone" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formTicketNo" class="col-sm-3 control-label">Ticket</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="formTicketNo" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formProduct" class="col-sm-3 control-label">Product</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="formProduct" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formDescription" class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control input-sm" id="formDescription"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formResponse" class="col-sm-3 control-label">Response</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control input-sm" id="formResponse" placeholder="Tulis jawaban untuk customer..."></textarea>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" id="btnReplied" class="btn btn-success btn-sm">Send Response</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('script')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        var categories = {!! json_encode($categories) !!};
        var selected = "";
        var mode = "";
        $(document).ready(function() {
            $("#tblBodyFaq").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblFaq").DataTable();
            $("#btnReplied").click(function(){
                replied();
            });
        });

        function clearForm() {
            selected = "";
            $("#formImage, #formTitle").val("");
            $('#formPublish').prop('checked', false);
        }

        function replied() {
            const id = selected;
            const md = mode;
            const response = $("#formResponse").val().trim();

            if (response === "") {
                $.toast({
                    heading: 'Error',
                    text: 'Response wajib diisi.',
                    showHideTransition: 'fade',
                    position: 'bottom-right',
                    icon: 'error'
                });
                return;
            }

            if (!AdminSubmit.start("#btnReplied", "Menyimpan...")) {
                return;
            }

            $.post(`{{ url('/wongelek/feedback/pertanyaan/replied') }}`, {id, mode: md, response}, function(res) {
                $("#modalFormFaq").modal("hide");
                if (res.msg) {
                    $.toast({
                        heading: 'Success',
                        text: res.msg,
                        showHideTransition: 'slide',
                        position: 'bottom-right',
                        icon: 'success'
                    });
                }
                setTimeout(function(){
                    window.location.reload();
                }, 500);
            }).fail(function(xhr) {
                AdminSubmit.stop("#btnReplied");
                const msg = xhr.responseJSON?.msg || 'Gagal mengirim jawaban.';
                $.toast({
                    heading: 'Error',
                    text: msg,
                    showHideTransition: 'fade',
                    position: 'bottom-right',
                    icon: 'error'
                });
            });
        }

        function viewSelected(id, md)
        {
            selected = id;
            mode = md;
            $.get(`{{ url('/wongelek/feedback/pertanyaan/get') }}?id=${selected}&mode=${mode}` , function(res) {
                const {code, msg, data} = res;
                const {id, name, email, phone, ticket_no, replied, description, response_message, title, category} = data;
                const cat = categories[category] ?? null;
                const product = cat ? `${title} (${cat})` : category;
                $("#formName").val(name);
                $("#formEmail").val(email);
                $("#formPhone").val(phone);
                $("#formTicketNo").val(ticket_no || "-");
                $("#formProduct").val(product);
                $("#formDescription").val(description);
                $("#formResponse").val(response_message || "");
                $("#modalFormFaq").modal("show")
            });
        }
    </script>
@endsection
