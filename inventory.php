<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List - AUXILIARY</title>
    <link href="./css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
<body>
    <div class="sidebar">
        <div class="logo">AUXILIARY</div>
        <a href="#" class="sidebar-icon"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-basket-shopping"></i><span>Inventory</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-truck"></i><span>Suppliers</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-user-circle"></i><span>User Profile</span></a>
    </div>

    <div class="header-bar">
        <div class="header-left">
            <h1 style="margin: 0; font-size: 20px;">Inventory List</h1>
        </div>
        <div class="header-right">
            <i class="fas fa-bell icon"></i>
            <i class="fas fa-user-circle icon"></i>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <div class="main-content">
        <h2>Inventory List</h2>
        <div class="top-bar">
    <input type="text" id="searchInput" placeholder="Search Product Name or Item Number..." onkeyup="searchTable()">
    <div class="button-group">
        <button class="export-btn" onclick="exportTableToCSV()">Export</button>
        <button class="new-product-btn" onclick="openForm()">New Product</button>
    </div>
</div>
        <table id="inventoryTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Product Name</th>
                    <th onclick="sortTable(1)">Item Number</th>
                    <th onclick="sortTable(2)">Manufacturer</th>
                    <th onclick="sortTable(3)">Category</th>
                    <th onclick="sortTable(4)">Quantity</th>
                    <th onclick="sortTable(5)">Expiry Date</th>
                    <th onclick="sortTable(6)">Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="inventoryBody">
                <?php
                $conn = new mysqli("localhost", "root", "", "inventory_stock");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT * FROM inventory";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $status = strtolower($row['Status']);
                    $statusClass = $status === 'active' ? 'status-active' : ($status === 'low' ? 'status-low' : 'status-out');
                    echo "<tr>
                        <td>{$row['Product Name']}</td>
                        <td>{$row['Item Number']}</td>
                        <td>{$row['Manufacturer']}</td>
                        <td>{$row['Category']}</td>
                        <td>{$row['Quantity']}</td>
                        <td>{$row['Expiry']}</td>
                        <td><span class='$statusClass'>" . ucfirst($status) . "</span></td>
                        <td>
                            <button class='edit-btn' onclick='editRow(this)'>Edit</button>
                            <button class='delete-btn' onclick='deleteRow(this)'>Delete</button>
                        </td>
                    </tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="overlay" id="overlay" onclick="closeForm()"></div>
    <div class="form-popup" id="productForm">
    <span class="close-icon" onclick="closeForm()">&times;</span>
    <h3 id="formTitle">Add New Product</h3>
        <form id="productForm" action="action_page.php" method="POST">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="text" name="item_number" placeholder="Item Number" required>
            <input type="text" name="manufacturer" placeholder="Manufacturer" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="date" name="expiry" required>
            <select name="status" required>
                <option value="active">Active</option>
                <option value="low">Low</option>
                <option value="out">Out of Stock</option>
            </select>
            <button type="submit">Add Product</button>
            <button type="button" onclick="closeForm()">Cancel</button>
        </form>
    </div>

    <script>
        function closeForm() {
            document.getElementById('productForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('productForm').querySelectorAll('input, select').forEach(el => el.value = '');
        }

        function openForm() {
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function deleteRow(btn) {
            const row = btn.parentElement.parentElement;
            row.remove();
        }

        function editRow(btn) {
            alert("Edit feature is frontend-only and does not sync with database.");
        }

        function searchTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryBody tr');
            rows.forEach(row => {
                const itemNumber = row.children[1].textContent.toLowerCase();
                const manufacturer = row.children[2].textContent.toLowerCase();
                row.style.display = itemNumber.includes(input) || manufacturer.includes(input) ? '' : 'none';
            });
        }

        function exportTableToCSV() {
            let csv = '';
            const rows = document.querySelectorAll("table tr");
            for (let row of rows) {
                let cols = Array.from(row.querySelectorAll("td, th")).map(col => col.innerText);
                csv += cols.join(",") + "\n";
            }
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('download', 'inventory.csv');
            a.click();
        }

        function sortTable(n) {
            const table = document.getElementById("inventoryTable");
            let switching = true;
            let dir = "asc";
            let switchcount = 0;
            while (switching) {
                switching = false;
                let rows = table.rows;
                for (let i = 1; i < (rows.length - 1); i++) {
                    let shouldSwitch = false;
                    let x = rows[i].getElementsByTagName("TD")[n];
                    let y = rows[i + 1].getElementsByTagName("TD")[n];
                    let xText = x.textContent || x.innerText;
                    let yText = y.textContent || y.innerText;
                    if (!isNaN(xText) && !isNaN(yText)) {
                        xText = parseFloat(xText);
                        yText = parseFloat(yText);
                    }
                    if (dir == "asc") {
                        if (xText > yText) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (xText < yText) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount === 0 && dir === "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</body>
</html>