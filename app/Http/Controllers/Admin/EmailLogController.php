<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListEmailLogRequest;
use App\Services\Admin\EmailLogService;

class EmailLogController extends Controller
{
    public function __construct(
        protected EmailLogService $emailLogService
    ) {}

    public function list(ListEmailLogRequest $request)
    {
        $result = $this->emailLogService->list($request->filters());
        $list = $result['list'];
        $filters = $result['filters'];

        return view('admin.email-log.list', compact('list', 'filters'));
    }

    public function show($id)
    {
        $row = $this->emailLogService->findByEncryptedId((string) $id);
        if (! $row) {
            return redirect('/admin/email-log')->with('error', 'Log email tidak ditemukan.');
        }

        return view('admin.email-log.detail', compact('row'));
    }
}
