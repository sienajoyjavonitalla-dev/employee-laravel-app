@component('mail::message')

    # Job #{{ $job_id }} Cancelled

    Hi {{ $employee }},

    The job that was assigned to you was cancelled.

    Thanks,
    {{ config('app.name') }}

@endcomponent