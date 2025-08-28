<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shakti Bhuvan - Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Ensure hidden sections are not visible */
    .hidden { display: none !important; }
    .section { display: none; }   /* Default hidden */
    .section.active { display: block; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>Shakti Bhuvan <span>Admin Panel</span></h2>
    <ul>
      <li class="menu-item active" data-section="dashboard">Dashboard</li>
      <li class="menu-item" data-section="rooms">Manage Rooms</li>
      <li class="menu-item" data-section="bookings">Bookings</li>
      <li class="menu-item" data-section="customers">Customers</li>
      <li class="menu-item" data-section="payments">Payments</li>
      <li class="menu-item" data-section="reviews">Reviews</li>
      <li class="menu-item" data-section="settings">Settings</li>
    </ul>
    <a href="admin_login.php" class="logout">⏻ Logout</a>
  </aside>

  <!-- Main Content -->
  <main class="main">
    <!-- Dashboard Section -->
    <div class="topbar">
      <input type="text" placeholder="Search bookings, rooms, customers...">
      <div class="icons">
        <span>🔔</span>
        <span>⚙️</span>
        <div class="profile">AD</div>
      </div>
    </div>
    <section id="dashboard" class="section active">

      <h1>Dashboard</h1>
      <p class="subtitle">Welcome back to Shakti Bhuvan admin panel</p>

      <div class="cards">
        <div class="card">
          <h3>Total Bookings</h3>
          <p class="value">1,234</p>
          <span class="note">+12% from last month</span>
        </div>
        <div class="card">
          <h3>Available Rooms</h3>
          <p class="value">24</p>
          <span class="note">Out of 50 total rooms</span>
        </div>
        <div class="card">
          <h3>Revenue This Month</h3>
          <p class="value">₹67,000</p>
          <span class="note">+8% from last month</span>
        </div>
        <div class="card">
          <h3>Occupancy Rate</h3>
          <p class="value">78%</p>
          <span class="note">+5% from last month</span>
        </div>
      </div>

      <div class="charts">
        <div class="chart-box">
          <h3>Monthly Revenue</h3>
          <canvas id="revenueChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>Booking Trends</h3>
          <canvas id="bookingChart"></canvas>
        </div>
      </div>
    </section>

    <!-- Manage Rooms Section -->
    <section id="rooms" class="section">
      <h1>Manage Rooms</h1>
      <p class="subtitle">Add, edit, and manage your room inventory</p>

      <div class="cards">
        <div class="card"><p class="value">28</p><h3>Total Rooms</h3></div>
        <div class="card"><p class="value" style="color:green;">20</p><h3>Available</h3></div>
        <div class="card"><p class="value">6</p><h3>Occupied</h3></div>
        <div class="card"><p class="value" style="color:red;">2</p><h3>Maintenance</h3></div>
      </div>

      <div class="table-box">
        <h3>Room Inventory</h3>
        <table>
          <tr>
            <th>Room ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Price/Night</th>
            <th>Capacity</th>
            <th>Occupancy</th>
            <th>Amenities</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
          <tr>
            <td>RM001</td>
            <td>Deluxe Suite</td>
            <td>Suite</td>
            <td>₹4,500</td>
            <td>4 guests</td>
            <td>0/5</td>
            <td>WiFi, AC, TV +1</td>
            <td><span class="status confirmed">Available</span></td>
            <td>...</td>
          </tr>
          <tr>
            <td>RM002</td>
            <td>Premium Room</td>
            <td>Premium</td>
            <td>₹3,200</td>
            <td>2 guests</td>
            <td>2/8</td>
            <td>WiFi, AC, TV +1</td>
            <td><span class="status confirmed">Available</span></td>
            <td>...</td>
          </tr>
          <tr>
            <td>RM003</td>
            <td>Standard Room</td>
            <td>Standard</td>
            <td>₹2,500</td>
            <td>2 guests</td>
            <td>5/12</td>
            <td>WiFi, TV</td>
            <td><span class="status pending">Maintenance</span></td>
            <td>...</td>
          </tr>
          <tr>
            <td>RM004</td>
            <td>Family Suite</td>
            <td>Suite</td>
            <td>₹5,200</td>
            <td>6 guests</td>
            <td>1/3</td>
            <td>WiFi, AC, TV +2</td>
            <td><span class="status confirmed">Available</span></td>
            <td>...</td>
          </tr>
        </table>
      </div>
    </section>

    <!-- Bookings Section -->
<section id="bookings" class="section">
  <h1>Bookings Management</h1>
  <p class="subtitle">Manage all customer bookings and reservations</p>

  <!-- Booking Stats -->
  <div class="cards">
    <div class="card">
      <p class="value">156</p>
      <h3>Total Bookings</h3>
    </div>
    <div class="card">
      <p class="value" style="color: green;">80</p>
      <h3>Confirmed</h3>
    </div>
    <div class="card">
      <p class="value" style="color: blue;">12</p>
      <h3>Checked In</h3>
    </div>
    <div class="card">
      <p class="value" style="color: orange;">8</p>
      <h3>Pending</h3>
    </div>
    <div class="card">
      <p class="value" style="color: red;">3</p>
      <h3>Cancelled</h3>
    </div>
  </div>

  <!-- Search + Filter -->
  <div class="filter-bar">
    <input type="text" placeholder="Search by booking ID, customer name, or room...">
    <select>
      <option>All Status</option>
      <option>Confirmed</option>
      <option>Checked In</option>
      <option>Pending</option>
      <option>Cancelled</option>
    </select>
    <button class="export-btn">⬇ Export Bookings</button>
  </div>

  <!-- Bookings Table -->
  <div class="table-box">
    <h3>All Bookings</h3>
    <table>
      <tr>
        <th>Booking ID</th>
        <th>Customer</th>
        <th>Room</th>
        <th>Dates</th>
        <th>Guests</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Actions</th>
      </tr>
      <tr>
        <td>BK001</td>
        <td>Rajesh Kumar<br><small>+91 9876543210</small></td>
        <td>Deluxe Suite</td>
        <td>2024-08-15 to 2024-08-18</td>
        <td>2</td>
        <td>₹13,500</td>
        <td><span class="status confirmed">Confirmed</span></td>
        <td><span class="payment paid">Paid</span></td>
        <td>...</td>
      </tr>
      <tr>
        <td>BK002</td>
        <td>Priya Sharma<br><small>+91 9876543211</small></td>
        <td>Standard Room</td>
        <td>2024-08-16 to 2024-08-17</td>
        <td>1</td>
        <td>₹2,500</td>
        <td><span class="status checked-in">Checked-in</span></td>
        <td><span class="payment partial">Partial</span></td>
        <td>...</td>
      </tr>
      <tr>
        <td>BK003</td>
        <td>Arjun Patel<br><small>+91 9876543212</small></td>
        <td>Premium Suite</td>
        <td>2024-08-17 to 2024-08-20</td>
        <td>4</td>
        <td>₹9,600</td>
        <td><span class="status pending">Pending</span></td>
        <td><span class="payment pending">Pending</span></td>
        <td>...</td>
      </tr>
      <tr>
        <td>BK004</td>
        <td>Sneha Reddy<br><small>+91 9876543213</small></td>
        <td>Deluxe Room</td>
        <td>2024-08-14 to 2024-08-16</td>
        <td>2</td>
        <td>₹7,000</td>
        <td><span class="status checked-out">Checked-out</span></td>
        <td><span class="payment paid">Paid</span></td>
        <td>...</td>
      </tr>
      <tr>
        <td>BK005</td>
        <td>Vikram Singh<br><small>+91 9876543214</small></td>
        <td>Standard Room</td>
        <td>2024-08-18 to 2024-08-21</td>
        <td>1</td>
        <td>₹7,500</td>
        <td><span class="status confirmed">Confirmed</span></td>
        <td><span class="payment paid">Paid</span></td>
        <td>...</td>
      </tr>
    </table>
  </div>
</section>


<!-- Customers Section -->
<!-- Customers Section -->
<section id="customers" class="section">
  <h1>Customer Management</h1>
  <p class="subtitle">Manage all customer information and relationships</p>

  <!-- Stats Cards -->
  <div class="cards">
    <div class="card">
      <p class="value">1,234</p>
      <h3>Total Customers</h3>
    </div>
    <div class="card">
      <p class="value" style="color: green;">987</p>
      <h3>Active Customers</h3>
    </div>
    <div class="card">
      <p class="value" style="color: orange;">56</p>
      <h3>VIP Customers</h3>
    </div>
    <div class="card">
      <p class="value">₹5.2L</p>
      <h3>Avg. Lifetime Value</h3>
    </div>
  </div>

  <!-- Search + Actions -->
  <div class="filter-bar">
    <input type="text" placeholder="Search customers by name, email, or phone number...">
    <div class="actions">
      <button class="export-btn">⬇ Export Data</button>
      <button class="add-btn">➕ Add Customer</button>
    </div>
  </div>

  <!-- Customers Table -->
  <div class="table-box">
    <h3>All Customers</h3>
    <table>
      <tr>
        <th>Customer ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Location</th>
        <th>Bookings</th>
        <th>Total Spent</th>
        <th>Rating</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>

      <tr>
        <td>CUST001</td>
        <td>Rajesh Kumar<br><small>Member since 2024-01-15</small></td>
        <td>rajesh@email.com<br><small>+91 9876543210</small></td>
        <td>Mumbai, Maharashtra</td>
        <td>8</td>
        <td>₹45,600</td>
        <td>⭐ 4.5</td>
        <td><span class="status active">ACTIVE</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>CUST002</td>
        <td>Priya Sharma<br><small>Member since 2024-02-20</small></td>
        <td>priya@email.com<br><small>+91 9876543211</small></td>
        <td>Delhi, India</td>
        <td>5</td>
        <td>₹28,900</td>
        <td>⭐ 4.8</td>
        <td><span class="status active">ACTIVE</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>CUST003</td>
        <td>Arjun Patel<br><small>Member since 2024-03-10</small></td>
        <td>arjun@email.com<br><small>+91 9876543212</small></td>
        <td>Ahmedabad, Gujarat</td>
        <td>3</td>
        <td>₹19,800</td>
        <td>⭐ 4.2</td>
        <td><span class="status active">ACTIVE</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>CUST004</td>
        <td>Sneha Reddy<br><small>Member since 2024-01-05</small></td>
        <td>sneha@email.com<br><small>+91 9876543213</small></td>
        <td>Hyderabad, Telangana</td>
        <td>12</td>
        <td>₹67,800</td>
        <td>⭐ 4.9</td>
        <td><span class="status vip">VIP</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>CUST005</td>
        <td>Vikram Singh<br><small>Member since 2024-04-22</small></td>
        <td>vikram@email.com<br><small>+91 9876543214</small></td>
        <td>Jaipur, Rajasthan</td>
        <td>2</td>
        <td>₹12,500</td>
        <td>⭐ 4.0</td>
        <td><span class="status active">ACTIVE</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>CUST006</td>
        <td>Anita Desai<br><small>Member since 2023-11-12</small></td>
        <td>anita@email.com<br><small>+91 9876543215</small></td>
        <td>Pune, Maharashtra</td>
        <td>0</td>
        <td>₹0</td>
        <td>No rating</td>
        <td><span class="status inactive">INACTIVE</span></td>
        <td>...</td>
      </tr>
    </table>
  </div>
</section>


<section id="payments" class="section">
  <h1>Payment Management</h1>
  <p class="subtitle">Track and manage all payment transactions</p>

  <!-- Stats Cards -->
  <div class="cards">
    <div class="card">
      <p class="value">₹8.2L</p>
      <small style="color:green;">+12% from last month</small>
      <h3>Total Revenue</h3>
    </div>
    <div class="card">
      <p class="value">1,456</p>
      <small style="color:green;">+8% from last month</small>
      <h3>Transactions</h3>
    </div>
    <div class="card">
      <p class="value">98.5%</p>
      <small style="color:green;">+0.5% from last month</small>
      <h3>Success Rate</h3>
    </div>
    <div class="card">
      <p class="value">₹563</p>
      <small style="color:red;">-2% from last month</small>
      <h3>Avg. Transaction</h3>
    </div>
  </div>

  <!-- Charts -->
  <!-- <div class="card">
      <h3>Monthly Revenue</h3>
      <canvas id="revenueChart"></canvas>
    </div>

    
    <div class="card">
      <h3>Payment Methods</h3>
      <canvas id="paymentChart"></canvas>
      <div class="legend-list">
        <div class="legend-item"><span class="legend-color" style="background:#6366f1"></span> UPI <span>45%</span></div>
        <div class="legend-item"><span class="legend-color" style="background:#22c55e"></span> Credit Card <span>30%</span></div>
        <div class="legend-item"><span class="legend-color" style="background:#fbbf24"></span> Debit Card <span>15%</span></div>
        <div class="legend-item"><span class="legend-color" style="background:#ef4444"></span> Bank Transfer <span>8%</span></div>
        <div class="legend-item"><span class="legend-color" style="background:#06b6d4"></span> Wallet <span>2%</span></div>
      </div>
    </div> 

     <div class="chart-container">
      <h3>Payment Methods</h3>
      <canvas id="methodsChart"></canvas>
      <ul class="legend">
        <li><span style="background:#6c63ff"></span> UPI</li>
        <li><span style="background:#4caf50"></span> Credit Card</li>
        <li><span style="background:#ff9800"></span> Debit Card</li>
        <li><span style="background:#e53935"></span> Bank Transfer</li>
        <li><span style="background:#009688"></span> Wallet</li>
      </ul>
    </div>
  </div> -->

  <!-- Search + Filter -->
  <div class="filter-bar">
    <input type="text" placeholder="Search by payment ID, booking ID, or customer name...">
    <select>
      <option>All Status</option>
      <option>Completed</option>
      <option>Pending</option>
      <option>Failed</option>
      <option>Refunded</option>
    </select>
    <button class="export-btn">⬇ Export Payments</button>
  </div>

  <!-- Payments Table -->
  <div class="table-box">
    <h3>All Payments</h3>
    <table>
      <tr>
        <th>Payment ID</th>
        <th>Booking ID</th>
        <th>Customer</th>
        <th>Amount</th>
        <th>Method</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>

      <tr>
        <td>PAY001</td>
        <td>BK001</td>
        <td>Rajesh Kumar</td>
        <td>₹3,500</td>
        <td>UPI</td>
        <td>2024-08-15</td>
        <td><span class="status completed">✔ Completed</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>PAY002</td>
        <td>BK002</td>
        <td>Priya Sharma</td>
        <td>₹1,250</td>
        <td>Credit Card</td>
        <td>2024-08-16</td>
        <td><span class="status completed">✔ Completed</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>PAY003</td>
        <td>BK003</td>
        <td>Arjun Patel</td>
        <td>₹6,600</td>
        <td>Bank Transfer</td>
        <td>2024-08-13</td>
        <td><span class="status pending">⏳ Pending</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>PAY004</td>
        <td>BK004</td>
        <td>Sneha Reddy</td>
        <td>₹1,800</td>
        <td>Debit Card</td>
        <td>2024-08-14</td>
        <td><span class="status completed">✔ Completed</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>PAY005</td>
        <td>BK005</td>
        <td>Vikram Singh</td>
        <td>₹7,500</td>
        <td>UPI</td>
        <td>2024-08-18</td>
        <td><span class="status failed">❌ Failed</span></td>
        <td>...</td>
      </tr>

      <tr>
        <td>PAY006</td>
        <td>BK006</td>
        <td>Anita Desai</td>
        <td>₹2,500</td>
        <td>Wallet</td>
        <td>2024-08-12</td>
        <td><span class="status refunded">↩ Refunded</span></td>
        <td>...</td>
      </tr>
    </table>
  </div>
</section>
<section id="reviews" class="section">
  <h1>Reviews Management</h1>
  <p class="subtitle">Monitor and respond to customer reviews</p>

  <!-- Stats Cards -->
  <div class="cards">
    <div class="card">
      <p class="value">3.8 ⭐</p>
      <h3>Average Rating</h3>
    </div>
    <div class="card">
      <p class="value">234</p>
      <h3>Total Reviews</h3>
    </div>
    <div class="card">
      <p class="value" style="color:green;">198</p>
      <h3>Published</h3>
    </div>
    <div class="card">
      <p class="value" style="color:orange;">8</p>
      <h3>Pending</h3>
    </div>
    <div class="card">
      <p class="value" style="color:red;">3</p>
      <h3>Flagged</h3>
    </div>
  </div>

  <!-- Search + Filter -->
  <div class="filter-bar">
    <input type="text" placeholder="Search reviews by customer name, title, or content...">
    <select>
      <option>All Ratings</option>
      <option>5 Stars</option>
      <option>4 Stars</option>
      <option>3 Stars</option>
      <option>2 Stars</option>
      <option>1 Star</option>
    </select>
  </div>

  <!-- Reviews Table -->
  <div class="table-box">
    <h3>All Reviews</h3>
    <table>
      <tr>
        <th>Review ID</th>
        <th>Customer</th>
        <th>Room</th>
        <th>Rating</th>
        <th>Title</th>
        <th>Date</th>
        <th>Status</th>
        <th>Helpful</th>
        <th>Actions</th>
      </tr>

      <tr>
        <td>REV001</td>
        <td>Rajesh Kumar <span class="verified">✔ Verified</span></td>
        <td>Deluxe Suite</td>
        <td>⭐⭐⭐⭐⭐ 5</td>
        <td>Exceptional Service and Comfort</td>
        <td>2024-08-18</td>
        <td><span class="status published">✔ Published</span></td>
        <td>👍 12</td>
        <td>...</td>
      </tr>

      <tr>
        <td>REV002</td>
        <td>Priya Sharma <span class="verified">✔ Verified</span></td>
        <td>Standard Room</td>
        <td>⭐⭐⭐⭐ 4</td>
        <td>Good Value for Money</td>
        <td>2024-08-17</td>
        <td><span class="status published">✔ Published</span></td>
        <td>👍 8</td>
        <td>...</td>
      </tr>

      <tr>
        <td>REV003</td>
        <td>Arjun Patel <span class="verified">✔ Verified</span></td>
        <td>Premium Suite</td>
        <td>⭐⭐⭐ 3</td>
        <td>Average Experience</td>
        <td>2024-08-20</td>
        <td><span class="status pending">⏳ Pending</span></td>
        <td>👍 3</td>
        <td>...</td>
      </tr>

      <tr>
        <td>REV004</td>
        <td>Sneha Reddy <span class="verified">✔ Verified</span></td>
        <td>Deluxe Room</td>
        <td>⭐⭐⭐⭐⭐ 5</td>
        <td>Perfect for Business Trip</td>
        <td>2024-08-16</td>
        <td><span class="status published">✔ Published</span></td>
        <td>👍 15</td>
        <td>...</td>
      </tr>

      <tr>
        <td>REV005</td>
        <td>Vikram Singh <span class="verified">✔ Verified</span></td>
        <td>Standard Room</td>
        <td>⭐⭐ 2</td>
        <td>Disappointing Stay</td>
        <td>2024-08-21</td>
        <td><span class="status flagged">❌ Flagged</span></td>
        <td>👍 2</td>
        <td>...</td>
      </tr>

      <tr>
        <td>REV006</td>
        <td>Anita Desai <span class="verified">✔ Verified</span></td>
        <td>Premium Room</td>
        <td>⭐⭐⭐⭐ 4</td>
        <td>Great Amenities</td>
        <td>2024-08-19</td>
        <td><span class="status published">✔ Published</span></td>
        <td>👍 6</td>
        <td>...</td>
      </tr>
    </table>
  </div>
</section>


<!-- setting section -->

<section id="settings" class="section">
  <h1>Settings</h1>
  <p class="subtitle">Configure your hotel management system</p>

  <button class="save-btn">💾 Save Changes</button>

  <div class="settings-grid">
    <!-- Hotel Information -->
    <div class="settings-card">
      <h3>🏨 Hotel Information</h3>
      <label>Hotel Name <input type="text" value="Shakti Bhuvan"></label>
      <label>Email <input type="email" value="info@shaktibhuvan.com"></label>
      <label>Phone <input type="text" value="+91 98765 43210"></label>
      <label>Address <input type="text" value="123 Heritage Street, Mumbai, Maharashtra 400001"></label>
      <label>Website <input type="text" value="www.shaktibhuvan.com"></label>
      <label>Description <textarea>A premium boutique hotel offering luxury accommodation with traditional Indian hospitality.</textarea></label>
    </div>

    <!-- Booking Settings -->
    <div class="settings-card">
      <h3>📅 Booking Settings</h3>
      <label>Max Advance Booking (days) <input type="number" value="365"></label>
      <label>Min Advance Booking (days) <input type="number" value="1"></label>
      <label>Check-in Time <input type="time" value="14:00"></label>
      <label>Check-out Time <input type="time" value="11:00"></label>
      <label>Cancellation Deadline (hours) <input type="number" value="24"></label>
    </div>

    <!-- Notification Settings -->
    <div class="settings-card">
      <h3>🔔 Notification Settings</h3>
      <label><input type="checkbox" checked> Email Notifications</label>
      <label><input type="checkbox" checked> SMS Notifications</label>
      <label><input type="checkbox" checked> Booking Alerts</label>
      <label><input type="checkbox" checked> Payment Alerts</label>
      <label><input type="checkbox" checked> Review Alerts</label>
    </div>

    <!-- Payment Settings -->
    <div class="settings-card">
      <h3>💳 Payment Settings</h3>
      <label>Currency 
        <select>
          <option>INR (Indian Rupee)</option>
          <option>USD (US Dollar)</option>
          <option>EUR (Euro)</option>
        </select>
      </label>
      <label>Tax Rate (%) <input type="number" value="18"></label>
      <label>Service Fee (%) <input type="number" value="5"></label>
      <label>Cancellation Fee (%) <input type="number" value="10"></label>
    </div>

    <!-- Security Settings -->
    <div class="settings-card">
      <h3>🔒 Security Settings</h3>
      <label><input type="checkbox" checked> Two-Factor Authentication</label>
      <label>Session Timeout (minutes) <input type="number" value="30"></label>
      <label>Password Expiry (days) <input type="number" value="90"></label>
    </div>

    <!-- Display Settings -->
    <div class="settings-card">
      <h3>🖥 Display Settings</h3>
      <label>Rooms Per Page <input type="number" value="12"></label>
      <label>Reviews Per Page <input type="number" value="10"></label>
      <label>Default Language 
        <select>
          <option>English</option>
          <option>Hindi</option>
          <option>French</option>
        </select>
      </label>
    </div>
  </div>
</section>



  </main>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
  // Sidebar navigation handling
  document.querySelectorAll(".menu-item").forEach(item => {
    item.addEventListener("click", function() {
      // Remove active class from menu items
      document.querySelectorAll(".menu-item").forEach(i => i.classList.remove("active"));
      this.classList.add("active");

      // Hide all sections
      document.querySelectorAll(".section").forEach(sec => {
        sec.classList.remove("active");
        sec.classList.add("hidden");
      });

      // Show the selected section
      let section = this.dataset.section;
      let activeSection = document.getElementById(section);
      activeSection.classList.remove("hidden");
      activeSection.classList.add("active");
    });
  });

  // Charts
  new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
      labels: ["Jan","Feb","Mar","Apr","May","Jun"],
      datasets: [{
        label: "Revenue",
        data: [45000, 50000, 47000, 60000, 58000, 65000],
        backgroundColor: "#e6b450"
      }]
    }
  });

  new Chart(document.getElementById('bookingChart'), {
    type: 'line',
    data: {
      labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
      datasets: [{
        label: "Bookings",
        data: [10,18,14,20,25,32,28],
        borderColor: "#e6b450",
        fill: false
      }]
    }
  });

  new Chart(document.getElementById("revenueChart"), {
      type: "bar",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [{
          data: [450000, 510000, 480000, 610000, 580000, 670000],
          backgroundColor: "#f4c361",
          borderRadius: 10, // rounded bars
          barThickness: 45
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: "#5c4a32" }
          },
          y: {
            ticks: { color: "#5c4a32" },
            grid: { color: "rgba(0,0,0,0.05)" }
          }
        }
      }
    });

    // Payment Methods Chart
    new Chart(document.getElementById("paymentChart"), {
      type: "doughnut",
      data: {
        labels: ["UPI", "Credit Card", "Debit Card", "Bank Transfer", "Wallet"],
        datasets: [{
          data: [45, 30, 15, 8, 2],
          backgroundColor: ["#6366f1", "#22c55e", "#fbbf24", "#ef4444", "#06b6d4"],
          borderWidth: 2,
          cutout: "65%"
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });

  </script>
</body>
</html>
