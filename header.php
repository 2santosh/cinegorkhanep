<link rel="stylesheet" type="text/css" href="css/header.css">
<div class="header_main">
    <div class="navBar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#aboutus">About Us</a></li>
            <li><a href="index.php#contactus">Contact Us</a></li>
            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">Login</button>
                <div class="dropdown-content" id="dropdown">
                    <a href="admin/pages_index.php">Admin</a>
                    <a href="staff/pages_staff_index.php">Staff</a>
                    <a href="client/pages_client_index.php">Client</a>
                </div>
            </div>
        </ul>
    </div>

</div>
<a href="index.php">
    <div class="logo-name">
        <div class="logo">
            <img class="logo_img" src="img/logo.jpg">
        </div>
        <div class="name">
            <h5>SBS Bank</h5>
            <h6>First Bank in Nepal</h6>
        </div>
    </div>
</a>
</div>

<div class="dif_banking">
    <div class="retail_banking">
        <a href="#">Deposit</a>
    </div>
    <div class="corporate_banking">
        <a href="#">Loan</a>
    </div>
    <div class="international_banking">
        <a href="#">Remittance</a>
    </div>
    <div class="international_banking">
        <a href="#">Internet or Mobile Banking</a>
    </div>

    <div class="bank_servic">
        <a href="index.php#iservices">iServices</a>
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
.dropdown {
    position: relative;
    display: inline-block;
    background-color: none;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: none;
    min-width: 10px;
    box-shadow: 0px 7px 10px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    font-family: verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: white;
    padding: 1px 1px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    color:  #17c3ba;
}

.show {
    display: block;
}

</style>
