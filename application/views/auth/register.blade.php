<div>
	{{ Form::open('/auth/register', 'POST'); }}
		{{ Form::label('email', 'E-Mail:'); }}
		{{ Form::text('email') }}

		{{ Form::label('password', 'Password:'); }}
		{{ Form::text('password'); }}

		{{ Form::submit('Register'); }}
	{{  Form::close(); }}
</div>
