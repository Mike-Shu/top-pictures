<b>{{ $app_name }}</b> ({{ $level_name }})
Env: {{ $app_env }}
[{{ $datetime->format('Y-m-d H:i:s') }}] {{ $app_env }}.{{ $level_name }} {{ $formatted }}
