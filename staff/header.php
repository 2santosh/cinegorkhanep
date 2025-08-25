<div class="header_main">
    <div class="navbar">
        <ul>
            <li><a href="./../index.php">Home</a></li>
            <li><a href="./../index.php#aboutus">About Us</a></li>
            <li><a href="./../index.php#contactus">Contact Us</a></li>
            <!-- Move the dropdown div outside of the ul element -->
            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">Login</button>
                <div class="dropdown-content" id="dropdown">
                    <a href="./../admin/pages_index.php">Admin</a>
                    <a href="pages_staff_index.php">Staff</a>
                    <a href="./../client/pages_client_index.php">Client</a>
                </div>
            </div>
        </ul>
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


<style>
* {
    margin: 0px;
}

body {
    font-family: verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
    padding-top: 40px; /* Added padding-top to account for the navbar height */
}

.header_main {
    width: 100%;
    background-color: #003344;
    position: fixed; /* Fixed position to stick to the top */
    top: 0; /* Display at the top of the viewport */
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
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color:#003344 ;
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
    color:  #17c3ba;
}

.show {
    display: block;
}


</style>
