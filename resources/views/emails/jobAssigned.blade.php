@component('mail::message')
    # New Job Asssigned to You

    Hi {{ $employee }},

    Job #{{ $job_id }} was just assigned to you.

    Job Title: {{ $job_title }}

    Thanks,
    {{ config('app.name') }}

@endcomponent
