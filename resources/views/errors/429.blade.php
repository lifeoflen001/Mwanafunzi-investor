@include('errors._layout', [
    'title' => 'Too many requests',
    'eyebrow' => '429 / Take a short pause',
    'heading' => 'The process needs a moment.',
    'message' => 'There have been too many requests in a short period. Wait briefly, then try again.',
])
