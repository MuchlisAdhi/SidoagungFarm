<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveEmailConfigRequest;
use App\Models\EmailConfig;
use Illuminate\Support\Facades\DB;

class EmailConfigController extends Controller
{
    public function list()
    {
        return view('admin.email-config.list', [
            'list' => EmailConfig::orderByDesc('created_at')->get(),
        ]);
    }

    public function add()
    {
        return redirect('/wongelek/email-config/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Throwable $th) {
            return redirect('/wongelek/email-config')->with('error', 'Data tidak valid.');
        }

        return redirect('/wongelek/email-config/form?id=' . encrypt($id));
    }

    public function form()
    {
        $id = request()->query('id');
        $row = new EmailConfig();

        if ($id) {
            try {
                $row = EmailConfig::findOrFail(decrypt($id));
            } catch (\Throwable $th) {
                return redirect('/wongelek/email-config')->with('error', 'Data tidak ditemukan.');
            }
        }

        return view('admin.email-config.form', ['rs' => $row]);
    }

    public function save(SaveEmailConfigRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['formId'] ?? null;

        $payload = [
            'name' => $validated['formName'] ?? null,
            'host' => $validated['formHost'],
            'port' => $validated['formPort'],
            'username' => $validated['formUsername'],
            'encryption' => $validated['formEncryption'] ?? null,
            'from_address' => $validated['formFromAddress'],
            'from_name' => $validated['formFromName'],
            'report' => $validated['formReport'] ?? null,
            'is_active' => ($validated['formIsActive'] ?? null) === 'on',
        ];

        try {
            DB::transaction(function () use ($id, $payload, $validated) {
                if ($payload['is_active']) {
                    EmailConfig::where('is_active', true)->update(['is_active' => false]);
                }

                if ($id) {
                    $rowId = decrypt($id);
                    $row = EmailConfig::findOrFail($rowId);

                    if (! empty($validated['formPassword'])) {
                        $payload['password'] = $validated['formPassword'];
                    }

                    $row->update($payload);
                } else {
                    $payload['password'] = $validated['formPassword'] ?? '';
                    EmailConfig::create($payload);
                }
            });

            return redirect('/wongelek/email-config')->with('success', 'Konfigurasi email berhasil disimpan.');
        } catch (\Throwable $th) {
            report($th);

            return redirect('/wongelek/email-config')->with('error', 'Gagal menyimpan konfigurasi email.');
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            EmailConfig::where('id', $id)->delete();

            return redirect('/wongelek/email-config')->with('success', 'Konfigurasi email berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect('/wongelek/email-config')->with('error', 'Gagal menghapus konfigurasi email.');
        }
    }

    public function activate($id)
    {
        try {
            $id = decrypt($id);

            DB::transaction(function () use ($id) {
                EmailConfig::where('is_active', true)->update(['is_active' => false]);
                EmailConfig::where('id', $id)->update(['is_active' => true]);
            });

            return redirect('/wongelek/email-config')->with('success', 'Konfigurasi email aktif berhasil diubah.');
        } catch (\Throwable $th) {
            return redirect('/wongelek/email-config')->with('error', 'Gagal mengaktifkan konfigurasi email.');
        }
    }
}
