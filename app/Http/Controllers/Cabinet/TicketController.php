<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Ticket\Ticket;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateRequest;
use App\Http\Requests\Ticket\MessageRequest;
use App\UseCases\Tickets\TicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tickets = Ticket::forUser(Auth::user())->orderByDesc('updated_at')->paginate(20);
        return view('cabinet.tickets.index', compact('tickets'));
    }

    /**
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        return view('cabinet.tickets.show', compact('ticket'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('cabinet.tickets.create');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $ticket = $this->service->create(Auth::id(), $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.tickets.show', $ticket);
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
     * @throws \Exception
     */
    public function destroy(Ticket $ticket)
    {
        $this->checkAccess($ticket);
        try {
            $this->service->removeByOwner($ticket->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.favorites.index');
    }

    /**
     * @param Ticket $ticket
     */
    private function checkAccess(Ticket $ticket)
    {
        if (!Gate::allows('manage-own-ticket', $ticket)) {
            abort(403);
        }
    }
}