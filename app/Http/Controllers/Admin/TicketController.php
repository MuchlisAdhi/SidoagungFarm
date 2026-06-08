<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestionType;
use App\Exports\TicketExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListTicketRequest;
use App\Http\Requests\Admin\UpdateTicketRequest;
use App\Services\Admin\TicketManagementService;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    public function __construct(
        protected TicketManagementService $ticketManagementService
    ) {}

    public function list(ListTicketRequest $request)
    {
        $result = $this->ticketManagementService->list($request->filters(), $this->allowedQuestionTypes());
        $tickets = $result['tickets'];
        $stats = $result['stats'];
        $filters = $result['filters'];

        return view('admin.ticket.ticket-list', compact('tickets', 'stats', 'filters'));
    }

    public function show($id)
    {
        $ticket = $this->ticketManagementService->findByEncryptedId((string) $id, $this->allowedQuestionTypes());
        if (! $ticket) {
            return redirect('/admin/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        return view('admin.ticket.ticket-show', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = $this->ticketManagementService->findByEncryptedId((string) $id, $this->allowedQuestionTypes());
        if (! $ticket) {
            return redirect('/admin/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        $warning = $this->ticketManagementService->update($ticket, $request->validated());

        return redirect('/admin/ticket/show/' . encrypt($ticket->id))
            ->with('success', 'Ticket berhasil diperbarui.' . $warning);
    }

    public function export(ListTicketRequest $request)
    {
        $fileName = 'Tickets_' . date('YmdHis') . '.xlsx';

        return Excel::download(
            new TicketExport($request->filters(), $this->allowedQuestionTypes()),
            $fileName
        );
    }

    protected function allowedQuestionTypes(): array
    {
        $user = auth()->user();
        if (! $user || ! method_exists($user, 'getRoleNames')) {
            return QuestionType::values();
        }

        return QuestionType::allowedForRoleNames($user->getRoleNames()->all());
    }
}
