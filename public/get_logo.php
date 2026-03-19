<?php
$url = 'https://chatgpt.com/backend-api/estuary/public_content/enc/eyJpZCI6Im1fNjliYjhlYWM3ODY4ODE5MWJmODY1MTVjNjc1OWQ2YTM6ZmlsZV8wMDAwMDAwMDY0OTQ3MjBiYThkNjYzZmM2NjU2OGJmZiIsInRzIjoiMjA1MzEiLCJwIjoicHlpIiwiY2lkIjoiMSIsInNpZyI6ImJkY2RmN2U2ODY2ZjYyMjI5ZTRlNmVmOGY4MzY0ZmViZDA4ZjkzODQyODk1NGJhOGVjMDIzNjY4MmQ5NTg5OTQiLCJ2IjoiMCIsImdpem1vX2lkIjpudWxsLCJjcyI6bnVsbCwiY2RuIjpudWxsLCJjcCI6bnVsbCwibWEiOm51bGx9';
$dest = __DIR__ . '/images/logo.png';
if (!is_dir(__DIR__ . '/images')) mkdir(__DIR__ . '/images', 0777, true);
$content = file_get_contents($url, false, stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]));
if ($content) {
    file_put_contents($dest, $content);
    echo "Success: Logo updated at $dest (" . strlen($content) . " bytes)";
} else {
    echo "Error: Failed to fetch from " . substr($url, 0, 50) . "...";
}
?>
