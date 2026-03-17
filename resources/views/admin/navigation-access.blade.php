@extends('admin.master')
@section('page')
    Navigation Access
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Access Name *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="formAccessName" maxlength="255">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="formDescription" rows="3" maxlength="255"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-lg-offset-2">
                    <div class="form-group">
                        <label>Existing</label>
                        <select class="form-control" id="formExistingAccess">
                            <option value="">Select Navigation Access</option>
                            @foreach ($existingAccesses as $access)
                                <option value="{{ $access['encrypted_id'] }}">{{ $access['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-bottom: 10px;">
                <div class="col-lg-12 text-right">
                    <button type="button" class="btn btn-default" id="btnClearAccess">Clear</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteAccess">Delete</button>
                    <button type="button" class="btn btn-primary" id="btnSaveAccess">Save</button>
                </div>
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">All</th>
                        <th>Navigation Name</th>
                        @foreach ($actions as $action)
                            <th style="width: 90px; text-transform: capitalize;">{{ $action }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($navigationRows as $row)
                        @php
                            $rowClass = str_replace(['.', '-'], '_', $row['key']);
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-select" data-row="{{ $rowClass }}">
                            </td>
                            <td>
                                @for ($i = 0; $i < $row['level']; $i++)
                                    <span style="display:inline-block; width: 18px;"></span>
                                @endfor
                                <strong>{{ $row['title'] }}</strong>
                            </td>
                            @foreach ($actions as $action)
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="permission-check row-{{ $rowClass }}"
                                        data-row="{{ $rowClass }}"
                                        value="{{ 'nav.' . strtolower($row['key']) . '.' . $action }}">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let selectedAccessId = "";

        $(function () {
            $("#btnClearAccess").on("click", function () {
                clearForm(true);
            });

            $("#formExistingAccess").on("change", function () {
                const accessId = $(this).val();
                if (!accessId) {
                    clearForm(false);
                    return;
                }

                $.get(`{{ url('/admin/navigation-access/get') }}/${accessId}`, function (res) {
                    if (res.code !== 200) {
                        showError(res.msg || "Gagal memuat data navigation access.");
                        clearForm(true);
                        return;
                    }

                    selectedAccessId = res.data.id;
                    $("#formAccessName").val(res.data.name || "");
                    $("#formDescription").val(res.data.description || "");
                    $(".permission-check, .row-select").prop("checked", false);

                    (res.data.permissions || []).forEach(function (permission) {
                        $(`.permission-check[value="${permission}"]`).prop("checked", true);
                    });

                    refreshRowSelection();
                }).fail(function () {
                    showError("Gagal memuat data navigation access.");
                    clearForm(true);
                });
            });

            $("#btnSaveAccess").on("click", function () {
                const access_name = ($("#formAccessName").val() || "").trim();
                const description = ($("#formDescription").val() || "").trim();
                const permissions = $(".permission-check:checked").map(function () {
                    return $(this).val();
                }).get();

                if (!AdminSubmit.start("#btnSaveAccess", "Menyimpan...")) {
                    return;
                }

                $.post(`{{ url('/admin/navigation-access/save') }}`, {
                    access_id: selectedAccessId,
                    access_name: access_name,
                    description: description,
                    permissions: permissions,
                }).done(function (res) {
                    if (res.code !== 200) {
                        AdminSubmit.stop("#btnSaveAccess");
                        showError(res.msg || "Gagal menyimpan navigation access.");
                        return;
                    }
                    window.location.reload();
                }).fail(function () {
                    AdminSubmit.stop("#btnSaveAccess");
                    showError("Gagal menyimpan navigation access.");
                });
            });

            $("#btnDeleteAccess").on("click", function () {
                if (!selectedAccessId) {
                    showError("Pilih navigation access yang ingin dihapus.");
                    return;
                }

                if (!confirm("Yakin ingin menghapus navigation access ini?")) {
                    return;
                }

                $.post(`{{ url('/admin/navigation-access/delete') }}`, {
                    access_id: selectedAccessId,
                }).done(function (res) {
                    if (res.code !== 200) {
                        showError(res.msg || "Gagal menghapus navigation access.");
                        return;
                    }
                    window.location.reload();
                }).fail(function () {
                    showError("Gagal menghapus navigation access.");
                });
            });

            $(".row-select").on("change", function () {
                const row = $(this).data("row");
                $(`.permission-check.row-${row}`).prop("checked", $(this).is(":checked"));
            });

            $(".permission-check").on("change", function () {
                const row = $(this).data("row");
                syncRowSelection(row);
            });
        });

        function clearForm(resetExistingSelect = false) {
            selectedAccessId = "";
            $("#formAccessName, #formDescription").val("");
            $(".permission-check, .row-select").prop("checked", false);

            if (resetExistingSelect) {
                $("#formExistingAccess").val("");
            }
        }

        function syncRowSelection(row) {
            const checks = $(`.permission-check.row-${row}`);
            const checked = checks.filter(":checked").length;
            $(`.row-select[data-row='${row}']`).prop("checked", checked > 0 && checked === checks.length);
        }

        function refreshRowSelection() {
            $(".row-select").each(function () {
                syncRowSelection($(this).data("row"));
            });
        }

        function showError(message) {
            $.toast({
                heading: "Error",
                text: message,
                showHideTransition: "fade",
                position: "bottom-right",
                icon: "error",
            });
        }
    </script>
@endsection
