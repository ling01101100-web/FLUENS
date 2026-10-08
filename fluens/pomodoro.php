<!--
+html5-boilerplate
+<link href="./output.css" rel="stylesheet">
+<body>...</body>
+<script src="" async defer></script>
+src="./pomodoro.js"
- class="..."
-->
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
      <h1>Pomodoro</h1>
      <div id="mode">Focus</div>
      <div id="time">25:00</div>
      <div>
        <button id="start">Start</button>
        <button id="reset">Reset</button>
        <button id="skip">Skip</button>
      </div>
      <div id="sessions">Completed: 0</div>
    </main>

    <script src="./pomodoro.js" async defer></script>
  </body>
</html>