<div>
	{{ Form::open('/auth/login', 'POST'); }}
		{{ Form::label('email', 'E-Mail:'); }}
		{{ Form::text('email') }}

		{{ Form::label('password', 'Password:'); }}
		{{ Form::text('password'); }}

		{{ Form::submit('Login'); }}
	{{  Form::close(); }}
</div>
