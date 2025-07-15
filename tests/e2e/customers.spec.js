// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Customers Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the customers page before each test
    await page.goto('/admin/customers');
    // Verify we're on the customers page
    await expect(page).toHaveTitle(/Customers/);
  });

  test('should display customers list', async ({ page }) => {
    // Check if the customers table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toBeVisible();
  });

  test('should create a new customer', async ({ page }) => {
    // Click on the create new customer button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create customer form
    await expect(page).toHaveURL(/.*\/admin\/customers\/create/);
    
    // Fill in the form
    const customerName = `Test Customer ${Date.now()}`;
    await page.getByLabel('Name').fill(customerName);
    await page.getByLabel('Email').fill(`test${Date.now()}@example.com`);
    await page.getByLabel('Phone').fill('08123456789');
    await page.getByLabel('Address').fill('123 Test Street');
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the customers list
    await expect(page).toHaveURL(/.*\/admin\/customers/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the new customer appears in the table
    await expect(page.getByText(customerName)).toBeVisible();
  });

  test('should edit an existing customer', async ({ page }) => {
    // Click on the edit button for the first customer
    await page.locator('table tbody tr').first().getByRole('link', { name: 'Edit' }).click();
    
    // Verify we're on the edit form
    await expect(page).toHaveURL(/.*\/admin\/customers\/\d+\/edit/);
    
    // Update the customer name
    const updatedName = `Updated Customer ${Date.now()}`;
    await page.getByLabel('Name').fill(updatedName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the customers list
    await expect(page).toHaveURL(/.*\/admin\/customers/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the updated customer appears in the table
    await expect(page.getByText(updatedName)).toBeVisible();
  });

  test('should delete a customer', async ({ page }) => {
    // Get the name of the first customer for verification later
    const customerName = await page.locator('table tbody tr').first().locator('td').nth(1).textContent() || '';
    
    // Skip test if no customer name found
    if (!customerName) {
      test.skip();
      return;
    }
    
    // Click on the delete button for the first customer
    await page.locator('table tbody tr').first().getByRole('button', { name: 'Delete' }).click();
    
    // Confirm deletion in the modal
    await page.getByRole('button', { name: 'Yes, delete it!' }).click();
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the customer no longer appears in the table
    await expect(page.getByText(customerName)).not.toBeVisible();
  });
});
