<?php

namespace ortom;

class MyCommitMessage implements CommitMessage
{

    public function __construct(private string $title, private ?int $taskId, private array $tags, private array $details, private array $bcBreaks, private array $todos)
    {
    }

    public function getTitle(): string
    {

        return $this->title;
    }

    public function getTaskId(): ?int
    {

        return $this->taskId;
    }

    public function getTags(): array
    {

        return $this->tags;
    }

    public function getDetails(): array
    {

        return $this->details;
    }

    public function getBCBreaks(): array
    {

        return $this->bcBreaks;
    }

    public function getTodos(): array
    {

        return $this->todos;
    }
}
