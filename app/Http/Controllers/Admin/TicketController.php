<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Ticket\Status;
use App\Entity\Ticket\Ticket;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\EditRequest;
use App\Http\Requests\Ticket\MessageRequest;
use App\Repositories\TicketRepository;
use App\UseCases\Tickets\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    private $service;

    /**
     * TicketController constructor.
     * @param TicketService $service
     */
    public function __construct(TicketService $service)
    {
        $this->service = $service;
        $this->middleware('can:manage-tickets');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tickets = TicketRepository::getTickets($request->all());
        $statuses = Status::statusesList();
        return view('admin.tickets.index', compact('tickets', 'statuses'));
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    /**
     * @param EditRequest $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, Ticket $ticket)
    {
        try {
            $this->service->edit($ticket->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.tickets.show', $ticket);
    }

    /**
     * @param MessageRequest $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function message(MessageRequest $request, Ticket $ticket)
    {
        try {
            $this->service->message(Auth::id(), $ticket->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.tickets.show', $ticket);
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Ticket $ticket)
    {
        try {
            $this->service->approve(Auth::id(), $ticket->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.tickets.show', $ticket);
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(Ticket $ticket)
    {
        try {
            $this->service->close(Auth::id(), $ticket->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.tickets.show', $ticket);
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reopen(Ticket $ticket)
    {
        try {
            $this->service->reopen(Auth::id(), $ticket->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.tickets.show', $ticket);
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Ticket $ticket)
    {
        try {
            $this->service->removeByAdmin($ticket->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.tickets.index');
    }
}