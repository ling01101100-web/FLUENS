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

    <div>
      <h1>Create an account</h1>

      <form method="post">
        <div>
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Choose a username"
            autocomplete="username"
            minlength="3"
            maxlength="30"
            pattern="[A-Za-z0-9_]+"
            title="Letters, numbers and underscores only"
            required>
          <span>3–30 characters: letters, numbers, underscore.</span>
        </div>

        <div>
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Create a password"
            autocomplete="new-password"
            minlength="8"
            required>
          <span>At least 8 characters.</span>
        </div>

        <div>
          <label for="confirm">Confirm password</label>
          <input
            type="password"
            id="confirm"
            name="confirm"
            placeholder="Repeat your password"
            autocomplete="new-password"
            minlength="8"
            required>
        </div>

        <button type="submit">Sign up</button>
      </form>
    </div>

  </body>
</html>