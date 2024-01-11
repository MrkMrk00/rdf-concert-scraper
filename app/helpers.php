<?php

function find(string $pattern, ?string $basedir = null): array
{
    if (!$basedir) {
        $basedir = app_path();
    }

    $descriptorspec = [
        1 => ['pipe', 'w'],
    ];

    $handle = proc_open(['find', $basedir, '-name', $pattern], $descriptorspec, $pipes);
    $result = explode("\n", stream_get_contents($pipes[1]));

    fclose($pipes[1]);
    proc_close($handle);

    return array_filter($result);
}
