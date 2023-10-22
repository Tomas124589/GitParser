<?php

namespace ortom;

interface CommitMessage
{
    public function getTitle(): string;
    public function getTaskId(): ?int;
    public function getTags(): array;
    public function getDetails(): array;
    public function getBCBreaks(): array;
    public function getTodos(): array;
}
