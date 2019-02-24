<?php

namespace App\UseCases\Tickets;

use App\Entity\Ticket\Ticket;
use App\Http\Requests\Ticket\CreateRequest;
use App\Http\Requests\Ticket\EditRequest;
use App\Http\Requests\Ticket\MessageRequest;

class TicketService
{
    /**
     * @param int $userId
     * @param CreateRequest $request
     * @return Ticket
     */
    public function create(int $userId, CreateRequest $request): Ticket
    {
        return Ticket::new($userId, $request['subject'], $request['content']);
    }

    /**
     * @param int $id
     * @param EditRequest $request
     */
    public function edit(int $id, EditRequest $request)
    {
        $ticket = $this->getTicket($id);
        $ticket->edit(
            $request['subject'],
            $request['content']
        );
    }

    /**
     * @param int $userId
     * @param int $id
     * @param MessageRequest $request
     */
    public function message(int $userId, int $id, MessageRequest $request)
    {
        $ticket = $this->getTicket($id);
        $ticket->addMessage($userId, $request['message']);
    }

    /**
     * @param int $userId
     * @param int $id
     */
    public function approve(int $userId, int $id)
    {
        $ticket = $this->getTicket($id);
        $ticket->approve($userId);
    }

    /**
     * @param int $userId
     * @param int $id
     */
    public function close(int $userId, int $id)
    {
        $ticket = $this->getTicket($id);
        $ticket->close($userId);
    }

    /**
     * @param int $userId
     * @param int $id
     */
    public function reopen(int $userId, int $id)
    {
        $ticket = $this->getTicket($id);
        $ticket->reopen($userId);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function removeByOwner(int $id)
    {
        $ticket = $this->getTicket($id);
        if (!$ticket->canBeRemoved()) {
            throw new \DomainException('Unable to remove active ticket');
        }
        $ticket->delete();
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function removeByAdmin(int $id)
    {
        $ticket = $this->getTicket($id);
        $ticket->delete();
    }

    /**
     * @param $id
     * @return Ticket
     */
    private function getTicket($id): Ticket
    {
        return Ticket::query()->findOrFail($id);
    }
}