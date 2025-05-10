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
            <tbody id="inventoryBody"></tbody>
        </table>
    </div>

    <div class="overlay" id="overlay" onclick="closeForm()"></div>
    <div class="form-popup" id="productForm">
    <span class="close-icon" onclick="closeForm()">&times;</span>
    <h3 id="formTitle">Add New Product</h3>
        <input type="text" id="productName" placeholder="Product Name">
        <input type="text" id="itemNumber" placeholder="Item Number">
        <input type="text" id="manufacturer" placeholder="Manufacturer">
        <input type="text" id="category" placeholder="Category">
        <input type="number" id="quantity" placeholder="Quantity">
        <input type="date" id="expiryDate">
        <select id="status" placeholder="Status">
            <option value="active">Active</option>
            <option value="low">Low</option>
            <option value="out">Out of Stock</option>
        </select>
        <button id="formSubmitBtn" onclick="addProduct()">Add Product</button>
        <button onclick="closeForm()">Cancel</button>
    </div>

    <script>
        let editingRow = null;

        function openForm(row = null) {
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            if (row) {
                editingRow = row;
                document.getElementById('formTitle').innerText = 'Edit Product';
                document.getElementById('formSubmitBtn').innerText = 'Update Product';

                const cells = row.children;
                document.getElementById('productName').value = cells[0].innerText;
                document.getElementById('itemNumber').value = cells[1].innerText;
                document.getElementById('manufacturer').value = cells[2].innerText;
                document.getElementById('category').value = cells[3].innerText;
                document.getElementById('quantity').value = cells[4].innerText;
                document.getElementById('expiryDate').value = cells[5].innerText;
                document.getElementById('status').value = cells[6].innerText.toLowerCase();
            }
        }
        
        function closeForm() {
            editingRow = null;
            document.getElementById('productForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('formTitle').innerText = 'Add New Product';
            document.getElementById('formSubmitBtn').innerText = 'Add Product';
            document.getElementById('productForm').querySelectorAll('input, select').forEach(el => el.value = '');
        }

        function deleteRow(btn) {
            const row = btn.parentElement.parentElement;
            row.remove();
        }

        function editRow(btn) {
            const row = btn.parentElement.parentElement;
            openForm(row);
        }

        function addProduct() {
            const name = document.getElementById('productName').value;
            const item = document.getElementById('itemNumber').value;
            const manu = document.getElementById('manufacturer').value;
            const cat = document.getElementById('category').value;
            const qty = document.getElementById('quantity').value;
            const exp = document.getElementById('expiryDate').value;
            const stat = document.getElementById('status').value;
            if (!name || !item || !manu || !cat || !qty || !exp || !stat) return alert("All fields required");

            let statusClass = stat === 'active' ? 'status-active' : (stat === 'low' ? 'status-low' : 'status-out');
            const newRowHtml = `<td>${name}</td><td>${item}</td><td>${manu}</td><td>${cat}</td><td>${qty}</td><td>${exp}</td><td><span class="${statusClass}">${stat.charAt(0).toUpperCase() + stat.slice(1)}</span></td><td><button class="edit-btn" onclick="editRow(this)">Edit</button><button class="delete-btn" onclick="deleteRow(this)">Delete</button></td>`;

            if (editingRow) {
                editingRow.innerHTML = newRowHtml;
                editingRow = null;
            } else {
                const row = document.createElement('tr');
                row.innerHTML = newRowHtml;
                document.getElementById('inventoryBody').appendChild(row);
            }
            closeForm();
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

        const defaultProducts = [
            ['Matcha Milk Tea','MT001','GreenLeaf Co.','Beverage',35,'2025-12-15','active'],
            ['Taro Milk Tea','MT002','BrewMasters Co.','Beverage',20,'2025-11-29','low'],
            ['Brown Sugar Boba','BB001','SweetPearls Inc.','Toppings',100,'2025-10-10','active'],
            ['Tapioca Pearls','TP001','SweetPearls Inc.','Toppings',0,'2025-09-25','out'],
            ['Wintermelon Syrup','SY001','SweetNectar Co.','Syrup',15,'2025-12-05','low'],
            ['Large Plastic Cups','CP001','PackIt Co.','Packaging',200,'2027-01-01','active'],
            ['Sealing Film Roll','PK001','PackIt Co.','Packaging',20,'2026-06-30','active']
        ];
        window.onload = () => {
            defaultProducts.forEach(p => {
                const row = document.createElement('tr');
                let statusClass = p[6] === 'active' ? 'status-active' : (p[6] === 'low' ? 'status-low' : 'status-out');
                row.innerHTML = `<td>${p[0]}</td><td>${p[1]}</td><td>${p[2]}</td><td>${p[3]}</td><td>${p[4]}</td><td>${p[5]}</td><td><span class="${statusClass}">${p[6].charAt(0).toUpperCase() + p[6].slice(1)}</span></td><td><button class="edit-btn" onclick="editRow(this)">Edit</button><button class="delete-btn" onclick="deleteRow(this)">Delete</button></td>`;
                document.getElementById('inventoryBody').appendChild(row);
            });
        };
    </script>
</body>
</html>