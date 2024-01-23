<?php

declare(strict_types=1);

namespace NotFound\ListBoss\Helpers;

class Job
{
    use Api;

    private ?string $subject = null;

    private ?string $content = null;

    public function __construct(private ?int $id = null)
    {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function subject(): ?string
    {
        return $this->subject;
    }

    public function setContent(string $subject, ?string $content, ?JobStatus $status): void
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->status = $status;
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

    public function status(): ?JobStatus
    {
        $status = $this->call(endPoint: $this->id.'/status', updateSelf: true);

        return JobStatus::tryFromName($status->status);
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
