# Test Result Declared!

Dear {{ $name }},

The official results for the position of **{{ $jobTitle }}** (Project: {{ $projectName }}) have been declared.

**Your Result Details:**
- **Roll Number:** {{ $rollNo }}
- **Score:** {{ $score }} / {{ $totalMarks }}
- **Percentage:** {{ $percentage }}%
- **Percentile:** {{ $percentile }}

<x-mail::button :url="$url">
Check on Portal
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

