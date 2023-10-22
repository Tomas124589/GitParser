<?php

namespace ortom;

use RuntimeException;

class MyCommitMessageParser implements CommitMessageParser
{
    public function parse(string $message): CommitMessage
    {

        $message = trim($message);

        if (!$message)
            throw new RuntimeException("Message is empty.");

        [$header, $body] = explode(PHP_EOL, $message, 2);

        $headerMatches = null;
        $bodyMatches = null;

        if (!preg_match('/(?:\[(.*?)\])*\s*@(\w+)\s*(?:#(\d+))?\s*(.*)/', $header, $headerMatches))
            throw new RuntimeException("Message header matching failed.");

        if (mb_strlen($header) != mb_strlen($headerMatches[0]))
            throw new RuntimeException('Header is malformed.');

        if (!preg_match('/\s*((?:\*\s*.*\s*)*)((?:BC:\s*.*\s*)*)((?:Feature:\s*.*\s*)*)((?:TODO:.*\s*)*)/', $body, $bodyMatches))
            throw new RuntimeException("Message body matching failed.");

        if (mb_strlen($body) != mb_strlen($bodyMatches[0]))
            throw new RuntimeException('Body is malformed.');

        $bodyMatches = array_map('trim', $bodyMatches);

        $tagsString = $headerMatches[1];
        $taskIdString = $headerMatches[3];
        $titleString = $headerMatches[4];
        $detailsString = $bodyMatches[1];
        $bcBreaksString = $bodyMatches[2];
        $todosString = $bodyMatches[4];

        if (!$titleString)
            throw new RuntimeException('Title is empty.');

        $tags = $tagsString ? $this->parseTags($tagsString) : [];
        $details = $detailsString ? $this->parseDetails($detailsString) : [];
        $bcBreaks = $bcBreaksString ? $this->parseBcBreaks($bcBreaksString) : [];
        $todos = $todosString ? $this->parseTodos($todosString) : [];

        return new MyCommitMessage($titleString, $taskIdString ? (int)$taskIdString : null, $tags, $details, $bcBreaks, $todos);
    }

    private function parseTags(string $tags): array
    {

        $tags = str_replace(["[", "]"], "", $tags);
        $tags = preg_replace('/\s\s+/', " ", $tags);

        $result = explode(" ", $tags);

        return $result;
    }

    private function parseDetails(string $details): array
    {

        $details = preg_replace('/^\s*(\*\s*)/m', '', $details);

        $result = explode(PHP_EOL, $details);

        return $result;
    }

    private function parseBcBreaks(string $bcBreaks): array
    {

        $bcBreaks = preg_replace('/^\s*(BC:\s*)/m', '', $bcBreaks);

        $result = explode(PHP_EOL, $bcBreaks);

        return $result;
    }

    private function parseTodos(string $todos): array
    {

        $todos = preg_replace('/^\s*(TODO:\s*)/m', '', $todos);

        $result = explode(PHP_EOL, $todos);

        return $result;
    }
}
