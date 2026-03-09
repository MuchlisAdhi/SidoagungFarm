@extends('admin.master')
@section('page')
    Pelamar => {{$career->position}}
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="tblCareer" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Name</th>
                        <th style="width: 15%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th>Education</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 10%;">Show</th>
                    </tr>
                </thead>
                <tbody id="tblBodyCareer">
                    @foreach ($list as $l)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $l->firstname }} {{$l->lastname}}</td>
                            <td>{{ $l->email }}</td>
                            <td>{{ $l->phone }}</td>
                            <td>{{ $l->lasteducation }} {{$l->major}}</td>
                            <td>
                                @if($l->isapprove)
                                    <span class="text-success text-bold">Approve</span>
                                @else
                                    @if($l->rejectreason == "")
                                        <span class="text-warning text-bold">New</span>
                                    @else
                                        <span class="text-danger text-bold">Reject</span>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="show('{{ encrypt($l->id) }}')">
                                    <i class="fa fa-eye" style="font-size: 20px;"></i>
                                </a>
                                &nbsp;
                                <a href="{{ route('main.getResource', ['id' => $l->cvid]) }}" target="blank" class="text-success" title="Show CV">
                                    <i class="fa fa-download" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group form-group-sm">
                                        <label for="formFullName" class="col-sm-3 control-label">Full Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formFullName">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formEmail" class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formEmail">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formPhone" class="col-sm-3 control-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formPhone">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formBod" class="col-sm-3 control-label">BOD</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formBod">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formEducation" class="col-sm-3 control-label">Last Education</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formEducation">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group form-group-sm">
                                        <label for="formEducation" class="col-sm-3 control-label">Experienced</label>
                                        <div class="col-sm-9">
                                            <span id="experienced" class="text-bold"></span>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formCurrentSalary" class="col-sm-3 control-label">Current Salary</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formCurrentSalary">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formExpectationSalary" class="col-sm-3 control-label">Expectation Salary</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formExpectationSalary">
                                        </div>
                                    </div>

                                    <hr />

                                    <div class="form-group form-group-sm">
                                        <label for="formStatus" class="col-sm-3 control-label">Status</label>
                                        <div class="col-sm-9">
                                            <span id="formStatus" class="text-bold"></span>
                                        </div>
                                    </div>

                                    <div class="form-group form-group-sm" id="reasonBlock">
                                        <label for="formRejectReason" class="col-sm-3 control-label">Reject Reason</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control input-sm" id="formRejectReason"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr />
                                    <h5 class="text-bold text-success">Experiences</h5>
                                    <table id="tblExpList" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">No.</th>
                                                <th>Company Name</th>
                                                <th>Industri</th>
                                                <th>Position</th>
                                                <th style="width: 10px">Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblExpBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="btnReject">Reject</button>
                    <button type="button" class="btn btn-primary" id="btnApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalGiveReason">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Reject Reason</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group form-group-sm">
                            <div class="col-sm-12">
                                <textarea class="form-control input-sm" id="formGiveReason"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitReason">Save</button>
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
        var selected;
        $(function() {
            $("#tblCareer").DataTable();

            $("#btnApprove").click(function(){
                $.get("{{ url('/wongelek/feedback/karir/approveApp') }}/" + selected, function(r) {
                    window.location.reload()
                });
            });

            $("#btnReject").click(function(){
                $("#formGiveReason").val("")
                $("#formGiveReason").removeClass("has-error");
                $("#modalDetail").modal("hide")
                $("#modalGiveReason").modal("show")
            })

            $("#btnSubmitReason").click(function(){
                let reason = $("#formGiveReason")
                if(reason.val() == "")
                {
                    reason.parents(".form-group").addClass("has-error");
                    return;
                }

                let form = new FormData();
                form.append('id', selected);
                form.append('reason', reason.val());

                $.ajax({
                    url: "{{ url('/wongelek/feedback/karir/rejectApp') }}",
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(res) {
                        if (res.code == 200) {
                            window.location.reload();
                        } else {
                            $.toast({
                                heading: 'Error',
                                text: res.msg,
                                showHideTransition: 'fade',
                                position: 'bottom-right',
                                icon: 'error'
                            })
                        }
                    },
                    error: function(e) {
                        $.toast({
                            heading: 'Error',
                            text: "Gagal, Hubungi Administrator.",
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                });
            })
        })

        function show(id) {
            selected = id;
            $.get("{{ url('/wongelek/feedback/karir/getApplicant') }}/" + id, function(r) {
                if(r.code == 200)
                {
                    const {firstname, lastname, email, phone, bod, lasteducation, major, currentsalary, expectationsalary, isexperience, experiencelist, isapprove, rejectreason, cvid  } = r.data

                    $("#formFullName").val(`${firstname} ${lastname}`)
                    $("#formEmail").val(email)
                    $("#formPhone").val(phone)
                    $("#formBod").val(bod)
                    $("#formEducation").val(`${lasteducation} ${major}`)
                    
                    $("#experienced").text(isexperience == "1" ? "Yes" : "No")
                    $("#formCurrentSalary").val(formatNumber(currentsalary))
                    $("#formExpectationSalary").val(formatNumber(expectationsalary))

                    if(isapprove == "1")
                    {
                        $("#formStatus").text("Approve");
                        $("#reasonBlock").hide()
                    }else{
                        if(rejectreason != null && rejectreason != "")
                        {
                            $("#formStatus").text("Reject");
                            $("#formRejectReason").val(rejectreason)
                            $("#reasonBlock").show()
                        }else{
                            $("#formStatus").text("New");
                            $("#reasonBlock").hide()
                        }
                    }

                    const expList = JSON.parse(experiencelist);
                    let html = ``;
                    expList.map(function(e, i){
                        html += `<tr><td>${i+1}</td><td>${e.companyName}</td><td>${e.industri}</td><td>${e.position}</td><td>${e.lengthOfWork}</td></tr>`
                    })
                    $("#tblExpBody").html(html)

                    $("#modalDetail").modal("show")
                }
            });
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>
@endsection
