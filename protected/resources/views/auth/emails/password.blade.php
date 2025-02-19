{{-- resources/views/emails/password.blade.php --}}

@lang('message.passwordclickreset') <a href="{{ url('password/reset/'.$token) }}">{{ url('password/reset/'.$token) }}</a>
