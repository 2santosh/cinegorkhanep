<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineGorkha Navigation</title>
    <style>
        * {
            margin: 0px;
        }

        body {
            font-family: verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding-top: 40px;
        }

        .header_main {
            width: 100%;
            background-color: #003344;
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .navbar ul li a {
            padding-left: 10px;
            padding-right: 10px;
            display: inline-block;
            font-size: 14px;
            text-decoration: none;
            color: #fff;
            transition-duration: 0.6s;
        }

        .navbar ul li a:hover {
            color: #17c3ba;
        }

        .navbar ul {
            font-family: verdana;
            margin-right: 20px;
            text-align: right;
            padding: 5px;
            list-style: none;
            /* Added to handle floating children */
            overflow: hidden; 
        }

        .navbar li {
            display: inline;
            padding: 6px;
        }

        .navbar li span {
            font-family: verdana;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            background-color: #003344;
            /* This is a crucial fix to display the dropdown correctly with the list items */
            float: right; 
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #003344;
            min-width: 10px;
            box-shadow: 0px 8px 10px 0px #003344;
            z-index: 1;
        }

        .dropdown-content a {
            font-family: verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: white;
            padding: 1px 1px;
            text-decoration: none;
            display: block;
            text-align: justify;
        }

        .dropdown-content a:hover {
            color: #17c3ba;
        }

        .show {
            display: block;
        }
    </style>
</head>
<body>

    <div class="header_main">
        <div class="navbar">
            <ul>
                <li><a href="./../index.php">Home</a></li>
                <li><a href="./../index.php#aboutus">About Us</a></li>
                <li><a href="./../index.php#contactus">Contact Us</a></li>
            </ul>
            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">Login</button>
                <div class="dropdown-content" id="dropdown">
                    <a href="pages_index.php">Admin</a>
                    <a href="./../staff/pages_staff_index.php">Staff</a>
                    <a href="./../client/pages_client_index.php">Client</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown");
            dropdown.classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

