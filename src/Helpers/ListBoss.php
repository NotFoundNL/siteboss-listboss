<?php

//declare(strict_types=1);

namespace NotFound\ListBoss\Helpers;

class ListBoss
{
    use Api;

    /**
     * lists all jobs for the current user
     *
     * @return array<Job>
     */
    public function list(): array
    {
        $jobs = [];
        $jobCall = $this->call(method: 'GET', endPoint: '/');
        if (isset($jobCall->jobs)) {
            foreach ($jobCall->jobs as $job) {
                $jobObject = new Job($job->id, subject: $job->subject, status: JobStatus::tryFromName($job->status));

                $jobs[] = $jobObject;
            }
        }

        return $jobs;
    }

    public function recipient(int $list, int $recipient): object
    {
        return $this->call(method: 'GET', endPoint: $list.'/recipient/'.$recipient);
    }
}
