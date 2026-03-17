<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListTicketRequest;
use App\Http\Requests\Admin\UpdateTicketRequest;
use App\Services\Admin\TicketManagementService;

class TicketController extends Controller
{
    public function __construct(
        protected TicketManagementService $ticketManagementService
    ) {}

    public function list(ListTicketRequest $request)
    {
        $result = $this->ticketManagementService->list($request->filters());
        $tickets = $result['tickets'];
        $stats = $result['stats'];
        $filters = $result['filters'];

        return view('admin.ticket.ticket-list', compact('tickets', 'stats', 'filters'));
    }

    public function show($id)
    {
        $ticket = $this->ticketManagementService->findByEncryptedId((string) $id);
        if (! $ticket) {
            return redirect('/wongelek/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        return view('admin.ticket.ticket-show', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = $this->ticketManagementService->findByEncryptedId((string) $id);
        if (! $ticket) {
            return redirect('/wongelek/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        $warning = $this->ticketManagementService->update($ticket, $request->validated());

        return redirect('/wongelek/ticket/show/' . encrypt($ticket->id))
            ->with('success', 'Ticket berhasil diperbarui.' . $warning);
    }
}
