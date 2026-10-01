<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Create an account</h1>

        <form method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
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
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <span class="text-gray-500 text-xs">3–30 characters: letters, numbers, underscore.</span>
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <span class="text-gray-500 text-xs">At least 8 characters.</span>
            </div>

            <div>
                <label for="confirm" class="block text-gray-700 font-medium mb-1">Confirm password</label>
                <input
                    type="password"
                    id="confirm"
                    name="confirm"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600">Sign up</button>
        </form>
    </div>

</body>
</html>