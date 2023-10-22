<?php

namespace ortom;

interface CommitMessageParser
{
    public function parse(string $message): CommitMessage;
}
