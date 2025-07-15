// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Transactions Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the transactions page before each test
    await page.goto('/admin/transactions');
    // Verify we're on the transactions page
    await expect(page).toHaveTitle(/Transactions/);
  });

  test('should display transactions list', async ({ page }) => {
    // Check if the transactions table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toBeVisible();
  });

  test('should create a new transaction', async ({ page }) => {
    // Click on the create new transaction button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create transaction form
    await expect(page).toHaveURL(/.*\/admin\/transactions\/create/);
    
    // Fill in the form
    const transactionDate = new Date().toISOString().split('T')[0]; // Today's date in YYYY-MM-DD format
    await page.getByLabel('Date').fill(transactionDate);
    
    // Select a customer (assuming there's at least one)
    await page.locator('select[name="customer_id"]').selectOption({ index: 1 });
    
    // Add a product to the transaction
    await page.getByRole('button', { name: /Add Item/ }).click();
    
    // Select a product for the first item
    await page.locator('.transaction-items').first().locator('select[name="transaction_items[0][product_id]"]').selectOption({ index: 1 });
    
    // Set quantity for the first item
    await page.locator('.transaction-items').first().locator('input[name="transaction_items[0][quantity]"]').fill('2');
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the transactions list
    await expect(page).toHaveURL(/.*\/admin\/transactions/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
  });

  test('should view transaction details', async ({ page }) => {
    // Click on the view button for the first transaction
    await page.locator('table tbody tr').first().getByRole('link', { name: 'View' }).click();
    
    // Verify we're on the transaction details page
    await expect(page).toHaveURL(/.*\/admin\/transactions\/\d+/);
    
    // Check if transaction details are displayed
    await expect(page.locator('.transaction-details')).toBeVisible();
    
    // Check if transaction items table is displayed
    await expect(page.locator('.transaction-items')).toBeVisible();
  });

  test('should generate transaction invoice', async ({ page }) => {
    // Click on the view button for the first transaction
    await page.locator('table tbody tr').first().getByRole('link', { name: 'View' }).click();
    
    // Click on the print invoice button
    await page.getByRole('link', { name: /Print Invoice/ }).click();
    
    // Wait for the invoice to open in a new tab
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('button', { name: /Download PDF/ }).click();
    const download = await downloadPromise;
    
    // Verify the download started
    expect(download.suggestedFilename()).toContain('invoice');
  });
});
