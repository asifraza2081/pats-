# Test Result Declared!

Dear **{{ $name }}**,

The official results for the position of **{{ $jobTitle }}** (Project: *{{ $projectName }}*) have been declared. We are pleased to inform you that your result report is now available for review.

### Your Result Summary
<x-mail::panel>
- **Roll Number:** {{ $rollNo }}
- **Score:** {{ $score }} / {{ $totalMarks }}
- **Percentage:** {{ $percentage }}%
- **Percentile:** {{ $percentile }}
</x-mail::panel>

You can view your detailed performance report and download your digital result card by clicking the button below:

<x-mail::button :url="$url" color="primary">
View Detailed Result
</x-mail::button>

*If the button above does not work, copy and paste the following link into your browser:*
[{{ $url }}]({{ $url }})

Regards,  
**Operations Team**  
{{ config('app.name') }}

