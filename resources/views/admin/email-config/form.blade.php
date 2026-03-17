@extends('admin.master')
@section('page')
    Form Email Config
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form id="frmEmailConfig" class="form-horizontal" method="post" action="{{ url('/admin/email-config/save') }}">
                {{ csrf_field() }}
                @if ($rs->id)
                    <input type="hidden" name="formId" value="{{ encrypt($rs->id) }}">
                @endif

                <div class="form-group" style="margin-top: 15px;">
                    <label for="formName" class="col-lg-2 control-label">Name</label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formName" name="formName" maxlength="100"
                            value="{{ old('formName') ?? $rs->name }}" placeholder="Optional label">
                    </div>
                </div>

                <div class="form-group">
                    <label for="formHost" class="col-lg-2 control-label">SMTP Host <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formHost" name="formHost" maxlength="255"
                            value="{{ old('formHost') ?? $rs->host }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formPort" class="col-lg-2 control-label">SMTP Port <span class="text-danger">*</span></label>
                    <div class="col-lg-2">
                        <input type="number" class="form-control input-sm" id="formPort" name="formPort" min="1"
                            value="{{ old('formPort') ?? ($rs->port ?: 587) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formUsername" class="col-lg-2 control-label">SMTP Username <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formUsername" name="formUsername" maxlength="255"
                            value="{{ old('formUsername') ?? $rs->username }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formPassword" class="col-lg-2 control-label">
                        SMTP Password
                        @if (! $rs->id)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <div class="col-lg-4">
                        <input type="password" class="form-control input-sm" id="formPassword" name="formPassword" maxlength="255"
                            @if (! $rs->id) required @endif>
                        @if ($rs->id)
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="formEncryption" class="col-lg-2 control-label">Encryption</label>
                    <div class="col-lg-2">
                        <select class="form-control input-sm" id="formEncryption" name="formEncryption">
                            @php $enc = old('formEncryption') ?? $rs->encryption; @endphp
                            <option value="" @selected($enc == '')>None</option>
                            <option value="tls" @selected($enc == 'tls')>TLS</option>
                            <option value="ssl" @selected($enc == 'ssl')>SSL</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formFromAddress" class="col-lg-2 control-label">From Address <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="email" class="form-control input-sm" id="formFromAddress" name="formFromAddress" maxlength="255"
                            value="{{ old('formFromAddress') ?? $rs->from_address }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formFromName" class="col-lg-2 control-label">From Name <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formFromName" name="formFromName" maxlength="255"
                            value="{{ old('formFromName') ?? $rs->from_name }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formReport" class="col-lg-2 control-label">Admin Recipient Email</label>
                    <div class="col-lg-4">
                        <input type="email" class="form-control input-sm" id="formReport" name="formReport" maxlength="255"
                            value="{{ old('formReport') ?? $rs->report }}" placeholder="contoh: admin@sidoagungfarm.com">
                        <small class="text-muted">Dipakai untuk notifikasi ticket baru ke admin.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formIsActive" class="col-lg-2 control-label">&nbsp;</label>
                    <div class="col-lg-3">
                        <input type="checkbox" id="formIsActive" name="formIsActive" @checked(old('formIsActive') == 'on' || $rs->is_active)>
                        <label for="formIsActive">&nbsp;Set as active SMTP</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-right">
                        <button id="btnBack" type="button" class="btn btn-sm btn-default">Kembali</button>
                        <button id="btnSave" type="submit" class="btn btn-sm btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $("#btnBack").click(function() {
                window.location.href = "{{ url('/admin/email-config') }}";
            });
        });
    </script>
@endsection
