<htmL>
<head>
    <script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-with-polyfills-latest.js"></script>
</head>
<body>
	<h1>Auth</h1>
	<script>
		window.YaAuthSuggest.init(
			{
			  client_id: "f59c65bae202418c8ce57e03fbff2e0e",
			  response_type: "token",
			  redirect_uri: "https://for16.ru/scripts/tracker.php"
			},
			"https://for16.ru",
			{ view: "default" }
		  )
		  .then(({handler}) => handler())
		  .then(data => console.log('Сообщение с токеном', data))
		  .catch(error => console.log('Обработка ошибки', error))
	</script>
</body>
</htmL>
