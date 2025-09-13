<form action="save_coupon.php" method="POST">
  <label>Coupon Code:</label>
  <input type="text" name="code" required><br><br>

  <label>Discount (%):</label>
  <input type="number" name="discount_percent" required><br><br>

  <label>Start Date:</label>
  <input type="date" name="start_date" required><br><br>

  <label>End Date:</label>
  <input type="date" name="end_date" required><br><br>

  <button type="submit">Add Coupon</button>
</form>
