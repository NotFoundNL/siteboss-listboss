<?php

declare(strict_types=1);

namespace NotFound\ListBoss\Helpers;

class Job
{
    use Api;

    private ?object $statusInfo = null;

    public function __construct(
        private ?int $id = null,
        private ?string $subject = null,
        private ?string $content = null,
        private ?JobStatus $status = null
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function subject(): ?string
    {
        return $this->subject;
    }

    public function started(): bool
    {
        if ($this->status === null) {
            $this->status = $this->status();
        }

        return $this->status->started();
    }

    public function save(): void
    {
        $params = ['subject' => $this->subject, 'content' => $this->content];
        if ($this->id === null) {
            // Create new
            $result = $this->call('POST', '', $params, updateSelf: true);
        } else {
            // Update
            $result = $this->call('PUT', (string) $this->id, $params);
        }
    }

    public function start(array $list)
    {
        $parameters = ['recipients' => $list];

        return $this->call(method: 'POST', endPoint: $this->id.'/start', params: $parameters, updateSelf: true);
    }

    public function result(string $sort = null, int $page = 1, string $direction = 'desc'): object
    {
        $params = [];
        if ($sort !== null) {
            $params['sort'] = $sort;
            $params['direction'] = $direction;
            $params['page'] = $page;
        }

        return $this->call(
            endPoint: $this->id.'/result',
            params: $params,
            updateSelf: true
        );
    }

    public function status(): ?JobStatus
    {
        $status = $this->call(endPoint: $this->id.'/status', updateSelf: true);

        $this->statusInfo = $status;

        return JobStatus::tryFromName($status->status);
    }

    public function statusInfo(): ?object
    {
        if ($this->statusInfo === null) {
            $this->status();
        }

        return $this->statusInfo;
    }

    public function preview(string $email, array $params = []): bool
    {
        if ($this->id === null) {
            return false;
        }
        $params = ['recipient' => $email, 'parameters' => (object) $params];

        $this->call('POST', $this->id.'/preview', $params, updateSelf: true);

        return true;
    }
}
