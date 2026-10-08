<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./output.css" rel="stylesheet">
  </head>
  <body>

    <main>
      <h1>Sign in</h1>

      <!-- action = where the form posts; method="post" keeps credentials out of the URL -->
      <form method="post">
        <div>
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
            autofocus>
        </div>

        <div>
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required>
        </div>

        <button type="submit">Log in</button>
      </form>
    </main>

  </body>
</html>